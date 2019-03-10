<?php

namespace App\Controller;

use App\Entity\Transfer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TransferQueueController extends AbstractController
{
    /**
     * @Route("/transfer/queue", name="app_transfer_queue")
     */
    public function index(EntityManagerInterface $em)
    {
        $transferQueue = $em->getRepository(Transfer::class)->findTransfersInQueue();

        return $this->render('transfer_queue/transfer-queue.twig', [
            'transferQueue' => $transferQueue,
        ]);
    }
}
