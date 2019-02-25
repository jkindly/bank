<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Utils\Account;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixture extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {

        $user = new User();
        $admin = new User();
        $jkowalski = new User();

        $user
            ->setFirstName('Jakub')
            ->setLastName('Kozupa')
            ->setUsername('j.kozupa')
            ->setEmail('kozupa.jakub@gmail.com')
            ->setAddress('Lipowa 57/59')
            ->setCity('Bydgoszcz')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'mnkctnob'));

        $admin
            ->setFirstName('Admin')
            ->setLastName('Admin')
            ->setUsername('admin')
            ->setEmail('admin@freebank.pl')
            ->setAddress('Karpacka 39B/70')
            ->setCity('Inowrocław')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'mnkctnob'))
            ->setRoles(['ROLE_ADMIN']);

        $jkowalski
            ->setFirstName('Jan')
            ->setLastName('Kowalski')
            ->setUsername('j.kowalski')
            ->setEmail('j.kowalski@gmail.com')
            ->setAddress('Nieznana 10')
            ->setCity('Bydgoszcz')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'mnkctnob'));


        $manager->persist($user);
        $manager->persist($admin);
        $manager->persist($jkowalski);

        $manager->flush();

        $this->addReference('kuba', $user);
        $this->addReference('admin', $admin);
        $this->addReference('jkowalski', $jkowalski);
    }
}
