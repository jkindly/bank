<?php

namespace App\Controller;

use App\Entity\BankAccount;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    /**
     * @Route("/transfer", name="app_transfer")
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function transfer(EntityManagerInterface $em)
    {
        $userId = $this->getUser()->getId();

        $bankAccount = $em->getRepository(BankAccount::class)
            ->findBy(['user' => $userId]);

        return $this->render('transfer/transfer.html.twig', [
            'bankAccount' => $bankAccount
        ]);
    }
}
