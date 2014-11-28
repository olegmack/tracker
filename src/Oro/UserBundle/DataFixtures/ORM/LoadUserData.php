<?php
namespace Oro\UserBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Oro\UserBundle\Entity\User;
use Oro\UserBundle\Entity\Role;

class LoadUserData implements FixtureInterface, ContainerAwareInterface
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

        $manager->flush();
    }
}