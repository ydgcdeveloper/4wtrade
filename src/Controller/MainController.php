<?php

namespace App\Controller;

use App\Entity\Deposit;
use App\Entity\History;
use App\Entity\Investment;
use App\Entity\Payment;
use App\Entity\Withdrawl;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class MainController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function dashboardAction()
    {
        $em = $this->em;
        $util = $em->getRepository('App:Util')->find(1);
        $payments = $em->getRepository('App:Payment')->findAll();
        $payment = $em->getRepository('App:Payment')->findAll();
        $pay = [];
        foreach ($payments as $payment) {
            array_push($pay, $payment->toJson());
        }
        return $this->render('dashboard.html.twig', array('util' => $util, 'payments' => json_encode($pay, true), 'payment' => $payment));
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('dashboard');
        }

        $referParent = null;
        $referParentUsername = null;

        if ($request->isMethod('GET')) {
            $referParent =  $request->get('id');
            if ($referParent != null) {
                $em = $this->em;
                $users_repo = $em->getRepository('App:User');
                if ($users_repo->findOneBy(['username' => $referParent]) != null) {
                    $referParentUsername = $users_repo->findOneBy(['username' => $referParent])->getUsername();
                } else {
                    $referParent = null;
                }
            }
        }

        return $this->render('register.html.twig', array('parentusername' => $referParentUsername, 'referparent' => $referParent));
    }

    /**
     * @Route("dashboard/changepass", name="changepass")
     */
    public function profileAction()
    {
        return $this->render('change-password.html.twig');
    }

    /**
     * @Route("dashboard/referralP" , name="referralP")
     */
    public function referralPAction()
    {
        return $this->render('referrals.html.twig');
    }

    /**
     * @Route("dashboard/depositView" , name="depositV")
     */
    public function depositView()
    {
        $em = $this->em;
        $coins = $em->getRepository('App:Coins')->findAll();

        return $this->render('deposit.html.twig', array('coins' => $coins));
    }

    /**
     * @Route("dashboard/walletView" , name="walletV")
     */
    public function walletView()
    {
        $em = $this->em;
        $coins = $em->getRepository('App:Coins')->findAll();

        return $this->render('wallet.html.twig', array('coins' => $coins));
    }

    /**
     * @Route("dashboard/withdrawView" , name="withdrawV")
     */
    public function withdrawView()
    {
        $em = $this->em;
        $coins = $em->getRepository('App:Coins')->findAll();

        return $this->render('withdraw.html.twig', array('coins' => $coins));
    }

    /**
     * @Route("dashboard/deposit", name="deposit")
     */
    public function depositAction(Request $request)
    {
        $notice = "Could not make the deposit!";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;

            $user = $this->getUser();

            $coin = $request->get('coin');
            $transhash = $request->get('transhash');

            $pattern = "/\s/";
            if (preg_match($pattern, $transhash)) {
                $this->addFlash("warning", "Invalid format");
                return $this->redirect($this->generateUrl('dashboard'));
            }

            if ($user != null) {
                $coin_repo = $em->getRepository('App:Coins');
                $coinr = $coin_repo->findOneBy(['abbr' => $coin]);
                if ($coinr != null) {

                    $deposit = new Deposit();
                    $deposit->setUserid($user);
                    $deposit->setTranshash($transhash);
                    $deposit->setStatus('Processing');
                    $deposit->setCoin($coinr);
                    $deposit->setDate(new \DateTime());
                    $em->persist($deposit);

                    $history = new History();
                    $history->addUserid($user);
                    $history->setDate(new \DateTime());
                    $history->setDescription("New deposit of " . $coin . " is in process");
                    $history->setType('info');
                    $history->setTitle('Deposit');
                    $history->setCssclass('fa-info');
                    $em->persist($history);

                    $em->flush();

                    $notice = "The deposit was successfully done";
                    $type = "success";
                }
            }
        }
        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('depositV'));
    }

    /**
     * @Route("dashboard/invest", name="invest")
     */
    public function investAction(Request $request)
    {

        $notice = "Could not make the investment!";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;

            $user = $this->getUser();

            $coin = $request->get('coin');
            $investamount = $request->get('investamount');

            if (!is_numeric($investamount)) {
                $this->addFlash("warning", "Invalid format");
                return $this->redirect($this->generateUrl('walletV'));
            }

            $coin_repo = $em->getRepository('App:Coins')->findAll();

            $wallet = $user->getWallet();
            if ($wallet != null) {
                $flag = true;
                switch (strtoupper($coin)) {
                    case "BTC":
                        if ($wallet->getBtc() < $investamount || $investamount < $coin_repo[0]->getMininvest()) {
                            $flag = false;
                        } else {
                            $wallet->setBtc($wallet->getBtc() - $investamount);
                        }
                        break;
                    case "ETH":
                        if ($wallet->getEth() < $investamount || $investamount < $coin_repo[1]->getMininvest()) {
                            $flag = false;
                        } else {
                            $wallet->setEth($wallet->getEth() - $investamount);
                        }
                        break;
                    case "USDT":
                        if ($wallet->getUsdt() < $investamount || $investamount < $coin_repo[2]->getMininvest()) {
                            $flag = false;
                        } else {
                            $wallet->setUsdt($wallet->getUsdt() - $investamount);
                        }
                        break;
                    case "LTC":
                        if ($wallet->getLtc() < $investamount || $investamount < $coin_repo[3]->getMininvest()) {
                            $flag = false;
                        } else {
                            $wallet->setLtc($wallet->getLtc() - $investamount);
                        }
                        break;
                    case "XRP":
                        if ($wallet->getXrp() < $investamount || $investamount < $coin_repo[4]->getMininvest()) {
                            $flag = false;
                        } else {
                            $wallet->setXrp($wallet->getXrp() - $investamount);
                        }
                        break;
                    case "BCH":
                        if ($wallet->getBch() < $investamount || $investamount < $coin_repo[5]->getMininvest()) {
                            $flag = false;
                        } else {
                            $wallet->setBch($wallet->getBch() - $investamount);
                        }
                        break;
                    case "TRX":
                        if ($wallet->getTrx() < $investamount || $investamount < $coin_repo[6]->getMininvest()) {
                            $flag = false;
                        } else {
                            $wallet->setTrx($wallet->getTrx() - $investamount);
                        }
                        break;
                    case "DOGE":
                        if ($wallet->getDoge() < $investamount || $investamount < $coin_repo[7]->getMininvest()) {
                            $flag = false;
                        } else {
                            $wallet->setDoge($wallet->getDoge() - $investamount);
                        }
                        break;
                    case "DASH":
                        if ($wallet->getDash() < $investamount || $investamount < $coin_repo[8]->getMininvest()) {
                            $flag = false;
                        } else {
                            $wallet->setDash($wallet->getDash() - $investamount);
                        }
                        break;
                    case "NANO":
                        if ($wallet->getNano() < $investamount || $investamount < $coin_repo[9]->getMininvest()) {
                            $flag = false;
                        } else {
                            $wallet->setNano($wallet->getNano() - $investamount);
                        }
                        break;
                    default:
                        $flag = false;
                        $this->addFlash("warning", "Something went wrong, try again");
                        return $this->redirect($this->generateUrl('walletV'));
                        break;
                }
                if (!$flag) {
                    $this->addFlash("warning", "Not enough balance");
                    return $this->redirect($this->generateUrl('walletV'));
                }
            }

            if ($user != null) {
                $coin_repo = $em->getRepository('App:Coins');
                $coinr = $coin_repo->findOneBy(['abbr' => $coin]);
                if ($coinr != null) {
                    $investment = new Investment();

                    $investment->setUserid($user);
                    $investment->setStatus(true);
                    $investment->setAmount($investamount);
                    $investment->setGoal($investamount * 2);
                    $investment->setCoin($coinr);
                    $investment->setDate(new \DateTime());
                    $em->persist($investment);

                    $history = new History();
                    $history->addUserid($user);
                    $history->setDate(new \DateTime());
                    $history->setDescription("New investment by " . $investment->getAmount() . $coin);
                    $history->setType('info');
                    $history->setTitle('Investment');
                    $history->setCssclass('fa-info');
                    $em->persist($history);

                    $referal = $user->getReferal();

                    if (!is_null($referal)) {
                        $walletamount = $referal->getWallet()->getByCoinAbbr(strtoupper($investment->getCoin()->getAbbr()));
                        $referal->getWallet()->setByCoinAbbr(strtoupper($investment->getCoin()->getAbbr()), $walletamount + ($investment->getAmount() * 0.05));
                        $referal->addTotalearningsBycoin($referal->getTotalearnings(), $investment->getCoin()->getAbbr(), ($investment->getAmount() * 0.05));
                        $user->addCommissionByCoin($user->getCommission(), $investment->getCoin()->getAbbr(), ($investment->getAmount() * 0.05));

                        $history = new History();
                        $history->addUserid($referal);
                        $history->setDate(new \DateTime());
                        $history->setDescription("New direct benefit of investment by " . ($investment->getAmount() * 0.1) . $investment->getCoin()->getAbbr());
                        $history->setType('info');
                        $history->setTitle('Benefit');
                        $history->setCssclass('fa-info');
                        $em->persist($history);
                    }

                    $em->flush();

                    $notice = "The investment was successfully done";
                    $type = "success";
                }
            }
        }
        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('walletV'));
    }

    /**
     * @Route("dashboard/withdraw", name="withdraw")
     */
    public function withdrawAction(Request $request)
    {

        $notice = "Could not make the withdrawl!";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;

            $user = $this->getUser();

            $coin = $request->get('coin');
            $withamount = $request->get('withamount');
            $withaddress = $request->get('withaddress');

            $pattern = "/\s/";
            if (!is_numeric($withamount) || preg_match($pattern, $withaddress)) {
                $this->addFlash("warning", "Invalid format");
                return $this->redirect($this->generateUrl('withdrawV'));
            }

            $coin_repo = $em->getRepository('App:Coins')->findAll();

            $wallet = $user->getWallet();
            if ($wallet != null) {
                $flag = true;
                switch (strtoupper($coin)) {
                    case "BTC":
                        if ($wallet->getBtc() < $withamount || $withamount < $coin_repo[0]->getMinwithdraw()) {
                            $flag = false;
                        }
                        break;
                    case "ETH":
                        if ($wallet->getEth() < $withamount || $withamount < $coin_repo[1]->getMinwithdraw()) {
                            $flag = false;
                        }
                        break;
                    case "USDT":
                        if ($wallet->getUsdt() < $withamount || $withamount < $coin_repo[2]->getMinwithdraw()) {
                            $flag = false;
                        }
                        break;
                    case "LTC":
                        if ($wallet->getLtc() < $withamount || $withamount < $coin_repo[3]->getMinwithdraw()) {
                            $flag = false;
                        }
                        break;
                    case "XRP":
                        if ($wallet->getXrp() < $withamount || $withamount < $coin_repo[4]->getMinwithdraw()) {
                            $flag = false;
                        }
                        break;
                    case "BCH":
                        if ($wallet->getBch() < $withamount || $withamount < $coin_repo[5]->getMinwithdraw()) {
                            $flag = false;
                        }
                        break;
                    case "TRX":
                        if ($wallet->getTrx() < $withamount || $withamount < $coin_repo[6]->getMinwithdraw()) {
                            $flag = false;
                        }
                        break;
                    case "DOGE":
                        if ($wallet->getDoge() < $withamount || $withamount < $coin_repo[7]->getMinwithdraw()) {
                            $flag = false;
                        }
                        break;
                    case "DASH":
                        if ($wallet->getDash() < $withamount || $withamount < $coin_repo[8]->getMinwithdraw()) {
                            $flag = false;
                        }
                        break;
                    case "NANO":
                        if ($wallet->getNano() < $withamount || $withamount < $coin_repo[9]->getMinwithdraw()) {
                            $flag = false;
                        }
                        break;
                    default:
                        $flag = false;
                        $this->addFlash("warning", "Something went wrong, try again");
                        return $this->redirect($this->generateUrl('withdrawV'));
                        break;
                }
                if (!$flag) {
                    $this->addFlash("warning", "Not enough balance");
                    return $this->redirect($this->generateUrl('withdrawV'));
                }
            }

            if ($user != null) {
                $coin_repo = $em->getRepository('App:Coins');
                $coinr = $coin_repo->findOneBy(['abbr' => $coin]);
                if ($coinr != null) {
                    $withdrawal = new Withdrawl();

                    $withdrawal->setUserid($user);
                    $withdrawal->setStatus('Processing');
                    $withdrawal->setAmount($withamount);
                    $withdrawal->setAddress($withaddress);
                    $withdrawal->setCoin($coinr);
                    $withdrawal->setDate(new \DateTime());
                    $em->persist($withdrawal);

                    $history = new History();
                    $history->addUserid($user);
                    $history->setDate(new \DateTime());
                    $history->setDescription("New withdrawal request by " . $withamount . $coin . " is in process");
                    $history->setType('info');
                    $history->setTitle('Withdrawal');
                    $history->setCssclass('fa-info');
                    $em->persist($history);

                    $em->flush();

                    $notice = "The withdrawal request was successfully done";
                    $type = "success";
                }
            }
        }
        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('withdrawV'));
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404)
     * 
     * @Route("admin/updateinv" , name="updateinvestments")
     */
    public function updateInvestmentsAction(Request $request)
    {
        $notice = "Could not execute the payment!";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;
            $user = $this->getUser();

            $dailypercent = $request->get('dailypercent');

            $util = $em->getRepository('App:Util')->find(1);
            $downlimit = $util->getDownlimit();
            $uplimit = $util->getUplimit();

            $percent = $dailypercent;

            if (is_numeric($percent)) {
                if ($downlimit <= $percent && $uplimit >= $percent) {
                    $investments = $em->getRepository('App:Investment')->findBy(['status' => true]);
                    if ($investments != null && $util != null) {
                        foreach ($investments as  $value) {
                            $all = $value->getAmount();
                            $toPay = (($percent * $all) / 100);
                            $paid = $value->getPaid();
                            $value->setPaid($paid + $toPay);

                            $coin = $value->getCoin();
                            $wallet = $value->getUserid()->getWallet();

                            if ($wallet != null) {
                                switch (strtoupper($coin->getAbbr())) {
                                    case "BTC":
                                        if ($value->getPaid() >= $value->getGoal()) {
                                            $wallet->setBtc($wallet->getBtc() + ($value->getGoal() - $paid));
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[0] += ($value->getGoal() - $paid);
                                            $value->getUserid()->setTotalearnings($earnings);
                                        } else {
                                            $wallet->setBtc($wallet->getBtc() + $toPay);
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[0] += $toPay;
                                            $value->getUserid()->setTotalearnings($earnings);
                                        }
                                        break;
                                    case "ETH":
                                        if ($value->getPaid() >= $value->getGoal()) {
                                            $wallet->setEth($wallet->getEth() + ($value->getGoal() - $paid));
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[1] += ($value->getGoal() - $paid);
                                            $value->getUserid()->setTotalearnings($earnings);
                                        } else {
                                            $wallet->setEth($wallet->getEth() + $toPay);
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[1] += $toPay;
                                            $value->getUserid()->setTotalearnings($earnings);
                                        }
                                        break;
                                    case "USDT":
                                        if ($value->getPaid() >= $value->getGoal()) {
                                            $wallet->setUsdt($wallet->getUsdt() + ($value->getGoal() - $paid));
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[2] += ($value->getGoal() - $paid);
                                            $value->getUserid()->setTotalearnings($earnings);
                                        } else {
                                            $wallet->setUsdt($wallet->getUsdt() + $toPay);
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[2] += $toPay;
                                            $value->getUserid()->setTotalearnings($earnings);
                                        }
                                        break;
                                    case "LTC":
                                        if ($value->getPaid() >= $value->getGoal()) {
                                            $wallet->setLtc($wallet->getLtc() + ($value->getGoal() - $paid));
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[3] += ($value->getGoal() - $paid);
                                            $value->getUserid()->setTotalearnings($earnings);
                                        } else {
                                            $wallet->setLtc($wallet->getLtc() + $toPay);
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[3] += $toPay;
                                            $value->getUserid()->setTotalearnings($earnings);
                                        }
                                        break;
                                    case "XRP":
                                        if ($value->getPaid() >= $value->getGoal()) {
                                            $wallet->setXrp($wallet->getXrp() + ($value->getGoal() - $paid));
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[4] += ($value->getGoal() - $paid);
                                            $value->getUserid()->setTotalearnings($earnings);
                                        } else {
                                            $wallet->setXrp($wallet->getXrp() + $toPay);
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[4] += $toPay;
                                            $value->getUserid()->setTotalearnings($earnings);
                                        }
                                        break;
                                    case "BCH":
                                        if ($value->getPaid() >= $value->getGoal()) {
                                            $wallet->setBch($wallet->getBch() + ($value->getGoal() - $paid));
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[5] += ($value->getGoal() - $paid);
                                            $value->getUserid()->setTotalearnings($earnings);
                                        } else {
                                            $wallet->setBch($wallet->getBch() + $toPay);
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[5] += $toPay;
                                            $value->getUserid()->setTotalearnings($earnings);
                                        }
                                        break;
                                    case "TRX":
                                        if ($value->getPaid() >= $value->getGoal()) {
                                            $wallet->setTrx($wallet->getTrx() + ($value->getGoal() - $paid));
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[6] += ($value->getGoal() - $paid);
                                            $value->getUserid()->setTotalearnings($earnings);
                                        } else {
                                            $wallet->setTrx($wallet->getTrx() + $toPay);
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[6] += $toPay;
                                            $value->getUserid()->setTotalearnings($earnings);
                                        }
                                        break;
                                    case "DOGE":
                                        if ($value->getPaid() >= $value->getGoal()) {
                                            $wallet->setDoge($wallet->getDoge() + ($value->getGoal() - $paid));
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[7] += ($value->getGoal() - $paid);
                                            $value->getUserid()->setTotalearnings($earnings);
                                        } else {
                                            $wallet->setDoge($wallet->getDoge() + $toPay);
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[7] += $toPay;
                                            $value->getUserid()->setTotalearnings($earnings);
                                        }
                                        break;
                                    case "DASH":
                                        if ($value->getPaid() >= $value->getGoal()) {
                                            $wallet->setDash($wallet->getDash() + ($value->getGoal() - $paid));
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[8] += ($value->getGoal() - $paid);
                                            $value->getUserid()->setTotalearnings($earnings);
                                        } else {
                                            $wallet->setDash($wallet->getDash() + $toPay);
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[8] += $toPay;
                                            $value->getUserid()->setTotalearnings($earnings);
                                        }
                                        break;
                                    case "NANO":
                                        if ($value->getPaid() >= $value->getGoal()) {
                                            $wallet->setNano($wallet->getNano() + ($value->getGoal() - $paid));
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[9] += ($value->getGoal() - $paid);
                                            $value->getUserid()->setTotalearnings($earnings);
                                        } else {
                                            $wallet->setNano($wallet->getNano() + $toPay);
                                            $earnings = $value->getUserid()->getTotalearnings();
                                            $earnings[9] += $toPay;
                                            $value->getUserid()->setTotalearnings($earnings);
                                        }
                                        break;
                                    default:
                                        $this->addFlash("warning", "Something went wrong, try again");
                                        return $this->redirect($this->generateUrl('admin'));
                                        break;
                                }
                            }

                            if ($value->getPaid() >= $value->getGoal()) {
                                $value->setPaid($value->getGoal());
                                $payByDay = $value->setPaidBydayData(200 - $value->getPaidpercent(), $value->getGoal() - $paid);
                                $value->setPaidByday($payByDay);
                                $value->setPaidpercent(200);
                                $value->setStatus(false);
                            } else {
                                $payByDay = $value->setPaidBydayData($percent, $toPay);
                                $value->setPaidByday($payByDay);
                                $value->setPaidpercent($value->getPaidpercent() + $percent);
                            }
                        }

                        $payment = new Payment();
                        $payment->setDate(new \DateTime());
                        $payment->setPaidby($user);
                        $payment->setPercent($percent);
                        $em->persist($payment);

                        $util->setPaymentby($user);
                        $util->setLastpayment($percent);
                        $util->setLastpaymentdate(new \DateTime());
                        $em->flush();

                        $notice = "Investments updated successfully";
                        $type = "success";
                    } else {
                        $notice = "Not investment to update";
                        $type = "warning";
                    }
                } else {
                    $notice = "Out of range data";
                    $type = "error";
                }
            } else {
                $notice = "Wrong data format";
                $type = "error";
            }
        }

        /*   $arr = $investments[0]->getPaidByDay();
        var_dump(array_keys($arr[1]));
        $toshow = "";
        for ($i = 0; $i < count($arr); $i++) {
            foreach ($arr[$i] as $key => $value) {
                if ($key != 'date') {
                    $toshow .=  "<h1>" .  $key . ': ' . $value . "</h1>";
                }
            }
        }

        return new Response("
        <html>
            <body>
               . $toshow .
            </body>
        </html>");*/

        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('admin'));
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404)
     * 
     * @Route("admin/updaterange" , name="updaterange")
     */
    public function updateRangeAction(Request $request)
    {
        $notice = "Could not update the range!";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;
            $user = $this->getUser();

            $minrange = $request->get('minrange');
            $maxrange = $request->get('maxrange');

            if (is_numeric($minrange) && is_numeric($maxrange) && $minrange < $maxrange) {

                $util = $em->getRepository('App:Util')->find(1);
                if ($util != null) {
                    $util->setDate(new \DateTime());
                    $util->setEditedby($user);
                    $util->setDownlimit($minrange);
                    $util->setUplimit($maxrange);
                    $em->flush();

                    $notice = "Range updated successfully";
                    $type = "success";
                } else {
                    $notice = "Util is not working right";
                    $type = "error";
                }
            } else {
                $notice = "Wrong data format";
                $type = "error";
            }
        }

        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('admin'));
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404)
     * 
     * @Route("admin/dashboard" , name="admin")
     */
    public function adminView()
    {
        $em = $this->em;
        $users = $em->getRepository('App:User')->findAll();
        $coins = $em->getRepository('App:Coins')->findAll();
        $wallets = $em->getRepository('App:Wallet')->findAll();
        $deposits = $em->getRepository('App:Deposit')->findAll();
        $withdrawals = $em->getRepository('App:Withdrawl')->findAll();
        $investments = $em->getRepository('App:Investment')->findAll();
        $history = $em->getRepository('App:History')->findAll();
        $payments = $em->getRepository('App:Payment')->findAll();
        $util = $em->getRepository('App:Util')->find(1);

        return $this->render(
            'admin.html.twig',
            array(
                'users' => $users, 'coins' => $coins, 'wallets' => $wallets, 'deposits' => $deposits,
                'withdrawals' => $withdrawals, 'investments' => $investments, 'history' => $history, 'payments' => $payments, 'util' => $util
            )
        );
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404)
     *
     * @Route("admin/aprovedepo", name="aprovedepo")
     */
    public function aprovedepoAction(Request $request)
    {

        $notice = "Could not aprove the deposit!";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;

            $depoid = $request->get('depoid');
            $aproamount = $request->get('aproamount');

            if (!is_numeric($aproamount) || !is_numeric($depoid)) {
                $this->addFlash("warning", "Invalid format");
                return $this->redirect($this->generateUrl('admin'));
            }

            $deposit = $em->getRepository('App:Deposit')->find($depoid);

            if ($deposit != null) {
                if ($aproamount  < $deposit->getCoin()->getMindeposit()) {
                    $this->addFlash("warning", "Under the minimum");
                    return $this->redirect($this->generateUrl('admin'));
                }
                $user = $deposit->getUserid();
                if ($user == null) {
                    $this->addFlash("warning", "No user found");
                    return $this->redirect($this->generateUrl('admin'));
                }

                $deposit->setAmount($aproamount);
                $deposit->setStatus("Done");
                $deposit->setActionby($this->getUser());

                $history = new History();
                $history->addUserid($user);
                $history->setDate(new \DateTime());
                $history->setDescription("Deposit by " . $aproamount . " " .  $deposit->getCoin()->getName() . " Done!");
                $history->setType('success');
                $history->setTitle('Deposit');
                $history->setCssclass('fa-thumbs-up');
                $em->persist($history);

                $investment = new Investment();

                $investment->setUserid($user);
                $investment->setStatus(true);
                $investment->setAmount($aproamount);
                $investment->setGoal($aproamount * 2);
                $investment->setCoin($deposit->getCoin());
                $investment->setDate(new \DateTime());
                $em->persist($investment);

                $history = new History();
                $history->addUserid($user);
                $history->setDate(new \DateTime());
                $history->setDescription("New investment by " . $investment->getAmount() . $deposit->getCoin()->getAbbr());
                $history->setType('info');
                $history->setTitle('Investment');
                $history->setCssclass('fa-info');
                $em->persist($history);

                $referal = $user->getReferal();

                if ($referal != null) {
                    $walletamount = $referal->getWallet()->getByCoinAbbr(strtoupper($deposit->getCoin()->getAbbr()));
                    $referal->getWallet()->setByCoinAbbr(strtoupper($deposit->getCoin()->getAbbr()), $walletamount + ($investment->getAmount() * 0.1));
                    $referal->addTotalearningsBycoin($referal->getTotalearnings(), $deposit->getCoin()->getAbbr(), ($investment->getAmount() * 0.1));
                    $user->addCommissionByCoin($user->getCommission(), $deposit->getCoin()->getAbbr(), ($investment->getAmount() * 0.1));

                    $history = new History();
                    $history->addUserid($referal);
                    $history->setDate(new \DateTime());
                    $history->setDescription("New direct benefit of deposit by " . ($investment->getAmount() * 0.1) . $deposit->getCoin()->getAbbr());
                    $history->setType('info');
                    $history->setTitle('Benefit');
                    $history->setCssclass('fa-info');
                    $em->persist($history);
                }

                $em->flush();

                $notice = "The aprovement was successfully done";
                $type = "success";
            } else {
                $this->addFlash("warning", "No deposit found");
                return $this->redirect($this->generateUrl('admin'));
            }
        }
        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('admin'));
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404)
     *
     * @Route("admin/denydepo", name="denydepo")
     */
    public function denyDepoAction(Request $request)
    {

        $notice = "Could not deny the deposit!";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;

            $depoid = $request->get('denydepoid');

            if (!is_numeric($depoid)) {
                $this->addFlash("warning", "Invalid format");
                return $this->redirect($this->generateUrl('admin'));
            }

            $deposit = $em->getRepository('App:Deposit')->find($depoid);

            if ($deposit != null) {
                $user = $deposit->getUserid();
                if ($user != null) {

                    $deposit->setAmount(0);
                    $deposit->setStatus("Denied");
                    $deposit->setActionby($this->getUser());

                    $history = new History();
                    $history->addUserid($user);
                    $history->setDate(new \DateTime());
                    $history->setDescription("Deposit of " .  $deposit->getCoin()->getName() . " Denied");
                    $history->setType('danger');
                    $history->setTitle('Deposit');
                    $history->setCssclass('fa-thumbs-down');
                    $em->persist($history);

                    $em->flush();

                    $notice = "The deposit was successfully denied";
                    $type = "success";
                } else {
                    $this->addFlash("warning", "No user found");
                    return $this->redirect($this->generateUrl('admin'));
                }
            } else {
                $this->addFlash("warning", "No deposit found");
                return $this->redirect($this->generateUrl('admin'));
            }
        }
        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('admin'));
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404)
     *
     * @Route("admin/denywith", name="denywith")
     */
    public function denyWithAction(Request $request)
    {

        $notice = "Could not deny the withdrawal!";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;

            $withid = $request->get('denywithid');

            if (!is_numeric($withid)) {
                $this->addFlash("warning", "Invalid format");
                return $this->redirect($this->generateUrl('admin'));
            }

            $withdrawal = $em->getRepository('App:Withdrawl')->find($withid);

            if ($withdrawal != null) {
                $user = $withdrawal->getUserid();
                if ($user != null) {

                    $withdrawal->setStatus("Denied");
                    $withdrawal->setActionby($this->getUser());

                    $history = new History();
                    $history->addUserid($user);
                    $history->setDate(new \DateTime());
                    $history->setDescription("Withdrawal request of " .  $withdrawal->getCoin()->getName() . " Denied");
                    $history->setType('danger');
                    $history->setTitle('Withdrawal');
                    $history->setCssclass('fa-thumbs-down');
                    $em->persist($history);

                    $em->flush();

                    $notice = "The withdrawal request was successfully denied";
                    $type = "success";
                } else {
                    $this->addFlash("warning", "No user found");
                    return $this->redirect($this->generateUrl('admin'));
                }
            } else {
                $this->addFlash("warning", "No withdrawal found");
                return $this->redirect($this->generateUrl('admin'));
            }
        }
        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('admin'));
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404)
     *
     * @Route("admin/aprovewith", name="aprovewith")
     */
    public function aprovewithAction(Request $request)
    {

        $notice = "Could not aprove the withdrawal!";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;

            $withid = $request->get('aprovewithid');
            $aproamount = $request->get('aproamountwith');

            if (!is_numeric($aproamount) || !is_numeric($withid)) {
                $this->addFlash("warning", "Invalid format");
                return $this->redirect($this->generateUrl('admin'));
            }

            $withdrawal = $em->getRepository('App:Withdrawl')->find($withid);

            if ($withdrawal != null) {
                if ($aproamount  < $withdrawal->getCoin()->getMinwithdraw()) {
                    $this->addFlash("warning", "Under the minimum");
                    return $this->redirect($this->generateUrl('admin'));
                }
                $user = $withdrawal->getUserid();
                if ($user == null) {
                    $this->addFlash("warning", "No user found");
                    return $this->redirect($this->generateUrl('admin'));
                }

                $walletamount = $user->getWallet()->getByCoinAbbr(strtoupper($withdrawal->getCoin()->getAbbr()));
                if ($walletamount >= $aproamount) {
                    $user->getWallet()->setByCoinAbbr(strtoupper($withdrawal->getCoin()->getAbbr()), $walletamount - $aproamount);
                } else {
                    $this->addFlash("warning", "Not enough balance");
                    return $this->redirect($this->generateUrl('admin'));
                }

                $withdrawal->setAmount($aproamount);
                $withdrawal->setStatus("Done");
                $withdrawal->setActionby($this->getUser());

                $history = new History();
                $history->addUserid($user);
                $history->setDate(new \DateTime());
                $history->setDescription("Withdrawal by " . $aproamount . " " .  $withdrawal->getCoin()->getName() . " Done!");
                $history->setType('success');
                $history->setTitle('Withdrawal');
                $history->setCssclass('fa-thumbs-up');
                $em->persist($history);

                $em->flush();

                $notice = "The aprovement was successfully done";
                $type = "success";
            } else {
                $this->addFlash("warning", "No withdrawal found");
                return $this->redirect($this->generateUrl('admin'));
            }
        }
        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('admin'));
    }

    /**
     * @Security("is_granted('ROLE_ADMIN')", statusCode=404)
     *
     * @Route("admin/editcoin", name="editcoin")
     */
    public function editCoinAction(Request $request)
    {

        $notice = "Could not edit the coin";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;

            $editcoinid = $request->get('editcoinid');
            $coinname = $request->get('coinname');
            $coinabbr = $request->get('coinabbr');
            $coinaddress = $request->get('coinaddress');
            $mininv = $request->get('mininv');
            $mindepo = $request->get('mindepo');
            $minwith = $request->get('minwith');
            $withfee = $request->get('withfee');
            $web = $request->get('web');
            $inwallet = $request->get('inwallet');

            if (!is_numeric($editcoinid) || !is_numeric($mininv) || !is_numeric($mindepo) || !is_numeric($minwith) || !is_numeric($withfee) || !is_numeric($inwallet)) {
                $this->addFlash("warning", "Invalid format");
                return $this->redirect($this->generateUrl('admin'));
            }

            $coin = $em->getRepository('App:Coins')->find($editcoinid);

            if ($coin != null) {

                $coin->setName($coinname);
                $coin->setAbbr($coinabbr);
                $coin->setAddress($coinaddress);
                $coin->setMininvest($mininv);
                $coin->setMindeposit($mindepo);
                $coin->setMinwithdraw($minwith);
                $coin->setWithdrawfee($withfee);
                $coin->setWeb($web);
                $coin->setInwallet($inwallet);
                $coin->setActionby($this->getUser());

                $em->flush();

                $notice = "The coin was successfully edited";
                $type = "success";
            } else {
                $this->addFlash("error", "No coin found");
                return $this->redirect($this->generateUrl('admin'));
            }
        }

        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('admin'));
    }

    /**
     * @Route("dashboard/getpayment", options={"expose"=true}, name="getpayment")
     */
    public function getpayment(Request $request)
    {
        $notice = 'error';
        if ($request->isXmlHttpRequest()) {
            $date = $request->get('date');
            $splited = explode("/", $date);
            $pay = $this->em->getRepository('App:Payment')->getPaymentByDay($splited[1], $splited[0], $splited[2]);
            if(!is_null($pay)){
                $notice = 'success';
            }
            return new JsonResponse(['paid' => $pay, 'notice' => $notice]);
        } else {
            throw new Exception($message = "Error");
        }
    }
    

    /**
     * @Route("dashboard/test" , name="test")
     */
    public function testAction()
    {

        $towithdraw = 0.005;
        $coin = "BTC";
        $user = $this->getUser();
        $depos = $user->getWithds();
        dd($depos);


        // $em = $this->em;

        // $mul = 100;

        // for ($i = 0; $i < 152; $i++) {
        //     $percent = mt_rand(2 * $mul, 6 * $mul) / $mul;
        //     $payment = new Payment($em);
        //     $payment->setDate(date_add(new \DateTime('2021-01-01 22:19:17'), date_interval_create_from_date_string($i > 9 ? $i . ' days' : $i . ' day')));
        //     $payment->setPaidby($user);
        //     $payment->setPercent($percent);
        //     $em->persist($payment);
        //     $em->flush();
        // }

        // return new Response("
        // <html>
        //     <body>
        //         <h1>" . "Yeah"  . "</h1>
        //     </body>
        // </html>");
    }
}
