<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserAddressSettingsRepository")
 */
class UserAddressSettings
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="userAddressSettings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $oldStreet;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $oldZipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $oldCity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $oldCountry;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $newStreet;

    /**
     * @ORM\Column(type="string", length=6)
     */
    private $newZipcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $newCity;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $newCountry;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSuccess = 0;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $hash;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getOldStreet(): ?string
    {
        return $this->oldStreet;
    }

    public function setOldStreet(string $oldStreet): self
    {
        $this->oldStreet = $oldStreet;

        return $this;
    }

    public function getOldZipcode(): ?string
    {
        return $this->oldZipcode;
    }

    public function setOldZipcode(string $oldZipcode): self
    {
        $this->oldZipcode = $oldZipcode;

        return $this;
    }

    public function getOldCity(): ?string
    {
        return $this->oldCity;
    }

    public function setOldCity(string $oldCity): self
    {
        $this->oldCity = $oldCity;

        return $this;
    }

    public function getOldCountry(): ?string
    {
        return $this->oldCountry;
    }

    public function setOldCountry(string $oldCountry): self
    {
        $this->oldCountry = $oldCountry;

        return $this;
    }

    public function getNewStreet(): ?string
    {
        return $this->newStreet;
    }

    public function setNewStreet(string $newStreet): self
    {
        $this->newStreet = $newStreet;

        return $this;
    }

    public function getNewZipcode(): ?string
    {
        return $this->newZipcode;
    }

    public function setNewZipcode(string $newZipcode): self
    {
        $this->newZipcode = $newZipcode;

        return $this;
    }

    public function getNewCity(): ?string
    {
        return $this->newCity;
    }

    public function setNewCity(string $newCity): self
    {
        $this->newCity = $newCity;

        return $this;
    }

    public function getNewCountry(): ?string
    {
        return $this->newCountry;
    }

    public function setNewCountry(string $newCountry): self
    {
        $this->newCountry = $newCountry;

        return $this;
    }

    public function getIsSuccess(): ?bool
    {
        return $this->isSuccess;
    }

    public function setIsSuccess(bool $isSuccess): self
    {
        $this->isSuccess = $isSuccess;

        return $this;
    }

    public function getHash(): ?string
    {
        return $this->hash;
    }

    public function setHash(string $hash): self
    {
        $this->hash = $hash;

        return $this;
    }
}
