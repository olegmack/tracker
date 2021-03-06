<?php

namespace Oro\UserBundle\DataFixtures\ORM;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Oro\UserBundle\Entity\User;
use Oro\UserBundle\Entity\Role;

class LoadUserData extends AbstractFixture implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $encoder = $this->container
            ->get('security.encoder_factory')
            ->getEncoder($user);

        $adminRole = new Role();
        $adminRole
            ->setRole('ROLE_ADMIN')
            ->setName('Administrator');

        $manager->persist($adminRole);

        $user->setUsername('admin');
        $user->setEmail('admin@oro.crm');
        $user->setFullname('John Doe');
        $user->setPassword($encoder->encodePassword('admin123', $user->getSalt()));
        $user->getRolesCollection()->add($adminRole);
        $manager->persist($user);

        $managerRole = new Role();
        $managerRole
            ->setRole('ROLE_MANAGER')
            ->setName('Manager');

        $manager->persist($managerRole);

        $user2 = new User();
        $user2->setUsername('manager');
        $user2->setEmail('manager@oro.crm');
        $user2->setFullname('Richard Brooks');
        $user2->setPassword($encoder->encodePassword('manager123', $user2->getSalt()));
        $user2->getRolesCollection()->add($managerRole);
        $manager->persist($user2);

        $userRole = new Role();
        $userRole
            ->setRole('ROLE_USER')
            ->setName('Operator');

        $manager->persist($userRole);

        $user3 = new User();
        $user3->setUsername('operator');
        $user3->setEmail('operator@oro.crm');
        $user3->setFullname('Robert Lamm');
        $user3->setPassword($encoder->encodePassword('operator123', $user3->getSalt()));
        $user3->getRolesCollection()->add($userRole);
        $manager->persist($user3);

        $user4 = new User();
        $user4->setUsername('operator2');
        $user4->setEmail('operator2@oro.crm');
        $user4->setFullname('Neil Phillips');
        $user4->setPassword($encoder->encodePassword('operator123', $user4->getSalt()));
        $user4->getRolesCollection()->add($userRole);
        $manager->persist($user4);

        $manager->flush();

        $this->addReference('user_admin', $user);
        $this->addReference('user_manager', $user2);
        $this->addReference('user_operator', $user3);
        $this->addReference('user_operator2', $user4);
    }
}
