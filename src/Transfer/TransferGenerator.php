<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 3/6/19
 * Time: 12:52 PM
 */

namespace App\Transfer;


use App\Entity\BankAccount;
use App\Entity\Transfer;
use Doctrine\ORM\EntityManagerInterface;

class TransferGenerator
{
    private $em;
    private $transferStatus;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @var Transfer $transfer
     */
    public function sendTransfer($transfer)
    {
        $sendingAmount = $transfer->getAmount();

        $senderBankAccount = $this->em->getRepository(BankAccount::class)
            ->findOneBy([
                'accountNumber' => $transfer->getSenderAccountNumber()
            ])
        ;

        $receiverBankAccount = $this->em->getRepository(BankAccount::class)
            ->findOneBy([
                'accountNumber' => $transfer->getReceiverAccountNumber()
            ])
        ;

        $senderAvailableFunds = $senderBankAccount->getAvailableFunds();
        $senderBalance = $senderBankAccount->getBalance();

        // checking if receiver bank account exists
        if ($receiverBankAccount) {
            $receiverAvailableFunds = $receiverBankAccount->getAvailableFunds();
            $receiverBalance = $receiverBankAccount->getBalance();

            // checking if sender has enough available funds to send transfer
            if ($senderAvailableFunds >= $sendingAmount) {
                // updating balance and available funds to sender
                $senderBankAccount
                    ->setAvailableFunds($senderAvailableFunds - $sendingAmount)
                    ->setBalance($senderBalance - $sendingAmount)
                ;

                // updating balance and available funds to receiver
                $receiverBankAccount
                    ->setAvailableFunds($receiverAvailableFunds + $sendingAmount)
                    ->setBalance($receiverBalance + $sendingAmount)
                ;

                $transfer
                    ->setIsSuccess(true)
                    ->setStatus('Transfer success')
                ;
                $this->setTransferStatus('transfer_success');
            } else {
                $transfer
                    ->setIsSuccess(false)
                    ->setStatus('Not enough funds')
                ;
                $this->setTransferStatus('not_enough_funds');
            }
        } else {
            $transfer
                ->setIsSuccess(false)
                ->setStatus('Receiver account not exists')
            ;
            $this->setTransferStatus('receiver_acc_not_exists');
        }

        $this->em->persist($transfer);
        $this->em->flush();
    }

    public function setTransferStatus($transferStatus)
    {
        $this->transferStatus = $transferStatus;
    }

    public function getTransferStatus()
    {
        return $this->transferStatus;
    }
}