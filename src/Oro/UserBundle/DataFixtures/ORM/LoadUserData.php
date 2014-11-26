<?php
namespace Oro\UserBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Oro\UserBundle\Entity\User;

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

        $user->setUsername('admin');
        $user->setEmail('admin@oro.crm');
        $user->setFullname('John Doe');
        $user->setPassword($encoder->encodePassword('admin123', $user->getSalt()));
        $user->setRoles(array('ROLE_ADMIN'));
        $manager->persist($user);

        $user2 = new User();
        $user2->setUsername('manager');
        $user2->setEmail('manager@oro.crm');
        $user2->setFullname('Richard Brooks');
        $user2->setPassword($encoder->encodePassword('manager123', $user2->getSalt()));
        $user2->setRoles(array('ROLE_MANAGER'));
        $manager->persist($user2);

        $user3 = new User();
        $user3->setUsername('operator');
        $user3->setEmail('operator@oro.crm');
        $user3->setFullname('Robert Lamm');
        $user3->setPassword($encoder->encodePassword('operator123', $user3->getSalt()));
        $user3->setRoles(array('ROLE_USER'));
        $manager->persist($user3);

        $manager->flush();
    }
}