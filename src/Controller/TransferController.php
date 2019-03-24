<?php

namespace App\Controller;

use App\Entity\BankAccount;
use App\Entity\Transfer;
use App\Entity\User;
use App\Form\TransferFinalizeFormType;
use App\Form\TransferFormType;
use App\Transfer\TransferDomestic;
use Doctrine\ORM\EntityManagerInterface;
use http\Env\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Tests\Compiler\J;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Date;

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
    public function transferDomestic(Request $request, TransferDomestic $transferDomestic)
    {
        // If user doesn't have any account, access to domestic transfer is denied
        if ($this->getUser()->getBankAccounts()->isEmpty()) {
            return $this->redirectToRoute('app_transfer');
        }

        $form = $this->createForm(TransferFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $transferDomestic->validateTransfer($form, $this->getUser());

            if ($transferDomestic->getTransferStatus() == 'to_verify_code') {
                $transferHash = $transferDomestic->getHash();
                $this->container->get('session')->set('transferHash', $transferHash);

                return $this->redirectToRoute('transfer_domestic_verify_code');
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
     * @param TransferDomestic $transferDomestic
     * @return \Symfony\Component\HttpFoundation\Response
     * @return JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function transferDomesticVerifyCode(Request $request, TransferDomestic $transferDomestic)
    {
        $transferHash = $this->container->get('session')->get('transferHash');
        if ($transferHash === NULL) {
            return $this->redirectToRoute('app_transfer_domestic');
        }

        $transfer = $this->getDoctrine()->getRepository(Transfer::class)
            ->findOneBy([
                'transferHash' => $transferHash,
                'status' => 'To code verification',
                'user' => $this->getUser()->getId()
            ])
        ;

        if (!$transfer) {
            return $this->redirectToRoute('app_transfer_domestic');
        }

        if ($request->isXmlHttpRequest()) {
            return $transferDomestic->setTimeToInputCode($transfer);
        }

        $transferVerifictionCode = $transfer->getVerificationCode();

        $form = $this->createForm(TransferFinalizeFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $userInputCode = $form->getData()->getVerificationCode();

            if ($userInputCode === $transferVerifictionCode) {
                $transferDomestic->sendTransfer($transfer);
                $this->addFlash('to_finalize', $transferDomestic->getTransferStatusMessage());
                return $this->redirectToRoute('app_transfer_domestic');
            } else {
                if ($transfer->getFailCodeCount() >= 2) {
                    $this->container->get('session')->clear('transferHash');
                    $transferDomestic->blockUser($this->getUser());
                    return $this->redirectToRoute('app_transfer_domestic');
                }
                $transferDomestic->incrementFailCode($transfer);
                $this->addFlash('wrong_code', $transferDomestic->getTransferStatusMessage());
                return $this->redirectToRoute('transfer_domestic_verify_code');
            }
        }

        return $this->render('transfer/transfer-domestic-finalize.html.twig', [
            'transferFinalizeForm' => $form->createView(),
            'transfer' => $transfer
        ]);
    }
}
