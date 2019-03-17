<?php
/**
 * Created by PhpStorm.
 * User: jakub
 * Date: 2/9/19
 * Time: 11:36 PM
 */

namespace App\Utils;


class BankAccountUtils
{
    public static function randomAccountNumber()
    {
        $controlSum    = '77';
        $billingNumber1 = '1140';
        $billingNumber2 = '2004';

        for ($i = 0; $i < 4; $i++) {
            $restNumber[$i] = (string)rand(1000, 9999);
        }

        $accountNumber = $controlSum . ' ' . $billingNumber1 . ' ' . $billingNumber2 . ' ' . $restNumber[0] . ' ' .
            $restNumber[1] . ' ' . $restNumber[2] . ' ' . $restNumber[3];

        return $accountNumber;
    }

    public static function renderAmount($amount)
    {
        return number_format($amount, 2, ',', ' ');
    }
}