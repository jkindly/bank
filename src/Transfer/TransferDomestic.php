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
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class TransferDomestic extends AbstractTransferGenerator
{
    private $transferStatus = 'failed';
    private $transferStatusMessage;
    private $transferAmount;
    private $failCodeCount;
    private $em;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating, EntityManagerInterface $em)
    {
        parent::__construct($mailer, $templating, $em);
        $this->em = $em;
    }

    /**
     * @param \App\Entity\User $user
     * @param FormInterface $form
     */
    public function validateTransfer($form, $user)
    {
        $transferData = $form->getData();

        $transfer = new Transfer();

        $senderAccountNumber = $form->get('senderAccountNumber')->getData();

        $transfer
            ->setUser($user)
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
                        $this->sendVerificationCode($user->getEmail());
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
        $date = $transfer->getCreatedAt();
        $now = new \DateTime('now');
        $roznica = date_diff($date, $now);
        $secondsLeft = $roznica->days * 24 * 60;
        $secondsLeft += $roznica->h * 60;
        $secondsLeft += $roznica->i * 60;
        $secondsLeft += $roznica->s;
        return new JsonResponse($secondsLeft);
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