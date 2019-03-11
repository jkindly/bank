<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Entity\Transfer;
use App\Form\TransferFormType;
use App\Transfer\TransferGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransferController extends AbstractController
{
    /**
     * @Route("/transfer", name="app_transfer")
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

    /**
     * @Route("/transfer/domestic", name="app_transfer_domestic")
     */
    public function domesticTransfer(Request $request, TransferGenerator $transferGenerator)
    {
        // If user doesn't have any account, access to domestic transfer is denied
        if ($this->getUser()->getBankAccounts()->isEmpty()) {
            return $this->redirectToRoute('app_transfer');
        }

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

            $this->addFlash(
                $transferGenerator->getTransferStatus(),
                $transferGenerator->getTransferStatusMessage()
            );

            return $this->redirectToRoute('app_transfer_domestic');
        }

        return $this->render('transfer/transfer-domestic.twig', [
            'transferForm' => $form->createView(),
        ]);
    }
}
