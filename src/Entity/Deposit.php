<?php

namespace App\Entity;

use App\Repository\DepositRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DepositRepository::class)
 */
class Deposit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="deposits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $userid;

    /**
     * @ORM\Column(type="float" , nullable=true)
     */
    private $amount;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $transhash;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Coins::class, inversedBy="deposits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $coin;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="depoactionby")
     */
    private $actionby;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserid(): ?user
    {
        return $this->userid;
    }

    public function setUserid(?user $userid): self
    {
        $this->userid = $userid;

        return $this;
    }
    
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getTranshash(): ?string
    {
        return $this->transhash;
    }

    public function setTranshash(string $transhash): self
    {
        $this->transhash = $transhash;

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

    public function getCoin(): ?coins
    {
        return $this->coin;
    }

    public function setCoin(?coins $coin): self
    {
        $this->coin = $coin;

        return $this;
    }

    public function getActionby(): ?user
    {
        return $this->actionby;
    }

    public function setActionby(?user $actionby): self
    {
        $this->actionby = $actionby;

        return $this;
    }
}
