<?php

namespace App\Controller;

use App\Form\VerificationCodeFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class VerificationCodeController extends AbstractController
{
    /**
     * @Route("/verification/code", name="verification_code")
     */
    public function verificationCode(Request $request)
    {
        if (!$request->isXmlHttpRequest()) return $this->redirectToRoute('app_account');

        $form = $this->createForm(VerificationCodeFormType::class);
        $form->handleRequest($request);

        return $this->render('verification_code/verification-code.html.twig', [
            'VerificationCodeForm' => $form->createView()
        ])->getContent();
    }
}
