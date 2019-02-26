<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TestsController extends AbstractController
{
    /**
     * @Route("/tests", name="tests")
     */
    public function index()
    {
        $amount = '12345.67';

        $formatter = new \NumberFormatter('en_GB',  NumberFormatter::CURRENCY);
        echo 'UK: ', $formatter->formatCurrency($amount, 'EUR'), PHP_EOL;


        return $this->render('tests/index.html.twig', [
            'controller_name' => 'TestsController',
        ]);
    }
}
