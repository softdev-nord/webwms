<?php

namespace WebWMS\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use WebWMS\Entity\User;

class UserFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
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

        //$roles = 'ROLE_SUPER_ADMIN';
        $userAdmin->setRoles((array) 'ROLE_SUPER_ADMIN');

        $manager->persist($userAdmin);
        $manager->flush();
    }
}
