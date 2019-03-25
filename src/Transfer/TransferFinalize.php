<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 3/11/19
 * Time: 1:49 PM
 */

namespace App\Transfer;


use App\Entity\BankAccount;
use App\Entity\Transfer;
use Doctrine\ORM\EntityManagerInterface;

class TransferFinalize
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @var Transfer $transfer
     * @param $transferDecision
     */
    public function makeDecision($transfer, $transferDecision)
    {
        $senderAccNumber = $transfer->getSenderAccountNumber();
        $senderAccount = $this->em->getRepository(BankAccount::class)
            ->findOneBy(['accountNumber' => $senderAccNumber]);
        $senderAvailableFunds = $senderAccount->getAvailableFunds();
        $transferAmount = $transfer->getAmount();

        if ($transferDecision == 'confirm') {
            $receiverAccNumber = $transfer->getReceiverAccountNumber();
            $senderBalance = $senderAccount->getBalance();
            /**
             * @var BankAccount $receiverAccount
             * @var BankAccount $senderAccount
             */
            $receiverAccount = $this->em->getRepository(BankAccount::class)
                ->findOneBy(['accountNumber' => $receiverAccNumber]);

            $receiverBalance = $receiverAccount->getBalance();
            $receiverAvailableFunds = $receiverAccount->getAvailableFunds();

            $receiverAccount->setAvailableFunds($receiverAvailableFunds + $transferAmount);
            $receiverAccount->setBalance($receiverBalance + $transferAmount);

            $senderAccount->setBalance($senderBalance - $transferAmount);

            $transfer->setReceiverFundsAfterTransfer($receiverAvailableFunds + $transferAmount);
            $transfer->setStatus('Success');
            $transfer->setIsSuccess(1);
        } elseif ($transferDecision == 'decline') {
            $senderAccount->setAvailableFunds($senderAvailableFunds + $transferAmount);
            $transfer->setStatus('Declined by admin');
        } else {
            // error jakiś jebany z dupy
        }
        $transfer->setIsCompleted(true);

        $this->em->persist($transfer);
        $this->em->flush();
    }
}