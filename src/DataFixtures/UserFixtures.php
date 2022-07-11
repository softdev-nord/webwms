<?php

declare(strict_types=1);

namespace WebWMS\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use WebWMS\Entity\User;

class UserFixtures extends Fixture
{
    public function __construct(
        private UserPasswordEncoderInterface $passwordEncoder
    ) {
    }

    public function load(ObjectManager $manager)
    {
        // Create our user and set details
        $userAdmin = new User();
        $userAdmin->setUsername('admin');
        $userAdmin->setFirstname('Rene');
        $userAdmin->setLastname('Irrgang');

        $plainPassword = 'Apolda8801!#';
        $userAdmin->setPassword(
            $this->passwordEncoder->encodePassword($userAdmin, $plainPassword)
        );

        $userAdmin->setRoles((array) 'ROLE_SUPER_ADMIN');

        $manager->persist($userAdmin);
        $manager->flush();
    }
}
