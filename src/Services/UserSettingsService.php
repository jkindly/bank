<?php


namespace App\Services;


use App\Entity\UserAddressSettings;
use App\Entity\UserPasswordSettings;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class UserSettingsService
{
    private $em;
    private $user;
    private $session;

    public function __construct(EntityManagerInterface $em, Security $security, SessionInterface $session)
    {
        $this->em = $em;
        $this->user = $security->getUser();
        $this->session = $session;
    }

    public function insertNewChanges($formData, $changeOption)
    {
        $hash = md5(uniqid());

        if ($changeOption == 'user_address_form') {
            $userAddressSettings = new UserAddressSettings();

            $userAddressSettings
                ->setUser($this->user)
                ->setHash($hash)
                ->setOldStreet($this->user->getStreet())
                ->setOldZipcode($this->user->getZipcode())
                ->setOldCity($this->user->getCity())
                ->setOldCountry($this->user->getCountry())
                ->setNewStreet($formData['street'])
                ->setNewZipcode($formData['zipcode'])
                ->setNewCity($formData['city'])
                ->setNewCountry($formData['country'])
            ;

            $this->session->set('userAddressChangeHash', $hash);

            $this->em->persist($userAddressSettings);
        }
        if ($changeOption == 'user_password_change') {
            $userPasswordSettings = new UserPasswordSettings();

        }
        $this->em->flush();
    }

    public function insertNewAddressChange($formData)
    {
        $userAddressSettings = new UserAddressSettings();

        $userAddressSettings
            ->setUser($this->user)
            ->setHash($hash)
            ->setOldStreet($this->user->getStreet())
            ->setOldZipcode($this->user->getZipcode())
            ->setOldCity($this->user->getCity())
            ->setOldCountry($this->user->getCountry())
            ->setNewStreet($formData['street'])
            ->setNewZipcode($formData['zipcode'])
            ->setNewCity($formData['city'])
            ->setNewCountry($formData['country'])
        ;

        $this->session->set('userAddressChangeHash', $hash);

        $this->em->persist($userAddressSettings);
        $this->em->flush();
    }

    public function applyNewAddress()
    {
        if ($this->session->has('userAddressChangeHash')) {
            $userAddressChangeHash = $this->session->get('userAddressChangeHash');

            $settingsRow = $this->em->getRepository(UserAddressSettings::class)
                ->findOneBy(['hash' => $userAddressChangeHash])
            ;

            $settingsRow->setIsSuccess(true);
            $this->user
                ->setStreet($settingsRow->getNewStreet())
                ->setZipcode($settingsRow->getNewZipcode())
                ->setCity($settingsRow->getNewCity())
                ->setCountry($settingsRow->getNewCountry())
            ;

            $this->em->flush();
        }
    }

}