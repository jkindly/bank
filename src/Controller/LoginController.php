<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function index()
    {
        return $this->render('login/index.html.twig', [
            'controller_name' => 'LoginController',
        ]);
    }

    /**
     * @Route("/new-user", name="new_user")
     */
    public function newUser() {
        $entityManager = $this->getDoctrine()->getManager();

        $user = new User();
        $user->setName('Jakub')
            ->setSurname('Kozupa')
            ->setLogin('j.kozupa')
            ->setPassword('mnkctnob');

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response(sprintf('Added new user with name: %s, surname: %s and login: %s', $user->getName(), $user->getSurname(), $user->getLogin()));
    }
}
