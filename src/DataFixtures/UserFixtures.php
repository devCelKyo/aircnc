<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $encoder;
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("truc@truc.com");
        $user->setFirstname('A');
        $user->setLastname('B');
        $user->setUsername('Nadir');
        $user->setPassword($this->encoder->encodePassword($user, 'password'));
        $user->addRole('ROLE_SUPER_ADMIN');

        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setEmail("truc2@truc.com");
        $user->setFirstname('A');
        $user->setLastname('B');
        $user->setUsername('Nadir2');
        $user->setPassword($this->encoder->encodePassword($user, 'password'));

        $manager->persist($user);
        $manager->flush();
    }
}
