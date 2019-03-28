<?php

namespace App\Controller;

use App\Form\UserAddressFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/settings/ajaxChange/user-address", name="ajax_change_address")
     */
    public function changeUserAddress(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $form = $this->createForm(UserAddressFormType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                dd('chuj');
            }

            $response = $this->render('user_settings/user_data/__user_address.html.twig', [
                'UserAddressForm' => $form->createView()
            ])->getContent();

            return new JsonResponse($response);
        }

        return $this->redirectToRoute('app_user_settings');
    }

}
