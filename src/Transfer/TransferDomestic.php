<?php
/**
 * Created by PhpStorm.
 * User: jking
 * Date: 3/19/19
 * Time: 9:55 PM
 */

namespace App\Transfer;

use App\Entity\Transfer;
use App\Entity\User;
use App\Utils\BankAccountUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

class TransferDomestic extends AbstractTransferGenerator
{
    private $transferStatus = 'failed';
    private $transferStatusMessage;
    private $transferAmount;
    private $failCodeCount;
    private $em;
    private $container;
    private $user;
    const TIME_TO_INPUT_CODE = 100;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating, EntityManagerInterface $em, ContainerInterface $container, Security $security)
    {
        parent::__construct($mailer, $templating, $em, $security);
        $this->em = $em;
        $this->container = $container;
        $this->user = $security->getUser();
    }

    /**
     * @param FormInterface $form
     */
    public function validateTransferForm($form)
    {
        $transferData = $form->getData();

        $transfer = new Transfer();

        $senderAccountNumber = $form->get('senderAccountNumber')->getData();

        $transfer
            ->setUser($this->getUser())
            ->setReceiverName($transferData->getReceiverName())
            ->setReceiverAccountNumber($transferData->getReceiverAccountNumber())
            ->setAmount($transferData->getAmount())
            ->setTitle($transferData->getTitle())
            ->setSenderAccountNumber($senderAccountNumber->getAccountNumber());

        $bankAccount = $this->setBankAccounts($transfer);
        $receiver = (object)$bankAccount['receiver'];
        $sender = (object)$bankAccount['sender'];

        // Full Validation of Transfer
        if (!empty($bankAccount['receiver'])) {
            $receiverAccountNumber = $receiver->getAccountNumber();
            $senderAccountNumber = $sender->getAccountNumber();
            if ($receiverAccountNumber != $senderAccountNumber) {
                $senderAvailableFunds = $sender->getAvailableFunds();
                $sendingAmount = $transfer->getAmount();
                if ($senderAvailableFunds >= $sendingAmount) {
                    if ($sendingAmount > 0.00) {
                        $this->setTransferStatus('to_verify_code');
                        $this->sendVerificationCode($this->getUser()->getEmail());
                        $transfer->setTransferHash($this->setHash());
                        $transfer->setVerificationCode($this->getVerificationCode());
                        $transfer->setStatus('To code verification');
                    } else {
                        $transfer->setStatus('Amount of transfer must be higher than zero');
                        $this->setTransferStatusMessage(3);;
                    }
                } else {
                    $transfer->setStatus('Not enough funds');
                    $this->setTransferStatusMessage(2);
                }
            } else {
                $transfer->setStatus('Cant transfer to the same acc number');
                $this->setTransferStatusMessage(1);
            }
        } else {
            $transfer->setStatus('Receiver account not exists');
            $this->setTransferStatusMessage(0);;
        }

        $this->em->persist($transfer);
        $this->em->flush();
    }

    /**
     * @var Transfer $transfer
     */
    public function sendTransfer($transfer)
    {
        $this->transferAmount = $transfer->getAmount();
        $bankAccount = $this->setBankAccounts($transfer);
        $sender = (object)$bankAccount['sender'];
        $senderAvailableFunds = $sender->getAvailableFunds();
        $sendingAmount = $transfer->getAmount();

        $transfer->setSenderFundsAfterTransfer($senderAvailableFunds - $sendingAmount);
        $transfer->setStatus('Transfer send to finalize');
        $this->setTransferStatus('to_finalize');
        $this->setTransferStatusMessage(4);

        $sender->setAvailableFunds($senderAvailableFunds - $sendingAmount);

        $this->em->persist($transfer);
        $this->em->flush();
    }

    /**
     * @var Transfer $transfer
     */
    public function incrementFailCode($transfer)
    {
        $this->failCodeCount = $transfer->getFailCodeCount();
        $transfer->setFailCodeCount($this->failCodeCount + 1);
        $this->setTransferStatusMessage(5);
        $this->em->persist($transfer);
        $this->em->flush();
    }

    /**
     * @var User $user
     */
    public function blockUser($user)
    {
        $user->setIsBlockedTransfers(true);
        $this->em->flush();
    }

    /**
     * @var Transfer $transfer
     * @return JsonResponse
     * @throws \Exception
     */
    public function setTimeToInputCode($transfer)
    {
        if ($transfer) {
            $date = $transfer->getCreatedAt();
            $now = new \DateTime('now');
            $roznica = date_diff($date, $now);
            $secondsLeft = $roznica->days * 24 * 60;
            $secondsLeft += $roznica->h * 60;
            $secondsLeft += $roznica->i * 60;
            $secondsLeft += $roznica->s;
            return new JsonResponse($secondsLeft);
        }
    }

    /**
     * @var Transfer $transfer
     * @return boolean
     * @throws \Exception
     */
    public function isCodeTimeExpired($transfer)
    {
        $passedTime = $this->setTimeToInputCode($transfer);

        if ($passedTime->getContent() > self::TIME_TO_INPUT_CODE) {
            $this->setTransferStatusMessage(6);
            $this->container->get('session')->clear('transferHash');
            $transfer->setStatus('Code time expired');
            $this->em->flush();
            return true;
        }
    }

    /**
     * @var Transfer $transfer
     * @param $transferVerifictionCode
     * @param $userInputCode
     * @return mixed
     */
    public function validateVerificationCode($transfer, $transferVerifictionCode, $userInputCode)
    {
        $validationCode = [];

        if ($userInputCode === $transferVerifictionCode) {
            $this->sendTransfer($transfer);
            $this->container->get('session')->clear('transferHash');
            $validationCode['status'] = 'to_finalize';
            $validationCode['message'] = $this->getTransferStatusMessage();
        } else {
            if ($transfer->getFailCodeCount() >= 2) {
                $this->container->get('session')->clear('transferHash');
                $this->blockUser($this->getUser());
                $transfer->setStatus('Too much wrong codes, user blocked');
                $this->em->flush();
                $validationCode['status'] = 'block_user';
            }
            $this->incrementFailCode($transfer);
            $validationCode['status'] = 'wrong_code';
            $validationCode['message'] = $this->getTransferStatusMessage();
        }
        return $validationCode;
    }

    public function setTransferStatusMessage(int $code)
    {
        switch ($code) {
            case 0:
                $this->transferStatusMessage = 'Podany numer rachunku odbiorcy nie istnieje';
                break;
            case 1:
                $this->transferStatusMessage = 'Nie możesz wykonać przelewu na ten sam numer rachunku';
                break;
            case 2:
                $this->transferStatusMessage = 'Nie masz wystarczających środków na koncie';
                break;
            case 3:
                $this->transferStatusMessage = 'Kwota przelewu musi być większa niż 0,00 PLN';
                break;
            case 4:
                $this->transferStatusMessage = 'Przelew na kwotę ' . BankAccountUtils::renderAmount($this->transferAmount) .
                    ' PLN został przyjęty do realizacji';
                break;
            case 5:
                $this->transferStatusMessage = 'Niepoprawny kod, pozostało prób: ' . (2 - $this->failCodeCount);
                break;
            case 6:
                $this->transferStatusMessage = 'Przelew został anulowany z powodu nie wpisania kodu weryfikacyjnego
                    przez dłużej niż ' . self::TIME_TO_INPUT_CODE . ' sekund';
                break;
            case 7:
                $this->transferStatusMessage = 'Brak transferu do finalizacji';
                break;
            default:
                $this->transferStatusMessage = 'Wystąpił nieznany błąd, prosimy o kontakt z supportem';
        }
    }

    public function getTransferStatusMessage()
    {
        return $this->transferStatusMessage;
    }

    public function getTransferStatus()
    {
        return $this->transferStatus;
    }

    public function setTransferStatus($transferStatus): void
    {
        $this->transferStatus = $transferStatus;
    }
}