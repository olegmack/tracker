<?php

namespace Oro\ProjectBundle\DataFixtures\ORM;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

use Oro\ProjectBundle\Entity\Project;

class LoadProjectData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @return array
     */
    public function getDependencies()
    {
        return array(
            'Oro\UserBundle\DataFixtures\ORM\LoadUserData'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $project = new Project();
        $project->setName('Business Application Platform')
            ->setCode('BAP')
            ->setSummary('The Oro Platform is an Open source Business Application Platform (BAP).
It offers developers the exact business
application platform they’ve been looking for, by combining the tools they need. Built in PHP5 and the
Symfony2 framework, developing custom business applications has never been so easy.');

        $adminUser = $this->getReference('user_admin');
        $operatorUser = $this->getReference('user_operator');
        $operatorUser2 = $this->getReference('user_operator2');
        $managerUser = $this->getReference('user_manager');

        $project
            ->addUser($managerUser)
            ->addUser($operatorUser)
            ->addUser($adminUser);

        $manager->persist($project);

        $project2 = new Project();
        $project2->setName('OroCRM')
            ->setCode('CRM')
            ->setSummary('OroCRM is an easy-to-use, open source CRM with built in marketing automation tools for your
            commerce business. It’s the CRM built for both sales and marketing!');

        $project2
            ->addUser($managerUser)
            ->addUser($operatorUser2)
            ->addUser($adminUser);

        $manager->persist($project2);

        $manager->flush();

        $this->addReference('project1', $project);
        $this->addReference('project2', $project2);
    }
}
