<?php
/**
 * Created by PhpStorm.
 * User: jking
 * Date: 3/19/19
 * Time: 9:44 PM
 */

namespace App\Transfer;


use App\Entity\Transfer;
use App\Entity\User;

abstract class AbstractTransferGenerator
{
    private $mailer;
    private $templating;

    public function __construct(\Swift_Mailer $mailer, \Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @var Transfer $transfer
     * @var User $user
     */
    abstract public function validateTransfer($transfer, $user);
    abstract public function sendTransfer();
    abstract public function setTransferStatusMessage(int $code);

    public function sendVerificationCode($userMail)
    {
        $code = rand(100000, 999999);

        $message = (new \Swift_Message('Kod Transakcji'))
            ->setFrom('code@freebank.pl')
            ->setTo($userMail)
            ->setBody(
                $this->templating->render(
                    'mailer/mailer.html.twig',
                    ['code' => $code]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }
}