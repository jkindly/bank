<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TransferRepository")
 */
class Transfer
{
    use TimestampableEntity;



    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    // todo change length field senderAccountNumber
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $senderAccountNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Proszę wypełnić pole odbiorcy")
     */
    private $receiverName;

    // todo change length field receiverAccountNumber
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Proszę wypełnić nr rachunku odbiorcy")
     */
    private $receiverAccountNumber;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2)
     * @Assert\NotBlank(message="Proszę wprowadzić kwotę")
     */
    private $amount;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $title;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="transfers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $receiverAddress;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $receiverCity;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSuccess;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $status;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSenderAccountNumber(): ?string
    {
        return $this->senderAccountNumber;
    }

    public function setSenderAccountNumber(string $senderAccountNumber): self
    {
        $this->senderAccountNumber = $senderAccountNumber;

        return $this;
    }

    public function getReceiverAccountNumber(): ?string
    {
        return $this->receiverAccountNumber;
    }

    public function setReceiverAccountNumber(string $receiverAccountNumber): self
    {
        $this->receiverAccountNumber = $receiverAccountNumber;

        return $this;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function setAmount($amount): self
    {
        $this->amount = $amount;

        return $this;
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

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getReceiverName(): ?string
    {
        return $this->receiverName;
    }

    public function setReceiverName(string $receiverName): self
    {
        $this->receiverName = $receiverName;

        return $this;
    }

    public function getReceiverAddress(): ?string
    {
        return $this->receiverAddress;
    }

    public function setReceiverAddress(?string $receiverAddress): self
    {
        $this->receiverAddress = $receiverAddress;

        return $this;
    }

    public function getReceiverCity(): ?string
    {
        return $this->receiverCity;
    }

    public function setReceiverCity(?string $receiverCity): self
    {
        $this->receiverCity = $receiverCity;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }
}
