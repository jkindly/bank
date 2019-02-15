<?php

namespace App\Controller;

use App\Entity\BankAccount;
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

        $transfersHistory = $this->getDoctrine()->getRepository(Transfer::class)
            ->findAllTransfers('77 1140 2004 2791 6581 7819 3183');

//        dd($transfersHistory);

        if ($request->isXmlHttpRequest()) {
            $jsonData = array();
            $idx = 0;
            $accountNumber = $request->request->get('accountNumber');
            $transfersForChoosedAccount = $this->getDoctrine()->getRepository(Transfer::class)
                ->findAllTransfers($accountNumber);

            foreach ($transfersForChoosedAccount as $transferForChoosedAccount) {
                $temp = array(
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
}
