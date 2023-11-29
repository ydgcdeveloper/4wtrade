<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wallet;
use App\Help\MailerHelp;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    private $em;
    private $encoder;
    private $mailer;

    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder, MailerInterface $mailer)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->mailer = $mailer;
    }


    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('dashboard');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout_message", name="logout_message")
     */
    public function logoutMessage()
    {

        //  $this->addFlash('success', "You've been disconnected. Bye bye !");
        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
        // este controller no se ejecutará,
        // ya que la route se maneja por el sistema de seguridad
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
        // este controller no se ejecutará,
        // ya que la route se maneja por el sistema de seguridad
    }

    /**
     * @Route("dashboard/before_logout", name="before_logout")
     */
    public function beforelAction()
    {
        $user = $this->getUser();

        $type = 'info';
        $notice = "Good bye, come back soon";

        $this->addFlash($type, $notice);

        $time = new \DateTime();
        $user->setLastlogout($time);

        $em = $this->getDoctrine()->getManager();
        $em->flush($user);

        return $this->redirectToroute('logout');
    }

    /**
     * @Route("security/changepassword", name="changepassword")
     */
    public function changepasswordAction(Request $request)
    {
        $notice = "Could not change password!";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;

            $user = $this->getUser();

            $password = $request->get('cpass');
            $new_pass = $request->get('npass');
            $repnew_pass = $request->get('rnpass');

            if (!is_string($password) || !is_string($new_pass) || !is_string($repnew_pass)) {
                $this->addFlash("error", "Invalid format");
                return $this->redirect($this->generateUrl('changepass'));
            }

            if ($new_pass == $repnew_pass) {

                if ($user != null) {
                    $checkPass = $this->encoder->isPasswordValid($user, $password) ? true : false;
                    if ($checkPass === true) {
                        $user->setPassword($new_pass);
                        $password_encoded = $this->encoder->encodePassword($user, $user->getPassword());
                        $user->setPassword($password_encoded);
                        $em->flush($user);
                        $notice = "Password succesfully changed";
                        $type = "success";
                    } else {
                        $notice = "Invalid current password";
                        $type = "error";
                    }
                }
            } else {
                $notice = "Invalid new password";
                $type = "error";
            }
        }

        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('changepass'));
    }

    
    /**
     * @Route("/register_user", name="register_user")
     */
    public function register_userAction(Request $request)
    {
        $notice = "Error al crear el usuario";
        $type = "error";

        if ($request->isMethod('POST')) {

            $em = $this->em;
            $users_repo = $em->getRepository('App:User');
            
            
            //se genera el hash que identifica al usuario
            $refusername = $request->get('refusername');
            $name = $request->get('name');
            $username = $request->get('username');
            $email = $request->get('email');
            $password = $request->get('password');
            $rpassword = $request->get('rpassword');

            $pattern="/\s/";
            if(preg_match($pattern , $username ) || !filter_var($email , FILTER_VALIDATE_EMAIL)){
                $this->addFlash("error", "Invalid format");
                return $this->redirect($this->generateUrl('register'));
            }

            $emailrepo = $users_repo->findOneBy(['email' => $email]);
            $usernamerepo = $users_repo->findBy(['username' => $username]);
            $referal = $users_repo->findOneBy(['username' => $refusername]);

            if ($emailrepo == null && $usernamerepo == null) {

                if ($password == $rpassword) {

                    $user = new User();

                    if ($referal != null) {
                        $user->setReferal($referal);
                        $referal->addRefuser($user);
                    }
                    $user->setUsername($username);
                    $user->setEmail($email);
                    $user->setPassword($password);
                    $user->setHash($this->generateHash(15));
                    $user->setEmailHash($this->generateHash(20));
                    $user->setCreatedAt(new \DateTime());
                    $user->setName($name);
                    $user->setLastlogin(new \DateTime());
                    $user->setLastlogout(new \DateTime());
                    $password_encoded = $this->encoder->encodePassword($user, $user->getPassword());
                    $user->setPassword($password_encoded);

                    //se envia el email con el hash al correo del usuario #DESCOMENTAR
                 //   $helper->sendEmail($email, $emailhash);

                    $em->persist($user);
                    $flush = $em->flush();

                    $mailer =  new MailerHelp();
                    // $mailer->sendConfEmail($this->mailer, $email, $user->getUsername() , $user->getEmailHash());

                    if ($flush == null) {
                        $notice = "User succesfully registered, check your email account";
                        $type = "success";
                        $this->addFlash($type, $notice);
                        return $this->redirect($this->generateUrl('app_login'));
                    } else {
                        $this->addFlash($type, $notice);
                        return $this->redirect($this->generateUrl('register', array('id' => $refusername)));
                    }
                } else {
                    $notice = "Invalid passwords";
                    $type = "warning";
                    $this->addFlash($type, $notice);
                    return $this->redirect($this->generateUrl('register', array('id' => $refusername)));
                }
            } else {
                $notice = "Email or username already exist";
                $type = "info";
                $this->addFlash($type, $notice);
                return $this->redirect($this->generateUrl('register', array('id' => $refusername)));
            }
        }

        $this->addFlash($type, $notice);
        return $this->redirect($this->generateUrl('register'));
    }

    /**
     * @Route("/check_email", name="check_email")
     */
    public function check_emailAction(Request $request)
    {
        if ($this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('dashboard');
        }

        $emailhash = null;

        $notice = 'Ups! your email could not be verified';
        $status = 'danger';

        if ($request->isMethod('GET')) {
            $emailhash =  $request->get('code');
            if ($emailhash != null) {
                $em = $this->em;
                $users_repo = $em->getRepository('App:User');
                $user = $users_repo->findOneBy(array('emailhash' => $emailhash));
                if ($user != null) {
                    if ($user->isActive() == false) {
                        $user->setIsActive(true);
                        $wallet = new Wallet();
                        $user->setWallet($wallet);

                        $referalParent = $user->getReferal();
                        if ($referalParent != null) {
                            $id = $referalParent->getId();
                            $parent =  $users_repo->find($id);
                            if ($parent != null) {
                                $parent->setReferrals($parent->getReferrals() + 1);
                                $em->flush($parent);
                            }
                        }
                        $em->persist($wallet);
                        $em->flush($user);
                        $notice = 'Your Email has been successfully verified';
                        $status = 'success';
                    } else {
                        $notice = 'The email has already been verified previously';
                        $status = 'warning';
                    }
                }
            }
        }
        $this->addFlash($status, $notice);
        return $this->render('verified_email.html.twig', array('status' => $status, 'notice' => $notice));
    }
























    //Support functions
    public static function generateHash($len)
    {
        $KEY_CHARS = 'acefghjkpqrstwxyz23456789';
        $k = str_repeat('.', $len);
        while ($len--) {
            $k[$len] = substr($KEY_CHARS, mt_rand(0, strlen($KEY_CHARS) - 1), 1);
        }
        return $k;
    }

    
}
