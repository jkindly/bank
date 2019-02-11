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
        $user2 = new User();

        $user
            ->setFirstName('Jakub')
            ->setLastName('Kozupa')
            ->setUsername('j.kozupa')
            ->setEmail('kozupa.jakub@gmail.com')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'mnkctnob'));

        $user2
            ->setFirstName('Admin')
            ->setLastName('Admin')
            ->setUsername('admin')
            ->setEmail('admin@freebank.pl')
            ->setPassword($this->passwordEncoder->encodePassword($user, 'mnkctnob'))
            ->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);
        $manager->persist($user2);

        $manager->flush();

        $this->addReference('user', $user);
    }
}
