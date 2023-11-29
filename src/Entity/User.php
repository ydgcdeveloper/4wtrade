<?php

namespace App\Entity;

use App\Repository\UserRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\JoinColumn;
use Exception;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="role", type="string", length=20, nullable=false)
     */
    private $role = "ROLE_USER";

    public function getRole()
    {
        return $this->role;
    }

    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;


    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=30, nullable=false)
     */
    private $username;

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="hash", type="string", length=50, nullable=false)
     */
    private $hash;

    public function getHash()
    {
        return $this->hash;
    }

    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @var int
     *
     * @ORM\Column(name="referrals", type="integer", nullable=false)
     */
    private $referrals = 0;

    public function getReferrals()
    {
        return $this->referrals;
    }

    public function setReferrals($referrals)
    {
        $this->referrals = $referrals;
    }

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active = false;

    public function isActive()
    {
        return $this->active;
    }

    public function setIsActive($active)
    {
        $this->active = $active;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="emailhash", type="string", length=50, nullable=false)
     */
    private $emailhash;

    public function getEmailHash()
    {
        return $this->emailhash;
    }

    public function setEmailHash($emailhash)
    {
        $this->emailhash = $emailhash;
    }

    /**
     * @ORM\OneToOne(targetEntity=Wallet::class, mappedBy="userid", cascade={"persist", "remove"})
     */
    private $wallet;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="refusers")
     */
    private $referal;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="referal")
     */
    private $refusers;

    /**
     * @ORM\Column(type="array")
     * [BTC, ETH, USDT, LTC, XRP, BCH, TRX, DOGE, DASH, NANO]
     */
    private $commission = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastlogin;

    /**
     * @ORM\Column(type="datetime")
     */
    private $lastlogout;

    /**
     * @ORM\OneToMany(targetEntity=Deposit::class, mappedBy="userid")
     */
    private $deposits;

    /**
     * @ORM\OneToMany(targetEntity=Withdrawl::class, mappedBy="userid")
     */
    private $withdrawls;

    /**
     * @ORM\OneToMany(targetEntity=Investment::class, mappedBy="userid")
     */
    private $investments;

    /**
     * @ORM\ManyToMany(targetEntity=History::class, mappedBy="userid")
     */
    private $histories;

    /**
     * @ORM\OneToMany(targetEntity=Deposit::class, mappedBy="actionby")
     */
    private $depoactionby;

    /**
     * @ORM\OneToMany(targetEntity=Withdrawl::class, mappedBy="actionby")
     */
    private $withactionby;

    /**
     * @ORM\Column(type="array")
     *  [BTC, ETH, USDT, LTC, XRP, BCH, TRX, DOGE, DASH, NANO]
     */
    private $totalearnings = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);

    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="paidby")
     */
    private $payments;

    public function __construct()
    {
        $this->refusers = new ArrayCollection();
        $this->deposits = new ArrayCollection();
        $this->withdrawls = new ArrayCollection();
        $this->investments = new ArrayCollection();
        $this->histories = new ArrayCollection();
        $this->depoactionby = new ArrayCollection();
        $this->withactionby = new ArrayCollection();
        $this->payments = new ArrayCollection();
    }


    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        // guarantee every user at least has ROLE_USER
        $roles[] = $this->getRole();

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
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getWallet(): ?Wallet
    {
        return $this->wallet;
    }

    public function setWallet(Wallet $wallet): self
    {
        // set the owning side of the relation if necessary
        if ($wallet->getUserid() !== $this) {
            $wallet->setUserid($this);
        }

        $this->wallet = $wallet;

        return $this;
    }



    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
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

    public function getReferal(): ?self
    {
        return $this->referal;
    }

    public function setReferal(?self $referal): self
    {
        $this->referal = $referal;

        return $this;
    }

    /**
     * @return Collection|self[]
     */
    public function getRefusers(): Collection
    {
        return $this->refusers;
    }

    public function addRefuser(self $refuser): self
    {
        if (!$this->refusers->contains($refuser)) {
            $this->refusers[] = $refuser;
            $refuser->setReferal($this);
        }

        return $this;
    }

    public function removeRefuser(self $refuser): self
    {
        if ($this->refusers->removeElement($refuser)) {
            // set the owning side to null (unless already changed)
            if ($refuser->getReferal() === $this) {
                $refuser->setReferal(null);
            }
        }

        return $this;
    }

    public function getCommission(): ?array
    {
        return $this->commission;
    }

    public function setCommission(array $commission): self
    {
        $this->commission = $commission;

        return $this;
    }

    public function getLastlogin(): ?\DateTimeInterface
    {
        return $this->lastlogin;
    }

    public function setLastlogin(\DateTimeInterface $lastlogin): self
    {
        $this->lastlogin = $lastlogin;

        return $this;
    }

    public function getLastlogout(): ?\DateTimeInterface
    {
        return $this->lastlogout;
    }

    public function setLastlogout(?\DateTimeInterface $lastlogout): self
    {
        $this->lastlogout = $lastlogout;

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
            $deposit->setUserid($this);
        }

        return $this;
    }

    public function removeDeposit(Deposit $deposit): self
    {
        if ($this->deposits->removeElement($deposit)) {
            // set the owning side to null (unless already changed)
            if ($deposit->getUserid() === $this) {
                $deposit->setUserid(null);
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
            $withdrawl->setUserid($this);
        }

        return $this;
    }

    public function removeWithdrawl(Withdrawl $withdrawl): self
    {
        if ($this->withdrawls->removeElement($withdrawl)) {
            // set the owning side to null (unless already changed)
            if ($withdrawl->getUserid() === $this) {
                $withdrawl->setUserid(null);
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
            $investment->setUserid($this);
        }

        return $this;
    }

    public function removeInvestment(Investment $investment): self
    {
        if ($this->investments->removeElement($investment)) {
            // set the owning side to null (unless already changed)
            if ($investment->getUserid() === $this) {
                $investment->setUserid(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|History[]
     */
    public function getHistories(): Collection
    {
        return $this->histories;
    }

    public function addHistory(History $history): self
    {
        if (!$this->histories->contains($history)) {
            $this->histories[] = $history;
            $history->addUserid($this);
        }

        return $this;
    }

    public function removeHistory(History $history): self
    {
        if ($this->histories->removeElement($history)) {
            $history->removeUserid($this);
        }

        return $this;
    }

    /**
     * @return Collection|Deposit[]
     */
    public function getDepoactionby(): Collection
    {
        return $this->depoactionby;
    }

    public function addDepoactionby(Deposit $depoactionby): self
    {
        if (!$this->depoactionby->contains($depoactionby)) {
            $this->depoactionby[] = $depoactionby;
            $depoactionby->setActionby($this);
        }

        return $this;
    }

    public function removeDepoactionby(Deposit $depoactionby): self
    {
        if ($this->depoactionby->removeElement($depoactionby)) {
            // set the owning side to null (unless already changed)
            if ($depoactionby->getActionby() === $this) {
                $depoactionby->setActionby(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Withdrawl[]
     */
    public function getWithactionby(): Collection
    {
        return $this->withactionby;
    }

    public function addWithactionby(Withdrawl $withactionby): self
    {
        if (!$this->withactionby->contains($withactionby)) {
            $this->withactionby[] = $withactionby;
            $withactionby->setActionby($this);
        }

        return $this;
    }

    public function removeWithactionby(Withdrawl $withactionby): self
    {
        if ($this->withactionby->removeElement($withactionby)) {
            // set the owning side to null (unless already changed)
            if ($withactionby->getActionby() === $this) {
                $withactionby->setActionby(null);
            }
        }
        return $this;
    }

    public function getTodaysEarnings()
    {
        $investments = $this->getInvestments();
        $todaysearning = 0;

        try {
            $curl = curl_init();
            foreach ($investments as $investment) {
                $paidbyday = $investment->getPaidByday();
                foreach ($paidbyday as $paid) {
                    if (date_format($paid[0], 'Y/m/d') == date_format(new \DateTime('now'), 'Y/m/d')) {

                        curl_setopt_array($curl, array(
                            CURLOPT_URL => "https://min-api.cryptocompare.com/data/price?fsym=" . $investment->getCoin()->getAbbr() .
                                "&tsyms=USD",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "GET",
                            CURLOPT_HTTPHEADER => array(
                                "cache-control: no-cache"
                            ),
                        ));

                        $response = curl_exec($curl);
                        $err = curl_error($curl);

                        $json = json_decode($response, true); //because of true, it's in an array
                        $todaysearning += $json['USD'] * $paid[2];
                    }
                }
            }

            curl_close($curl);
        } catch (Exception $e) {
            // throw new Exception("Invalid URL", 0, $e);
        }
        return $todaysearning;
    }

    public function getTotalearnings(): ?array
    {
        return $this->totalearnings;
    }

    public function getTotalearningsInusd()
    {
        $totalinusd = 0;
        $coins = ['BTC', 'ETH', 'USDT', 'LTC', 'XRP', 'BCH', 'TRX', 'DOGE', 'DASH', 'NANO'];

        $length = count($coins);

        try {
            $curl = curl_init();
            for ($index = 0; $index < $length; $index++) {
                $amount = $this->totalearnings[$index];
                if ($amount > 0) {

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://min-api.cryptocompare.com/data/price?fsym=" . $coins[$index] . "&tsyms=USD",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            "cache-control: no-cache"
                        ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    $json = json_decode($response, true); //because of true, it's in an array
                    $totalinusd += $json['USD'] * $amount;
                }
            }

            curl_close($curl);
        } catch (Exception $e) {
            //throw new Exception("Invalid URL", 0, $e);
        }
        return $totalinusd;
    }

    public function getReferalearningsInusd(): float
    {
        $totalinusd = 0;
        $referrals = $this->getRefusers();
        $coins = ['BTC', 'ETH', 'USDT', 'LTC', 'XRP', 'BCH', 'TRX', 'DOGE', 'DASH', 'NANO'];

        try {
            $curl = curl_init();

            foreach ($referrals as $referal) {
                $commission = $referal->getCommission();
                $length = count($coins);
                for ($index = 0; $index < $length; $index++) {
                    $comm = $commission[$index];
                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://min-api.cryptocompare.com/data/price?fsym=" . $coins[$index] . "&tsyms=USD",
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            "cache-control: no-cache"
                        ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    $json = json_decode($response, true); //because of true, it's in an array
                    $totalinusd += $json['USD'] * $comm;
                }
            }

            curl_close($curl);
        } catch (Exception $e) {
            // throw new Exception("Invalid URL", 0, $e);
        }
        return $totalinusd;
    }

    public function setTotalearnings(array $totalearnings): self
    {
        $this->totalearnings = $totalearnings;

        return $this;
    }

    public function addTotalearningsBycoin(array $totalearnings, $coin, $amount)
    {

        switch (strtoupper($coin)) {
            case "BTC":
                $totalearnings[0] += $amount;
                break;
            case "ETH":
                $totalearnings[1] += $amount;
                break;
            case "USDT":
                $totalearnings[2] += $amount;
                break;
            case "LTC":
                $totalearnings[3] += $amount;
                break;
            case "XRP":
                $totalearnings[4] += $amount;
                break;
            case "BCH":
                $totalearnings[5] += $amount;
                break;
            case "TRX":
                $totalearnings[6] += $amount;
                break;
            case "DOGE":
                $totalearnings[7] += $amount;
                break;
            case "DASH":
                $totalearnings[8] += $amount;
                break;
            case "NANO":
                $totalearnings[9] += $amount;
                break;
            default:
                break;
        }

        $this->totalearnings = $totalearnings;
    }

    public function addCommissionBycoin(array $commission, $coin, $amount)
    {

        switch (strtoupper($coin)) {
            case "BTC":
                $commission[0] += $amount;
                break;
            case "ETH":
                $commission[1] += $amount;
                break;
            case "USDT":
                $commission[2] += $amount;
                break;
            case "LTC":
                $commission[3] += $amount;
                break;
            case "XRP":
                $commission[4] += $amount;
                break;
            case "BCH":
                $commission[5] += $amount;
                break;
            case "TRX":
                $commission[6] += $amount;
                break;
            case "DOGE":
                $commission[7] += $amount;
                break;
            case "DASH":
                $commission[8] += $amount;
                break;
            case "NANO":
                $commission[9] += $amount;
                break;
            default:
                break;
        }

        $this->commission = $commission;
    }

    //Devuelve un array con todos los depositos aprovados de tus referidos
    public function getRefersDepos()
    {

        $depos = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $referrals = $this->getRefusers();

        foreach ($referrals as $referal) {
            $deposits = $referal->getDeposits();
            foreach ($deposits as $deposit) {
                if ($deposit->getStatus() == "Done") {
                    $coin = $deposit->getCoin()->getAbbr();
                    $amount = $deposit->getAmount();
                    switch (strtoupper($coin)) {
                        case "BTC":
                            $depos[0] += $amount;
                            break;
                        case "ETH":
                            $depos[1] += $amount;
                            break;
                        case "USDT":
                            $depos[2] += $amount;
                            break;
                        case "LTC":
                            $depos[3] += $amount;
                            break;
                        case "XRP":
                            $depos[4] += $amount;
                            break;
                        case "BCH":
                            $depos[5] += $amount;
                            break;
                        case "TRX":
                            $depos[6] += $amount;
                            break;
                        case "DOGE":
                            $depos[7] += $amount;
                            break;
                        case "DASH":
                            $depos[8] += $amount;
                            break;
                        case "NANO":
                            $depos[9] += $amount;
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        return $depos;
    }

    //Devuelve un array con todos los retiros aprovados de tus referidos
    public function getRefersWithds()
    {

        $withds = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        $referrals = $this->getRefusers();

        foreach ($referrals as $referal) {
            $withdrawls = $referal->getWithdrawls();
            foreach ($withdrawls as $withdrawl) {
                if ($withdrawl->getStatus() == "Done") {
                    $coin = $withdrawl->getCoin()->getAbbr();
                    $amount = $withdrawl->getAmount();
                    switch (strtoupper($coin)) {
                        case "BTC":
                            $withds[0] += $amount;
                            break;
                        case "ETH":
                            $withds[1] += $amount;
                            break;
                        case "USDT":
                            $withds[2] += $amount;
                            break;
                        case "LTC":
                            $withds[3] += $amount;
                            break;
                        case "XRP":
                            $withds[4] += $amount;
                            break;
                        case "BCH":
                            $withds[5] += $amount;
                            break;
                        case "TRX":
                            $withds[6] += $amount;
                            break;
                        case "DOGE":
                            $withds[7] += $amount;
                            break;
                        case "DASH":
                            $withds[8] += $amount;
                            break;
                        case "NANO":
                            $withds[9] += $amount;
                            break;
                        default:
                            break;
                    }
                }
            }
        }
        return $withds;
    }

    //Devuelve un array con todos los depositos aprovados del usuario
    public function getDepos()
    {

        $depos = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

            $deposits = $this->getDeposits();
            foreach ($deposits as $deposit) {
                if ($deposit->getStatus() == "Done") {
                    $coin = $deposit->getCoin()->getAbbr();
                    $amount = $deposit->getAmount();
                    switch (strtoupper($coin)) {
                        case "BTC":
                            $depos[0] += $amount;
                            break;
                        case "ETH":
                            $depos[1] += $amount;
                            break;
                        case "USDT":
                            $depos[2] += $amount;
                            break;
                        case "LTC":
                            $depos[3] += $amount;
                            break;
                        case "XRP":
                            $depos[4] += $amount;
                            break;
                        case "BCH":
                            $depos[5] += $amount;
                            break;
                        case "TRX":
                            $depos[6] += $amount;
                            break;
                        case "DOGE":
                            $depos[7] += $amount;
                            break;
                        case "DASH":
                            $depos[8] += $amount;
                            break;
                        case "NANO":
                            $depos[9] += $amount;
                            break;
                        default:
                            break;
                    }
                }
        }
        return $depos;
    }

    //Devuelve un array con todos los retiros aprovados del usuario
    public function getWithds()
    {

        $withds = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

            $withdrawls = $this->getWithdrawls();
            foreach ($withdrawls as $withdrawl) {
                if ($withdrawl->getStatus() == "Done") {
                    $coin = $withdrawl->getCoin()->getAbbr();
                    $amount = $withdrawl->getAmount();
                    switch (strtoupper($coin)) {
                        case "BTC":
                            $withds[0] += $amount;
                            break;
                        case "ETH":
                            $withds[1] += $amount;
                            break;
                        case "USDT":
                            $withds[2] += $amount;
                            break;
                        case "LTC":
                            $withds[3] += $amount;
                            break;
                        case "XRP":
                            $withds[4] += $amount;
                            break;
                        case "BCH":
                            $withds[5] += $amount;
                            break;
                        case "TRX":
                            $withds[6] += $amount;
                            break;
                        case "DOGE":
                            $withds[7] += $amount;
                            break;
                        case "DASH":
                            $withds[8] += $amount;
                            break;
                        case "NANO":
                            $withds[9] += $amount;
                            break;
                        default:
                            break;
                    }
                }
        }
        return $withds;
    }
    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setPaidby($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getPaidby() === $this) {
                $payment->setPaidby(null);
            }
        }

        return $this;
    }
}
