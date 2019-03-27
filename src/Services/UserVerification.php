<?php
/**
 * Created by PhpStorm.
 * User: jking
 * Date: 3/27/19
 * Time: 10:08 PM
 */

namespace App\Services;

use Symfony\Component\Security\Core\Security;

class UserVerification
{
    private $mailer;
    private $verificationCode;
    private $user;
    private $templating;
    const SENDER_EMAIL = 'kozupa.jakub@gmail.com';

    public function __construct(\Swift_Mailer $mailer, Security $security, \Twig_Environment $templating)
    {
        $this->mailer = $mailer;
        $this->user = $security->getUser();
        $this->templating = $templating;
    }

    public function setVerificationCode(){
        $this->verificationCode = rand(100000, 999999);
    }

    public function getVerificationCode(): int
    {
        return $this->verificationCode;
    }

    public function validateVerificationCode($userInputCode): bool
    {
        return $userInputCode == $this->verificationCode ? true : false;
    }

    public function sendVerificationCode()
    {
        $message = (new \Swift_Message('Kod Transakcji'))
            ->setFrom(self::SENDER_EMAIL)
            ->setTo($this->user->getEmail())
            ->setBody(
                $this->templating->render(
                    'mailer/mailer.html.twig',
                    ['code' => $this->getVerificationCode()]
                ),
                'text/html'
            )
        ;
        $this->mailer->send($message);
    }
}