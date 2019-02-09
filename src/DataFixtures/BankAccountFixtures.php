<?php

namespace App\DataFixtures;

use App\Entity\BankAccount;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class BankAccountFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Jakub')
            ->setSurname('Kozupa')
            ->setLogin('j.kozupa')
            ->setPassword('mnkctnob');

        for ($i = 0; $i < 2; $i++ ) {
            $bankAccount = new BankAccount();

            $bankAccount
                ->setAccountType('standard')
                ->setAccountNumber($this->randomAccountNumber())
                ->setBalance(0.00)
                ->setAvailableFunds(0.00)
                ->setUser($user);
            $manager->persist($bankAccount);
        }


        $manager->persist($user);

        $manager->flush();
    }

    private function randomAccountNumber() {
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
}
