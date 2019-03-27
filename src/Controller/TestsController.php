<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class TestsController extends AbstractController
{
    /**
     * @Route("/tests", name="tests")
     */
    public function index(\Swift_Mailer $mailer, Security $security)
    {
        $email = $security->getUser()->getEmail();
        dd($email);

        return $this->render('tests/index.html.twig', [
            'code' => $code
        ]);
    }

    /**
     * @method User getUser()
     */
    protected function getUser(): User
    {
        return parent::getUser();
    }
}
