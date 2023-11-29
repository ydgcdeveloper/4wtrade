<?php

namespace App\Entity;

use App\Repository\CoinsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CoinsRepository::class)
 */
class Coins
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $abbr;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $address;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $mininvest;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $mindeposit;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $minwithdraw;

    /**
     * @ORM\Column(type="float", nullable=false)
     */
    private $withdrawfee;

    /**
     * @ORM\OneToMany(targetEntity=Deposit::class, mappedBy="coin")
     */
    private $deposits;

    /**
     * @ORM\OneToMany(targetEntity=Withdrawl::class, mappedBy="coin")
     */
    private $withdrawls;

    /**
     * @ORM\OneToMany(targetEntity=Investment::class, mappedBy="coin")
     */
    private $investments;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $web;

    /**
     * @ORM\ManyToOne(targetEntity=user::class)
     */
    private $actionby;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $inwallet;

    public function __construct()
    {
        $this->deposits = new ArrayCollection();
        $this->withdrawls = new ArrayCollection();
        $this->investments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAbbr(): ?string
    {
        return $this->abbr;
    }

    public function setAbbr(string $abbr): self
    {
        $this->abbr = $abbr;

        return $this;
    }

    public function getInvestment(): ?Investment
    {
        return $this->investment;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getMininvest(): ?float
    {
        return $this->mininvest;
    }

    public function setMininvest(?float $mininvest): self
    {
        $this->mininvest = $mininvest;

        return $this;
    }

    public function getMindeposit(): ?float
    {
        return $this->mindeposit;
    }

    public function setMindeposit(?float $mindeposit): self
    {
        $this->mindeposit = $mindeposit;

        return $this;
    }

    public function getMinwithdraw(): ?float
    {
        return $this->minwithdraw;
    }

    public function setMinwithdraw(?float $minwithdraw): self
    {
        $this->minwithdraw = $minwithdraw;

        return $this;
    }

    public function getWithdrawfee(): ?float
    {
        return $this->withdrawfee;
    }

    public function setWithdrawfee(?float $withdrawfee): self
    {
        $this->withdrawfee = $withdrawfee;

        return $this;
    }

    /**
     * @return Collection|Deposit[]
     */
    public function getDeposits(): Collection
    {
        return $this->deposits;
    }

    public function addDeposit(Deposit $deposit): self
    {
        if (!$this->deposits->contains($deposit)) {
            $this->deposits[] = $deposit;
            $deposit->setCoin($this);
        }

        return $this;
    }

    public function removeDeposit(Deposit $deposit): self
    {
        if ($this->deposits->removeElement($deposit)) {
            // set the owning side to null (unless already changed)
            if ($deposit->getCoin() === $this) {
                $deposit->setCoin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Withdrawl[]
     */
    public function getWithdrawls(): Collection
    {
        return $this->withdrawls;
    }

    public function addWithdrawl(Withdrawl $withdrawl): self
    {
        if (!$this->withdrawls->contains($withdrawl)) {
            $this->withdrawls[] = $withdrawl;
            $withdrawl->setCoin($this);
        }

        return $this;
    }

    public function removeWithdrawl(Withdrawl $withdrawl): self
    {
        if ($this->withdrawls->removeElement($withdrawl)) {
            // set the owning side to null (unless already changed)
            if ($withdrawl->getCoin() === $this) {
                $withdrawl->setCoin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Investment[]
     */
    public function getInvestments(): Collection
    {
        return $this->investments;
    }

    public function addInvestment(Investment $investment): self
    {
        if (!$this->investments->contains($investment)) {
            $this->investments[] = $investment;
            $investment->setCoin($this);
        }

        return $this;
    }

    public function removeInvestment(Investment $investment): self
    {
        if ($this->investments->removeElement($investment)) {
            // set the owning side to null (unless already changed)
            if ($investment->getCoin() === $this) {
                $investment->setCoin(null);
            }
        }

        return $this;
    }

    public function getWeb(): ?string
    {
        return $this->web;
    }

    public function setWeb(?string $web): self
    {
        $this->web = $web;

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

    public function getInwallet(): ?float
    {
        return $this->inwallet;
    }

    public function setInwallet(?float $inwallet): self
    {
        $this->inwallet = $inwallet;

        return $this;
    }

}
