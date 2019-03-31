<?php

namespace App\Controller;

use App\Form\UserAddressFormType;
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
        if ($request->isXmlHttpRequest()) {
            $settingsName = $request->request->get('settingsName');

            $response = $this->render('user_settings/'.$settingsName.'/__'.$settingsName.'.html.twig')->getContent();
            return new JsonResponse($response);
        }

        return $this->redirectToRoute('app_user_settings');
    }

    /**
     * @Route("/settings/ajaxLoadForm/user-address", name="ajax_load_form_address")
     */
    public function ajaxLoadUserAddress(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(UserAddressFormType::class);
            $form->handleRequest($request);

            $response = $this->render('user_settings/user-data/__change-user-address.html.twig', [
                'UserAddressForm' => $form->createView()
            ])->getContent();

            return new JsonResponse($response);
        }

        return $this->redirectToRoute('app_user_settings');
    }

    /**
     * @Route("/settings/ajaxValidateForm/user-address", name="ajax_validate_address")
     * @Method({"POST"})
     */
    public function ajaxValidateUserAddress(Request $request, UserVerificationService $verification, UserSettingsService $userSettings)
    {
        if (!$request->isXmlHttpRequest()) return $this->redirectToRoute('app_user_settings');

        $form = $this->createForm(UserAddressFormType::class);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $form = $this->createForm(VerificationCodeFormType::class);
            $form->handleRequest($request);

            $formData = $request->request->get('user_address_form');

            $userSettings->insertNewAddressChange($formData);

            $verification
                ->setVerificationCode()
                ->sendVerificationCode()
            ;

            $response =  $this->render('user_settings/__verification-code.html.twig', [
                'VerificationCodeForm' => $form->createView()
            ])->getContent();

            return new JsonResponse($response);
        }

        $response = $this->render('user_settings/user-data/__change-user-address.html.twig', [
            'UserAddressForm' => $form->createView()
        ])->getContent();

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
