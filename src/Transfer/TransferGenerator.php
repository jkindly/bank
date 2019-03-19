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
use App\Utils\BankAccountUtils;
use Doctrine\ORM\EntityManagerInterface;
class TransferGenerator
{
    private $em;
    private $transferStatus;
    private $transferStatusMessage;
    private $senderAvailableFunds;
    private $sendingAmount;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @var Transfer $transfer
     */
    public function validateTransfer($transfer)
    {
        $this->sendingAmount = $transfer->getAmount();

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

        $this->senderAvailableFunds = $senderBankAccount->getAvailableFunds();

        $senderAccountNumber = $senderBankAccount->getAccountNumber();

        // checking if receiver bank account exists
        if ($receiverBankAccount) {
            $receiverAccountNumber = $receiverBankAccount->getAccountNumber();
            //checking if receiver acc number is the same as sender
            if ($receiverAccountNumber == $senderAccountNumber) {
                $transfer
                    ->setStatus('Cant transfer to the same acc number')
                ;
                $this->setTransferStatus('failed');
                $this->setTransferStatusMessage('Nie możesz wykonać przelewu na ten sam numer rachunku');
            } else {
                // checking if sender has enough available funds to send transfer
                if ($this->senderAvailableFunds >= $this->sendingAmount) {
                    // checking if transfer amount is higher than 0.00 PLN
                    if ($transfer->getAmount() > 0.00) {
                        $this->sendVerificationCode();
                        $transfer
                            ->setStatus('Transfer send to finalize');
                        $transfer->setSenderFundsAfterTransfer($this->senderAvailableFunds - $this->sendingAmount);
                        $this->setTransferStatus('to_finalize');
                        $this->setTransferStatusMessage(
                            'Przelew na kwotę ' . BankAccountUtils::renderAmount($transfer->getAmount()) . ' PLN został przyjęty do realizacji'
                        );

                        $senderBankAccount
                            ->setAvailableFunds($this->senderAvailableFunds - $this->sendingAmount);
                    } else {
                        $transfer
                            ->setStatus('Amount of transfer must be higher than zero');
                        $this->setTransferStatus('failed');
                        $this->setTransferStatusMessage('Kwota przelewu musi być większa niż 0.00 PLN');
                    }
                } else {
                    $transfer
                        ->setStatus('Not enough funds')
                    ;
                    $this->setTransferStatus('failed');
                    $this->setTransferStatusMessage('Nie masz wystarczających środków na koncie');
                }
            }
        } else {
            $transfer
                ->setStatus('Receiver account not exists')
            ;
            $this->setTransferStatus('failed');
            $this->setTransferStatusMessage('Podany numer rachunku odbiorcy nie istnieje');
        }

        $this->em->persist($transfer);
        $this->em->flush();
    }

    public function sendVerificationCode(\Swift_Mailer $mailer)
    {
        $userMail = $this->user->getMail();
        $code = rand(100000, 999999);
        $message = (new \Swift_Message('Kod transakcji'))
            ->setFrom('transfer@freebank.pl')
            ->setTo('kozupa.jakub@gmail.com')
            ->setBody(
                $this->renderView(
                    'tests/index.html.twig',
                    ['code' => $code]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);
    }

    public function getTransferStatus()
    {
        return $this->transferStatus;
    }

    public function setTransferStatus($transferStatus): void
    {
        $this->transferStatus = $transferStatus;
    }

    public function getTransferStatusMessage()
    {
        return $this->transferStatusMessage;
    }

    public function setTransferStatusMessage($transferStatusMessage): void
    {
        $this->transferStatusMessage = $transferStatusMessage;
    }
}