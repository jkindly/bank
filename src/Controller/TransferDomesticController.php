<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 3/25/19
 * Time: 3:26 PM
 */

namespace App\Controller;

use App\Entity\Transfer;
use App\Form\TransferFinalizeFormType;
use App\Form\TransferFormType;
use App\Transfer\TransferDomestic;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class TransferDomesticController extends AbstractController
{
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

            $transferDomestic->validateTransferForm($form);

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

        if ($transferHash == NULL) return $this->redirectToRoute('app_transfer_domestic');

        $transfer = $this->getDoctrine()->getRepository(Transfer::class)
            ->findOneBy([
                'transferHash' => $transferHash,
                'status' => 'To code verification',
                'user' => $this->getUser()->getId()
            ]);

        if ($transferDomestic->isCodeTimeExpired($transfer)) {
            $this->addFlash('failed', $transferDomestic->getTransferStatusMessage());
            return $this->redirectToRoute('app_transfer_domestic');
        }

        if ($request->isXmlHttpRequest()) return $transferDomestic->setTimeToInputCode($transfer);

        $form = $this->createForm(TransferFinalizeFormType::class);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $transferVerifictionCode = $transfer->getVerificationCode();
            $userInputCode = $form->getData()->getVerificationCode();

            $validationCode = $transferDomestic->validateVerificationCode($transfer, $transferVerifictionCode, $userInputCode);

            if ($validationCode['status'] == 'to_finalize' || $validationCode['status'] == 'block_user') {
                if ($validationCode['message']) {
                    $this->addFlash('to_finalize', $validationCode['message']);
                    return $this->redirectToRoute('app_transfer_domestic');
                }
            } else {
                $this->addFlash('wrong_code', $validationCode['message']);
                return $this->redirectToRoute('transfer_domestic_verify_code');
            }
        }

        return $this->render('transfer/transfer-domestic-finalize.html.twig', [
            'transferFinalizeForm' => $form->createView(),
            'transfer' => $transfer
        ]);
    }
}