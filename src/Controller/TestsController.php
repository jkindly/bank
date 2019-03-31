<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class TestsController extends AbstractController
{
    /**
     * @Route("/tests", name="tests")
     */
    public function index(Request $request, Session $session)
    {
    }

    /**
     * @method User getUser()
     */
    protected function getUser(): User
    {
        return parent::getUser();
    }
}
