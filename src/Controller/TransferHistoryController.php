<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TransferHistoryController extends AbstractController
{
    /**
     * @Route("/history", name="app_transfer_history")
     */
    public function index()
    {
        return $this->render('transfer_history/index.html.twig', [
            'controller_name' => 'TransferHistoryController',
        ]);
    }
}
