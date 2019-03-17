<?php

namespace App\DataFixtures;

use App\Entity\BankAccount;
use App\Entity\User;
use App\Utils\BankAccountUtils;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class BankAccountFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $bankAccount1 = new BankAccountUtils();
        $bankAccount2 = new BankAccountUtils();
        $bankAccount3 = new BankAccountUtils();

        $bankAccount1
            ->setAccountType('standard')
            ->setAccountNumber('77 1140 2004 2791 6581 7819 3183')
            ->setBalance(0.00)
            ->setAvailableFunds(0.00)
            ->setUser($this->getReference('kuba'));

        $bankAccount2
            ->setAccountType('firmowe')
            ->setAccountNumber('77 1140 2004 2791 6581 7819 1234')
            ->setBalance(0.00)
            ->setAvailableFunds(0.00)
            ->setUser($this->getReference('jkowalski'));

        $bankAccount3
            ->setAccountType('standard')
            ->setAccountNumber(BankAccountUtils::randomAccountNumber())
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
