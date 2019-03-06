<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Entity\LoginLogs;
use Symfony\Component\Routing\Annotation\Route;

class BankAccountController extends BaseController
{
    /**
     * @Route("/account", name="app_account")
     */
    public function accounts()
    {
        $userId = $this->getUser()->getId();

        $bankAccounts = $this->getDoctrine()
            ->getRepository(BankAccount::class)
            ->findBy(['user' => $userId])
        ;

        $userLogSuccess = $this->getDoctrine()
            ->getRepository(LoginLogs::class)
            ->findLatestUserLoginLog($userId, 1)
        ;

        $userLogFailed = $this->getDoctrine()
            ->getRepository(LoginLogs::class)
            ->findLatestUserLoginLog($userId, 0);

        return $this->render('account/account.html.twig',
            [
                'bankAccounts' => $bankAccounts,
                'loginLogsSuccess' => $userLogSuccess,
                'loginLogFailed' => $userLogFailed
            ]);
    }
}
