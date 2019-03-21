<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Entity\Transfer;
use App\Form\TransferFinalizeFormType;
use App\Form\TransferFormType;
use App\Transfer\TransferDomestic;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    /**
     * @Route("/transfer/domestic", name="app_transfer_domestic")
     * @param Request $request
     * @param TransferDomestic $transferDomestic
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function domesticTransfer(Request $request, TransferDomestic $transferDomestic)
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

            $transferDomestic->validateTransfer($transfer, $this->getUser());

            $verificationCode = $transferDomestic->getVerificationCode();

            if ($transferDomestic->getTransferStatus() == 'to_finalize') {

                $form = $this->createForm(TransferFinalizeFormType::class);
                $form->handleRequest($request);

                return $this->render('transfer/transfer-domestic-finalize.html.twig', [
                    'verificationCode' => $verificationCode,
                    'transferFinalizeForm' => $form->createView()
                ]);

            } else {
                $this->addFlash('failed', $transferDomestic->getTransferStatusMessage());
                return $this->redirectToRoute('app_transfer_domestic');
            }
        }

        return $this->render('transfer/transfer-domestic.html.twig', [
            'transferForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/transfer/domestic/finalize", name="transfer_domestic_verify_code")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
//    public function transferDomesticVerifyCode(Request $request)
//    {
//        if ($form->isSubmitted() && $form->isValid()) {
//            $userInputCode = $form->getData()->getVerificationCode();
//            dd($userInputCode);
//        }
//
//        return $this->render('transfer/transfer-domestic-finalize.html.twig', [
//            'transferFinalizeForm' => $form->createView(),
//        ]);
//    }
}
