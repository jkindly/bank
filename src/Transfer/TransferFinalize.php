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
     */
    public function completeTransfer($transfer)
    {
        $transferAmount = $transfer->getAmount();
        $senderAccNumber = $transfer->getSenderAccountNumber();
        $receiverAccNumber = $transfer->getReceiverAccountNumber();

        /**
         * @var BankAccount $receiverAccount
         * @var BankAccount $senderAccount
         */
        $senderAccount = $this->em->getRepository(BankAccount::class)
            ->findOneBy(['accountNumber' => $senderAccNumber]);
        $receiverAccount = $this->em->getRepository(BankAccount::class)
            ->findOneBy(['accountNumber' => $receiverAccNumber]);

        $senderBalance = $senderAccount->getBalance();
        $receiverBalance = $receiverAccount->getBalance();
        $receiverAvailableFunds = $receiverAccount->getAvailableFunds();

        $receiverAccount->setAvailableFunds($receiverAvailableFunds + $transferAmount);
        $receiverAccount->setBalance($receiverBalance + $transferAmount);

        $senderAccount->setBalance($senderBalance - $transferAmount);

        $transfer->setIsCompleted(true);

        $this->em->persist($transfer);
        $this->em->flush();
    }
}