<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Wprowadź hasło", groups={"user_password"})
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BankAccount", mappedBy="user", orphanRemoval=true)
     */
    private $bankAccounts;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LoginLogs", mappedBy="user", orphanRemoval=true)
     */
    private $loginLogs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Transfer", mappedBy="user", orphanRemoval=true)
     */
    private $transfers;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Wprowadź ulicę", groups={"user_address"})
     */
    private $street;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Wprowadź miasto", groups={"user_address"})
     */
    private $city;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $isBlockedTransfers = false;

    /**
     * @ORM\Column(type="string", length=6)
     * @Assert\NotBlank(message="Wprowadź kod pocztowy", groups={"user_address"})
     */
    private $zipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $streetPermanent;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $cityPermanent;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $zipcodePermanent;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Wprowadź kraj", groups={"user_address"})
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $countryPermanent;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $verificationCode;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserAddressSettings", mappedBy="user")
     */
    private $userAddressSettings;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserPasswordSettings", mappedBy="user")
     */
    private $userPasswordSettings;

    public function __construct()
    {
        $this->bankAccounts = new ArrayCollection();
        $this->loginLogs = new ArrayCollection();
        $this->transfers = new ArrayCollection();
        $this->userAddressSettings = new ArrayCollection();
        $this->userPasswordSettings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when use bcrypt or argon
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return Collection|BankAccount[]
     */
    public function getBankAccounts(): Collection
    {
        return $this->bankAccounts;
    }

    public function addBankAccount(BankAccount $bankAccount): self
    {
        if (!$this->bankAccounts->contains($bankAccount)) {
            $this->bankAccounts[] = $bankAccount;
            $bankAccount->setUser($this);
        }

        return $this;
    }

    public function removeBankAccount(BankAccount $bankAccount): self
    {
        if ($this->bankAccounts->contains($bankAccount)) {
            $this->bankAccounts->removeElement($bankAccount);
            // set the owning side to null (unless already changed)
            if ($bankAccount->getUser() === $this) {
                $bankAccount->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LoginLogs[]
     */
    public function getLoginLogs(): Collection
    {
        return $this->loginLogs;
    }

    public function addLoginLog(LoginLogs $loginLog): self
    {
        if (!$this->loginLogs->contains($loginLog)) {
            $this->loginLogs[] = $loginLog;
            $loginLog->setUser($this);
        }

        return $this;
    }

    public function removeLoginLog(LoginLogs $loginLog): self
    {
        if ($this->loginLogs->contains($loginLog)) {
            $this->loginLogs->removeElement($loginLog);
            // set the owning side to null (unless already changed)
            if ($loginLog->getUser() === $this) {
                $loginLog->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Transfer[]
     */
    public function getTransfers(): Collection
    {
        return $this->transfers;
    }

    public function addTransfer(Transfer $transfer): self
    {
        if (!$this->transfers->contains($transfer)) {
            $this->transfers[] = $transfer;
            $transfer->setUser($this);
        }

        return $this;
    }

    public function removeTransfer(Transfer $transfer): self
    {
        if ($this->transfers->contains($transfer)) {
            $this->transfers->removeElement($transfer);
            // set the owning side to null (unless already changed)
            if ($transfer->getUser() === $this) {
                $transfer->setUser(null);
            }
        }

        return $this;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getIsBlockedTransfers(): ?bool
    {
        return $this->isBlockedTransfers;
    }

    public function setIsBlockedTransfers(bool $isBlockedTransfers): self
    {
        $this->isBlockedTransfers = $isBlockedTransfers;

        return $this;
    }

    public function getZipcode(): ?string
    {
        return $this->zipcode;
    }

    public function setZipcode(string $zipcode): self
    {
        $this->zipcode = $zipcode;

        return $this;
    }

    public function getStreetPermanent(): ?string
    {
        return $this->streetPermanent;
    }

    public function setStreetPermanent(string $adressPermanent): self
    {
        $this->streetPermanent = $adressPermanent;

        return $this;
    }

    public function getCityPermanent(): ?string
    {
        return $this->cityPermanent;
    }

    public function setCityPermanent(string $cityPermanent): self
    {
        $this->cityPermanent = $cityPermanent;

        return $this;
    }

    public function getZipcodePermanent(): ?string
    {
        return $this->zipcodePermanent;
    }

    public function setZipcodePermanent(string $zipcodePermanent): self
    {
        $this->zipcodePermanent = $zipcodePermanent;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getCountryPermanent(): ?string
    {
        return $this->countryPermanent;
    }

    public function setCountryPermanent(string $countryPermanent): self
    {
        $this->countryPermanent = $countryPermanent;

        return $this;
    }

    public function getVerificationCode(): ?int
    {
        return $this->verificationCode;
    }

    public function setVerificationCode(?int $verificationCode): self
    {
        $this->verificationCode = $verificationCode;

        return $this;
    }

    /**
     * @return Collection|UserAddressSettings[]
     */
    public function getUserAddressSettings(): Collection
    {
        return $this->userAddressSettings;
    }

    public function addUserAddressSetting(UserAddressSettings $userAddressSetting): self
    {
        if (!$this->userAddressSettings->contains($userAddressSetting)) {
            $this->userAddressSettings[] = $userAddressSetting;
            $userAddressSetting->setUser($this);
        }

        return $this;
    }

    public function removeUserAddressSetting(UserAddressSettings $userAddressSetting): self
    {
        if ($this->userAddressSettings->contains($userAddressSetting)) {
            $this->userAddressSettings->removeElement($userAddressSetting);
            // set the owning side to null (unless already changed)
            if ($userAddressSetting->getUser() === $this) {
                $userAddressSetting->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserPasswordSettings[]
     */
    public function getUserPasswordSettings(): Collection
    {
        return $this->userPasswordSettings;
    }

    public function addUserPasswordSetting(UserPasswordSettings $userPasswordSetting): self
    {
        if (!$this->userPasswordSettings->contains($userPasswordSetting)) {
            $this->userPasswordSettings[] = $userPasswordSetting;
            $userPasswordSetting->setUser($this);
        }

        return $this;
    }

    public function removeUserPasswordSetting(UserPasswordSettings $userPasswordSetting): self
    {
        if ($this->userPasswordSettings->contains($userPasswordSetting)) {
            $this->userPasswordSettings->removeElement($userPasswordSetting);
            // set the owning side to null (unless already changed)
            if ($userPasswordSetting->getUser() === $this) {
                $userPasswordSetting->setUser(null);
            }
        }

        return $this;
    }

}
