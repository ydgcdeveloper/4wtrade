<?php

namespace App\Entity;

use App\Repository\UtilRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UtilRepository::class)
 */
class Util
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=user::class, cascade={"persist", "remove"})
     */
    private $editedby;

    /**
     * @ORM\Column(type="float")
     */
    private $uplimit;

    /**
     * @ORM\Column(type="float")
     */
    private $downlimit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lastpaymentdate;

    /**
     * @ORM\OneToOne(targetEntity=user::class, cascade={"persist", "remove"})
     */
    private $paymentby;

    /**
     * @ORM\Column(type="float")
     */
    private $lastpayment;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEditedby(): ?user
    {
        return $this->editedby;
    }

    public function setEditedby(?user $editedby): self
    {
        $this->editedby = $editedby;

        return $this;
    }

    public function getUplimit(): ?float
    {
        return $this->uplimit;
    }

    public function setUplimit(float $uplimit): self
    {
        $this->uplimit = $uplimit;

        return $this;
    }

    public function getDownlimit(): ?float
    {
        return $this->downlimit;
    }

    public function setDownlimit(float $downlimit): self
    {
        $this->downlimit = $downlimit;

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

    public function getLastpaymentdate(): ?\DateTimeInterface
    {
        return $this->lastpaymentdate;
    }

    public function setLastpaymentdate(?\DateTimeInterface $lastpaymentdate): self
    {
        $this->lastpaymentdate = $lastpaymentdate;

        return $this;
    }

    public function getPaymentby(): ?user
    {
        return $this->paymentby;
    }

    public function setPaymentby(?user $paymentby): self
    {
        $this->paymentby = $paymentby;

        return $this;
    }

    public function getLastpayment(): ?float
    {
        return $this->lastpayment;
    }

    public function setLastpayment(float $lastpayment): self
    {
        $this->lastpayment = $lastpayment;

        return $this;
    }
}
