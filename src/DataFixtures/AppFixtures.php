<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(
        UserPasswordEncoderInterface $encoder,
        )
    {
        $this->encoder = $encoder;
    }

    // ...
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('superAdmin');
        $user->setName('Yan');
        $user->setEmail('ygomezcoba@gmail.com');
        $user->setCreatedAt(new \DateTime());

        $password = $this->encoder->encodePassword($user, 'AdminUser');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}