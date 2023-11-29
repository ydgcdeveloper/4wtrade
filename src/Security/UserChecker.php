<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker extends AbstractController implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user)
    {
        if (!$user instanceof User) {
            return;
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException(
                'Inactive users cannot log in, check your email account'
            );
        }
    }

    public function checkPostAuth(UserInterface $user)
    {
        $this->checkPreAuth($user);

        if (!$user instanceof User) {
            return;
        }

        $websitename = $this->getParameter('app.websitename');

        $type = 'info';
        $notice = "Welcome to " . $websitename;

        $this->addFlash($type, $notice);

        $time = new \DateTime();
        if ($user->getLastlogin() > $user->getLastlogout()) {
            $user->setLastlogout($time);
        }
        $user->setLastlogin($time);


        $em = $this->getDoctrine()->getManager();
        $em->flush($user);
    }
}
