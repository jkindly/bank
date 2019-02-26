<?php

namespace App\Controller;

use App\Entity\Transfer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TransferHistoryController extends BaseController
{
    /**
     * @Route("/history", name="app_transfer_history")
     */
    public function getUserTransfersHistory(Request $request)
    {
        $userBankAccounts = $this->getUser()->getBankAccounts();

        // If user has only one bank account, it fetches transfers for only one account number
        // If user has more than one bank account, AJAX take control of this
        if (count($userBankAccounts) > 0) {
            $userBankAccount = $userBankAccounts->toArray();
            $userBankAccountArray = array_shift($userBankAccount);
            $accountNumber = $userBankAccountArray->getAccountNumber();

            $transfersHistory = $this->getDoctrine()->getRepository(Transfer::class)
                ->findAllTransfersLimit5($accountNumber);
        } else {
            $transfersHistory = [];
        }

        if ($request->isXmlHttpRequest()) {
            $jsonData = array();
            $idx = 0;
            $accountNumber = $request->request->get('accountNumber');
            $transfersForChoosedAccount = $this->getDoctrine()->getRepository(Transfer::class)
                ->findAllTransfersLimit5($accountNumber);

            foreach ($transfersForChoosedAccount as $transferForChoosedAccount) {
                $temp = array(
                    'transferId' => $transferForChoosedAccount->getId(),
                    'senderAccountNumber' => $transferForChoosedAccount->getSenderAccountNumber(),
                    'receiverAccountNumber' => $transferForChoosedAccount->getReceiverAccountNumber(),
                    'receiverName' => $transferForChoosedAccount->getReceiverName(),
                    'title' => $transferForChoosedAccount->getTitle(),
                    'createdAt'    => $transferForChoosedAccount->getCreatedAt()->format('d.m.Y'),
                    'amount'       => $transferForChoosedAccount->getAmount(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }

        return $this->render('transfer_history/index.html.twig', [
            'transfersHistory' => $transfersHistory,
            'userBankAccounts' => $userBankAccounts
        ]);
    }

    /**
     * @Route("/history/show-more-transfers", name="show_more_transfers")
     */
    public function showMoreTransfers(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $jsonData = array();
            $idx = 0;
            $accountNumber = $request->request->get('accountNumber');
            $transferId = $request->request->get('transferId');
            $next5Transfers = $this->getDoctrine()->getRepository(Transfer::class)
                ->findNext5Transfers($transferId, $accountNumber);

            foreach ($next5Transfers as $transfer) {
                $temp = array(
                    'transferId' => $transfer->getId(),
                    'senderAccountNumber' => $transfer->getSenderAccountNumber(),
                    'receiverAccountNumber' => $transfer->getReceiverAccountNumber(),
                    'receiverName' => $transfer->getReceiverName(),
                    'title' => $transfer->getTitle(),
                    'createdAt'    => $transfer->getCreatedAt()->format('d.m.Y'),
                    'amount'       => $transfer->getAmount(),
                );
                $jsonData[$idx++] = $temp;
            }
            return new JsonResponse($jsonData);
        }
    }

    /**
     * @Route("/history/transfer-details", name="transfer_details")
     */
    public function showTransferDetails(Request $request)
    {

        if ($request->isXmlHttpRequest()) {
            $transferId = $request->request->get('transferId');
            $transferDetails = $this->getDoctrine()->getRepository(Transfer::class)
                ->find($transferId);

            $transferDetailsArray = array(
                'transferId' => $transferDetails->getId(),
                'senderAccountNumber' => $transferDetails->getSenderAccountNumber(),
                'senderFirstName' => $transferDetails->getUser()->getFirstName(),
                'senderLastName' => $transferDetails->getUser()->getLastName(),
                'senderAddress' => $transferDetails->getUser()->getAddress(),
                'senderCity' => $transferDetails->getUser()->getCity(),
                'receiverAccountNumber' => $transferDetails->getReceiverAccountNumber(),
                'receiverName' => $transferDetails->getReceiverName(),
                'receiverAddress' => $transferDetails->getReceiverAddress(),
                'receiverCity' => $transferDetails->getReceiverCity(),
                'title' => $transferDetails->getTitle(),
                'createdAt' => $transferDetails->getCreatedAt()->format('d.m.Y'),
                'amount' => $transferDetails->getAmount(),
            );

            return new JsonResponse($transferDetailsArray);
        }
    }
}
