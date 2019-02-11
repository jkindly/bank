<?php

namespace App\Controller;

use App\Entity\BankAccount;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class BankAccountController extends BaseController
{
    /**
     * @Route("/account", name="app_account")
     */
    public function index()
    {
        $bankAccount = $this->getDoctrine()
            ->getRepository(BankAccount::class)
            ->findAll();

        return $this->render('account/account.html.twig',
            [
                'bankAccount' => $bankAccount
            ]);
    }
}
