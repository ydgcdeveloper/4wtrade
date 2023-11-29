<?php

namespace App\Entity;

use App\Repository\WalletRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=WalletRepository::class)
 */
class Wallet
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
    private $btc = 0.00000000;

    /**
     * @ORM\Column(type="float")
     */
    private $eth = 0.00000;

    /**
     * @ORM\Column(type="float")
     */
    private $usdt = 0.00;

    /**
     * @ORM\Column(type="float")
     */
    private $ltc = 0.0000;

    /**
     * @ORM\Column(type="float")
     */
    private $xrp = 0.00;

    /**
     * @ORM\Column(type="float")
     */
    private $bch = 0.00000;

    /**
     * @ORM\Column(type="float")
     */
    private $trx = 0.00;

    /**
     * @ORM\Column(type="float")
     */
    private $doge = 0.00;

    /**
     * @ORM\Column(type="float")
     */
    private $dash = 0.00000;

    /**
     * @ORM\Column(type="float")
     */
    private $nano = 0.0000;

    /**
     * @ORM\OneToOne(targetEntity=User::class, inversedBy="wallet", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $userid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBtc(): ?float
    {
        return $this->btc;
    }

    public function setBtc(float $btc): self
    {
        $this->btc = $btc;

        return $this;
    }

    public function getEth(): ?float
    {
        return $this->eth;
    }

    public function setEth(float $eth): self
    {
        $this->eth = $eth;

        return $this;
    }

    public function getUsdt(): ?float
    {
        return $this->usdt;
    }

    public function setUsdt(float $usdt): self
    {
        $this->usdt = $usdt;

        return $this;
    }

    public function getLtc(): ?float
    {
        return $this->ltc;
    }

    public function setLtc(float $ltc): self
    {
        $this->ltc = $ltc;

        return $this;
    }

    public function getXrp(): ?float
    {
        return $this->xrp;
    }

    public function setXrp(float $xrp): self
    {
        $this->xrp = $xrp;

        return $this;
    }

    public function getBch(): ?float
    {
        return $this->bch;
    }

    public function setBch(float $bch): self
    {
        $this->bch = $bch;

        return $this;
    }

    public function getTrx(): ?float
    {
        return $this->trx;
    }

    public function setTrx(float $trx): self
    {
        $this->trx = $trx;

        return $this;
    }

    public function getDoge(): ?float
    {
        return $this->doge;
    }

    public function setDoge(float $doge): self
    {
        $this->doge = $doge;

        return $this;
    }

    public function getDash(): ?float
    {
        return $this->dash;
    }

    public function setDash(float $dash): self
    {
        $this->dash = $dash;

        return $this;
    }

    public function getNano(): ?float
    {
        return $this->nano;
    }

    public function setNano(float $nano): self
    {
        $this->nano = $nano;

        return $this;
    }

    public function getUserid(): ?User
    {
        return $this->userid;
    }

    public function setUserid(User $userid): self
    {
        $this->userid = $userid;

        return $this;
    }

    public function getByCoinAbbr($abbr = 'BTC')
    {
        switch ($abbr) {
            case 'BTC':
                return $this->getBtc();
                break;
            case 'ETH':
                return $this->getEth();
                break;
            case 'USDT':
                return $this->getUsdt();
                break;
            case 'LTC':
                return $this->getLtc();
                break;
            case 'XRP':
                return $this->getXrp();
                break;
            case 'BCH':
                return $this->getBch();
                break;
            case 'TRX':
                return $this->getTrx();
                break;
            case 'DOGE':
                return $this->getDoge();
                break;
            case 'DASH':
                return $this->getDash();
                break;
            case 'NANO':
                return $this->getNano();
                break;
            default:
                return $this->getBtc();
                break;
        }
    }

    public function setByCoinAbbr($abbr = 'BTC', float $value)
    {
        switch ($abbr) {
            case 'BTC':
                $this->setBtc($value);
                break;
            case 'ETH':
                $this->setEth($value);
                break;
            case 'USDT':
                $this->setUsdt($value);
                break;
            case 'LTC':
                $this->setLtc($value);
                break;
            case 'XRP':
                $this->setXrp($value);
                break;
            case 'BCH':
                $this->setBch($value);
                break;
            case 'TRX':
                $this->setTrx($value);
                break;
            case 'DOGE':
                $this->setDoge($value);
                break;
            case 'DASH':
                $this->setDash($value);
                break;
            case 'NANO':
                $this->setNano($value);
                break;
            default:
                break;
        }
    }
}
