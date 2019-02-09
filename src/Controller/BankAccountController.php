<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BankAccountController extends AbstractController
{
    /**
     * @Route("/account/{login}", name="app_account")
     */
    public function index($login)
    {
        $em = $this->getDoctrine()->getManager();

        $account = $em->getRepository(User::class)
            ->findOneBy(['login' => $login]);

        if (!$account) {
            throw $this->createNotFoundException(
                'Nie znaleziono użytkownika o loginie: ' . $login
            );
        }

        return $this->render('account/index.html.twig', [
            'account' => $account
        ]);
    }
}
