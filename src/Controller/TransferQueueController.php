<?php

namespace App\Controller;

use App\Entity\Transfer;
use App\Transfer\TransferFinalize;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransferQueueController extends AbstractController
{
    /**
     * @Route("/manage/transfer-queue", name="app_transfer_queue")
     */
    public function index(EntityManagerInterface $em)
    {
        $transferQueue = $em->getRepository(Transfer::class)->findTransfersInQueue();

        return $this->render('manage/transfer-queue.twig', [
            'transferQueue' => $transferQueue,
        ]);
    }

    /**
     * @Route("/manage/transfer-queue/decision", name="transfer_decision")
     */
    public function transferAccept(Request $request, EntityManagerInterface $em, TransferFinalize $transferFinalize)
    {
//        $transferId = 103;
        $transferId = $request->request->get('transferId');
        $transferDecision = $request->request->get('transferDecision');
        $transfer = $em->getRepository(Transfer::class)->find($transferId);

        $transferFinalize->makeDecision($transfer, $transferDecision);

        return new JsonResponse('done');
    }
}
