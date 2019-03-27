<?php

namespace App\Controller;

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
     * @Route("/settings/ajaxUserData", name="user_data_settings")
     */
    public function ajaxUserDataSettings(Request $request)
    {
        if ($request->isXmlHttpRequest()) {
            $response = $this->render('user_settings/__user_data.html.twig')->getContent();
            return new JsonResponse($response);
        }
    }


}
