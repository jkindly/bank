<?php
/**
 * Created by PhpStorm.
 * User: jking
 * Date: 3/19/19
 * Time: 9:55 PM
 */

namespace App\Transfer;


use App\Entity\BankAccount;
use Doctrine\ORM\EntityManagerInterface;

class TransferDomestic extends AbstractTransferGenerator
{
    private $entityManager;
    private $transferStatusMessage;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating, EntityManagerInterface $entityManager)
    {
        parent::__construct($mailer, $templating);
        $this->entityManager = $entityManager;
    }

    public function validateTransfer($transfer, $user)
    {
        $senderBankAccount = $this->entityManager->getRepository(BankAccount::class)
            ->findOneBy([
                'accountNumber' => $transfer->getSenderAccountNumber()
            ])
        ;

        $receiverBankAccount = $this->entityManager->getRepository(BankAccount::class)
            ->findOneBy([
                'accountNumber' => $transfer->getReceiverAccountNumber()
            ])
        ;

        // Full Validation of Transfer
        if ($receiverBankAccount) {
            $receiverAccountNumber = $receiverBankAccount->getAccountNumber();
            $senderAccountNumber = $senderBankAccount->getAccountNumber();
            if ($receiverAccountNumber != $senderAccountNumber) {
                $senderAvailableFunds = $senderBankAccount->getAvailableFunds();
                $sendingAmount = $transfer->getAmount();
                if ($senderAvailableFunds >= $sendingAmount) {
                    if ($sendingAmount > 0.00) {
                        $this->sendVerificationCode($user->getEmail());
                        return true;
                    } else {
                        $transfer->setStatus('Amount of transfer must be higher than zero');
                        $this->setTransferStatusMessage(3);
                        return false;
                    }
                } else {
                    $transfer->setStatus('Not enough funds');
                    $this->setTransferStatusMessage(2);
                    return false;
                }
            } else {
                $transfer->setStatus('Cant transfer to the same acc number');
                $this->setTransferStatusMessage(1);
                return false;
            }
        } else {
            $transfer->setStatus('Receiver account not exists');
            $this->setTransferStatusMessage(0);
            return false;
        }

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
                $this->transferStatusMessage = 'Kwota przelewu musi być większa niż 0.00 PLN';
                break;
            default:
                $this->transferStatusMessage = 'Wystąpił nieznany błąd, prosimy o kontakt z supportem';
        }
    }

    public function getTransferStatusMessage()
    {
        return $this->transferStatusMessage;
    }

    public function sendTransfer()
    {
        // TODO: Implement sendTransfer() method.
    }
}