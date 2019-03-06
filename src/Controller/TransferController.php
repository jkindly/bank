<?php

namespace App\Controller;

use App\Entity\Transfer;
use App\Form\TransferFormType;
use App\Transfer\TransferGenerator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    /**
     * @Route("/transfer", name="app_transfer")
     */
    public function transfer()
    {
        return $this->render('transfer/transfer.html.twig');
    }


    /**
     * @Route("/transfer/domestic", name="app_transfer_domestic")
     */

    public function domesticTransfer(Request $request, TransferGenerator $transferGenerator)
    {
        $form = $this->createForm(TransferFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $transfer = new Transfer();

            $senderAccountNumber = $form->get('senderAccountNumber')->getData();

            $transfer
                ->setUser($this->getUser())
                ->setReceiverName($data->getReceiverName())
                ->setReceiverAccountNumber($data->getReceiverAccountNumber())
                ->setAmount($data->getAmount())
                ->setTitle($data->getTitle())
                ->setSenderAccountNumber($senderAccountNumber->getAccountNumber());

            $transferGenerator->sendTransfer($transfer);

            $transferStatus = $transferGenerator->getTransferStatus();

            if ($transferStatus == 'transfer_success') {
                $this->addFlash('success', 'Przelew został nadany');
            } elseif ($transferStatus == 'not_enough_funds') {
                $this->addFlash('success', 'Za mało środków na koncie');
            } elseif ($transferStatus == 'receiver_acc_not_exists') {
                $this->addFlash('success', 'Rachunek odbiorcy nie istnieje');
            }

            return $this->redirectToRoute('app_transfer_domestic');
        }

        return $this->render('transfer/transfer-domestic.twig', [
            'transferForm' => $form->createView()
        ]);
    }
}
