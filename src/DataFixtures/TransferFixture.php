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
//        for ($i = 0; $i < 20; $i++){
//            $transfer = new Transfer();
            $faker = Factory::create();
//
//            $transfer
//                ->setUser($this->getReference('user'));
//
//            if($faker->boolean()) {
//                $transfer->setSenderAccountNumber('77 1140 2004 2791 6581 7819 3183');
//                $transfer->setReceiverAccountNumber(Account::randomAccountNumber());
//            } else {
//                $transfer->setSenderAccountNumber(Account::randomAccountNumber());
//                $transfer->setReceiverAccountNumber('77 1140 2004 2791 6581 7819 3183');
//            }
//
//            $transfer
//                ->setReceiverName($faker->name)
//                ->setTitle($faker->text(50))
//                ->setAmount(mt_rand(1, 100000)/10);
////                ->setCreatedAt($faker->dateTimeThisMonth($max = 'now', $timezone = 'Europe/Berlin'));
//            if ($i <= 9) {
//                $transfer->setCreatedAt(new \DateTime('2019-02-0'.$i.' 21:00:0'.$i));
//            } else {
//                $transfer->setCreatedAt(new \DateTime('2019-02-'.$i.' 21:'.$i.':00'));
//            }
//
//            $manager->persist($transfer);
//        }
//
//        for ($i = 0; $i < 9999; $i++){
//            $transfer = new Transfer();
//            $faker = Factory::create();
//
//            $transfer
//                ->setUser($this->getReference('admin'))
//                ->setSenderAccountNumber(Account::randomAccountNumber())
//                ->setReceiverAccountNumber(Account::randomAccountNumber())
//                ->setReceiverName($faker->name)
//                ->setTitle($faker->text(50))
//                ->setAmount(mt_rand(1, 100000)/10)
//                ->setCreatedAt($faker->dateTimeThisMonth($max = 'now', $timezone = 'Europe/Berlin'));
////                ->setCreatedAt(new \DateTime('2019-02-1'.$i.' 21:02:0'.$i));
//            $manager->persist($transfer);
//        }

        $transfer = new Transfer();

            $transfer
                ->setUser($this->getReference('kuba'))
                ->setSenderAccountNumber('77 1140 2004 2791 6581 7819 3183')
                ->setReceiverAccountNumber('77 1140 2004 2791 6581 7819 1234')
                ->setReceiverName('Jan Kowalski')
                ->setTitle('Opłata za mieszkanie + prąd')
                ->setReceiverAddress('Nieznana 10')
                ->setReceiverCity('Bydgoszcz')
                ->setAmount(mt_rand(1, 100000)/10)
                ->setCreatedAt($faker->dateTimeThisMonth($max = 'now', $timezone = 'Europe/Berlin'));
//                ->setCreatedAt(new \DateTime('2019-02-1'.$i.' 21:02:0'.$i));
            $manager->persist($transfer);

        $manager->flush();
    }
}
