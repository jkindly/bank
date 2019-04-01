<?php

namespace App\Controller;

use App\Form\UserAddressFormType;
use App\Form\UserPasswordFormType;
use App\Form\VerificationCodeFormType;
use App\Services\UserSettingsService;
use App\Services\UserVerificationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserSettingsController extends AbstractController
{
    /**
     * @Route("/settings", name="app_user_settings")
     */
    public function index()
    {
        return $this->render('user_settings/settings.html.twig', [
            'controller_name' => 'UserSettingsController',
        ]);
    }

    /**
     * @Route("/settings/ajaxSettingsContent", name="ajax_settings_content")
     * @Method({"POST"})
     */
    public function ajaxUserDataSettings(Request $request)
    {
        if (!$request->isXmlHttpRequest()) return $this->redirectToRoute('app_user_settings');

        $settingsName = $request->request->get('settingsName');

        $response = $this->render('user_settings/'.$settingsName.'/__'.$settingsName.'.html.twig')->getContent();

        return new JsonResponse($response);
    }

    /**
     * @Route("/settings/address", name="app_user_settings_address")
     */
    public function addressSettings(Request $request)
    {
        $form = $this->createForm(UserAddressFormType::class);
        $form->handleRequest($request);

        return $this->render('user_settings/user-data/user_address.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/settings/ajaxValidateForm/{option}", name="ajax_validate_form")
     * @Method({"POST"})
     */
    public function ajaxValidateUserAddress($option, Request $request, UserVerificationService $verification, UserSettingsService $userSettings)
    {
        if (!$request->isXmlHttpRequest()) return $this->redirectToRoute('app_user_settings');

        $formsToValidate = [
            'user_address_form' => UserAddressFormType::class,
            'user_password_form' => UserPasswordFormType::class,
        ];

        $form = $this->createForm($formsToValidate[$option]);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $form = $this->createForm(VerificationCodeFormType::class);
            $form->handleRequest($request);

            $formData = $request->request->get($option);

            $userSettings->insertNewChanges($formData, $option);

            $verification
                ->setVerificationCode()
                ->sendVerificationCode()
            ;

            $response =  $this->render('user_settings/__verification-code.html.twig', [
                'VerificationCodeForm' => $form->createView()
            ])->getContent();

            return new JsonResponse($response);
        }

        $response = 'failed_validation';

        return new JsonResponse($response);
    }

    /**
     * @Route("/settings/ajaxUpdate/user-address", name="ajax_update_user")
     * @Method({"POST"})
     */
    public function ajaxUpdateUser(Request $request, UserVerificationService $verification, UserSettingsService $userSettings)
    {
        if (!$request->isXmlHttpRequest()) return $this->redirectToRoute('app_user_settings');

        $userInputCode = $request->request->get('verification_code_form')['verificationCode'];

        $form = $this->createForm(VerificationCodeFormType::class);
        $form->handleRequest($request);

        $formView = $this->render('user_settings/__verification-code.html.twig', [
            'VerificationCodeForm' => $form->createView()
        ])->getContent();

        if (!$form->isValid()) {
            return new JsonResponse($formView);
        }

        if ($verification->validateVerificationCode($userInputCode)) {
            $userSettings->applyNewAddress();
            return new JsonResponse('Twój adres został pomyślnie zmieniony!');
        } else {
            return new JsonResponse($formView);
        }
    }
}
