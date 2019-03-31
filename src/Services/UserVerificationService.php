<?php
/**
 * Created by PhpStorm.
 * User: jking
 * Date: 3/27/19
 * Time: 10:08 PM
 */

namespace App\Services;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class UserVerificationService
{
    private $mailer;
    private $user;
    private $templating;
    private $em;
    private $userRepository;
    const SENDER_EMAIL = 'kozupa.jakub@gmail.com';

    public function __construct(\Swift_Mailer $mailer, Security $security, \Twig_Environment $templating, EntityManagerInterface $em)
    {
        $this->mailer = $mailer;
        $this->user = $security->getUser();
        $this->templating = $templating;
        $this->em = $em;
        $this->userRepository = $this->em->getRepository(User::class)->find($this->user->getId());
    }

    public function setVerificationCode(){
        $verificationCode = rand(100000, 999999);

        $this->userRepository->setVerificationCode($verificationCode);
        $this->em->flush();

        return $this;
    }

    public function getVerificationCode(): int
    {
        return $this->userRepository->getVerificationCode();
    }

    public function validateVerificationCode($userInputCode): bool
    {
        $validationCode = [];

        if ($this->getVerificationCode() == $userInputCode) {
            $validationCode['message'] = 'succeess';
        }




        return $userInputCode == $this->getVerificationCode() ? true : false;
    }

    public function sendVerificationCode()
    {
        return;
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