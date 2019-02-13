<?php

namespace App\DataFixtures;

use App\Entity\BankAccount;
use App\Entity\User;
use App\Utils\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BankAccountFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $bankAccount1 = new BankAccount();
        $bankAccount2 = new BankAccount();
        $bankAccount3 = new BankAccount();

        $bankAccount1
            ->setAccountType('standard')
            ->setAccountNumber(Account::randomAccountNumber())
            ->setBalance(0.00)
            ->setAvailableFunds(0.00)
            ->setUser($this->getReference('user'));

        $bankAccount2
            ->setAccountType('firmowe')
            ->setAccountNumber(Account::randomAccountNumber())
            ->setBalance(0.00)
            ->setAvailableFunds(0.00)
            ->setUser($this->getReference('user'));

        $bankAccount3
            ->setAccountType('standard')
            ->setAccountNumber(Account::randomAccountNumber())
            ->setBalance(0.00)
            ->setAvailableFunds(0.00)
            ->setUser($this->getReference('admin'));

        $manager->persist($bankAccount1);
        $manager->persist($bankAccount2);
        $manager->persist($bankAccount3);

        $manager->flush();
    }

    // sort loading fixtures, UserFixutre must be loaded before this one (BankAccountFixture)
    public function getDependencies()
    {
        return array (
          UserFixture::class
        );
    }
}
