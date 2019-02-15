<?php

namespace App\DataFixtures;

use App\Entity\Transfer;
use App\Utils\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class TransferFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++){
            $transfer = new Transfer();
            $faker = Factory::create();

            $transfer
                ->setUser($this->getReference('user'))
                ->setSenderAccountNumber('77 1140 2004 2791 6581 7819 3183')
                ->setReceiverAccountNumber(Account::randomAccountNumber())
                ->setReceiverName($faker->name)
                ->setTitle($faker->text(50))
                ->setAmount(mt_rand(1, 100000)/10)
                ->setCreatedAt($faker->dateTimeThisMonth($max = 'now', $timezone = 'Europe/Berlin'));

            $manager->persist($transfer);
        }

        for ($i = 0; $i < 5; $i++){
            $transfer = new Transfer();
            $faker = Factory::create();

            $transfer
                ->setUser($this->getReference('user'))
                ->setSenderAccountNumber(Account::randomAccountNumber())
                ->setReceiverAccountNumber('77 1140 2004 2791 6581 7819 3183')
                ->setReceiverName($faker->name)
                ->setTitle($faker->text(50))
                ->setAmount(mt_rand(1, 100000)/10)
                ->setCreatedAt($faker->dateTimeThisMonth($max = 'now', $timezone = 'Europe/Berlin'));

            $manager->persist($transfer);
        }

        $manager->flush();
    }
}
