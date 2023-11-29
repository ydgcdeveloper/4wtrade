<?php

namespace App\Entity;

use App\Repository\InvestmentRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=InvestmentRepository::class)
 */
class Investment
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="float")
     */
    private $paid = 0;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="array")
     */
    private $paidByday = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity=user::class, inversedBy="investments")
     */
    private $userid;

    /**
     * @ORM\ManyToOne(targetEntity=Coins::class, inversedBy="investments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $coin;

    /**
     * @ORM\Column(type="float")
     */
    private $goal;

    /**
     * @ORM\Column(type="float")
     */
    private $paidpercent = 0;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPaid(): ?float
    {
        return $this->paid;
    }

    public function setPaid(float $paid): self
    {
        $this->paid = $paid;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPaidByday(): ?array
    {
        return $this->paidByday;
    }

    public function setPaidByday(array $paidByday): self
    {
        array_push($this->paidByday , $paidByday);

        return $this;
    }

    public function setPaidBydayData(float $percent, float $amount)
    {
      $paid = array( new \DateTime(), $percent, $amount );

      return $paid;
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

    public function getUserid(): ?user
    {
        return $this->userid;
    }

    public function setUserid(?user $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getCoin(): ?Coins
    {
        return $this->coin;
    }

    public function setCoin(?Coins $coin): self
    {
        $this->coin = $coin;

        return $this;
    }

    public function getGoal(): ?float
    {
        return $this->goal;
    }

    public function setGoal(float $goal): self
    {
        $this->goal = $goal;

        return $this;
    }

    public function getPaidpercent(): ?float
    {
        return $this->paidpercent;
    }

    public function setPaidpercent(float $paidpercent): self
    {
        $this->paidpercent = $paidpercent;

        return $this;
    }

}
