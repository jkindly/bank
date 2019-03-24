<?php
/**
 * Created by PhpStorm.
 * User: jking
 * Date: 3/19/19
 * Time: 9:44 PM
 */

namespace App\Transfer;


use App\Entity\BankAccount;
use App\Entity\Transfer;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractTransferGenerator
{
    private $mailer;
    private $templating;
    private $code;
    private $transferHash;
    private $em;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->em = $em;
    }

    /**
     * @var Transfer $transfer
     * @var User $user
     */
    abstract public function validateTransfer($form, $user);
    abstract public function sendTransfer($transfer);
    abstract public function setTransferStatusMessage(int $code);

    public function sendVerificationCode($userMail)
    {
        $this->code = rand(100000, 999999);

        $message = (new \Swift_Message('Kod Transakcji'))
            ->setFrom('code@freebank.pl')
            ->setTo($userMail)
            ->setBody(
                $this->templating->render(
                    'mailer/mailer.html.twig',
                    ['code' => $this->code]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }

    public function setHash()
    {
        $random = uniqid();
        $this->transferHash = uniqid() . md5($random);
        return $this->transferHash;
    }

    public function getHash()
    {
        return $this->transferHash;
    }

    /**
     * @var Transfer $transfer*
     * @return mixed
     */
    public function setBankAccounts($transfer)
    {
        $bankAccount['sender'] = $this->em->getRepository(BankAccount::class)
            ->findOneBy([
                'accountNumber' => $transfer->getSenderAccountNumber()
            ])
        ;

        $bankAccount['receiver'] = $this->em->getRepository(BankAccount::class)
            ->findOneBy([
                'accountNumber' => $transfer->getReceiverAccountNumber()
            ])
        ;

        return $bankAccount;
    }

    public function validateVerificationCode($code)
    {
        if ($code === $this->getVerificationCode())
            return true;
        else
            return false;
    }

    public function getVerificationCode()
    {
        return $this->code;
    }
}