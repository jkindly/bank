<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestsController extends AbstractController
{
    /**
     * @Route("/tests", name="tests")
     */
    public function index(\Swift_Mailer $mailer)
    {
        $code = 123456123;
        $message = (new \Swift_Message('Kod transakcji'))
            ->setFrom('transfer@freebank.pl')
            ->setTo('kozupa.jakub@gmail.com')
            ->setBody(
                $this->renderView(
                    'tests/index.html.twig',
                    ['code' => $code]
                ),
                'text/html'
            )
        ;

        $mailer->send($message);


        return $this->render('tests/index.html.twig', [
            'code' => $code
        ]);
    }
}
