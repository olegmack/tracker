<?php
namespace Oro\UserBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Oro\UserBundle\Entity\User;
use Oro\ProjectBundle\Entity\Project;

class LoadProjectData implements FixtureInterface, ContainerAwareInterface
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
        $project = new Project();
        $project->setName('Business Application Platform')
            ->setCode('BAP')
            ->setSummary('The Oro Platform is an Open source Business Application Platform (BAP). It offers developers the exact business
application platform they’ve been looking for, by combining the tools they need. Built in PHP5 and the
Symfony2 framework, developing custom business applications has never been so easy.');

        $adminUser = $manager->getRepository('OroUserBundle:User')->findOneByUsername('admin');
        $operatorUser = $manager->getRepository('OroUserBundle:User')->findOneByUsername('operator');
        $operatorUser2 = $manager->getRepository('OroUserBundle:User')->findOneByUsername('operator2');
        $managerUser = $manager->getRepository('OroUserBundle:User')->findOneByUsername('manager');

        $project
            ->addUser($managerUser)
            ->addUser($operatorUser)
            ->addUser($adminUser);

        $manager->persist($project);

        $project2 = new Project();
        $project2->setName('OroCRM')
            ->setCode('CRM')
            ->setSummary('OroCRM is an easy-to-use, open source CRM with built in marketing automation tools for your commerce business. It’s the CRM built for both sales and marketing!');

        $project2
            ->addUser($managerUser)
            ->addUser($operatorUser2)
            ->addUser($adminUser);

        $manager->persist($project2);

        $manager->flush();
    }
}