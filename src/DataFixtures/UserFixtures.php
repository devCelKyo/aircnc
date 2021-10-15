<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Owner;
use App\Entity\Client;
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
        $user->setEmail("super_admin@truc.com");
        $user->setFirstname('A');
        $user->setLastname('B');
        $user->setUsername('Admin');
        $user->setPassword($this->encoder->encodePassword($user, 'password'));
        $user->addRole('ROLE_SUPER_ADMIN');

        $owner = new Owner();
        $owner->setFirstname($user->getFirstname());
        $owner->setLastname($user->getLastname());
        $owner->setCountry('FR');

        $client = new Client();
        $client->setFirstname($user->getFirstname());
        $client->setLastname($user->getLastname());
        $client->setCountry('FR');

        $user->setOwner($owner);
        $user->setClient($client);

        $manager->persist($user);
        $manager->flush();

        $user = new User();
        $user->setEmail("user@truc.com");
        $user->setFirstname('A2');
        $user->setLastname('B2');
        $user->setUsername('User');
        $user->setPassword($this->encoder->encodePassword($user, 'password'));

        $owner = new Owner();
        $owner->setFirstname($user->getFirstname());
        $owner->setLastname($user->getLastname());
        $owner->setCountry('UK');

        $client = new Client();
        $client->setFirstname($user->getFirstname());
        $client->setLastname($user->getLastname());
        $client->setCountry('UK');

        $user->setOwner($owner);
        $user->setClient($client);

        $manager->persist($user);
        $manager->flush();
    }
}
