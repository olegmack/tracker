<?php
namespace Oro\UserBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\Issue;
use Oro\UserBundle\Entity\User;
use Oro\ProjectBundle\Entity\Project;

class LoadIssueData implements FixtureInterface, DependentFixtureInterface
{
    public function getDependencies()
    {
        return array(
            'Oro\UserBundle\DataFixtures\ORM\LoadIssuePriorityData',
            'Oro\UserBundle\DataFixtures\ORM\LoadIssueResolutionData',
            'Oro\UserBundle\DataFixtures\ORM\LoadIssueStatusData',
            'Oro\UserBundle\DataFixtures\ORM\LoadIssueTypeData'
        );
    }
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $issue1 = new Issue();
        $issue1->setSummary('Replace calendar picker tool');
        $issue1->setDescription('The usability of our calendar picker control is bad and we should replace it with the more convenient one.');

        $bapProject = $manager->getRepository('OroProjectBundle:Project')->findOneByCode('BAP');
        $operatorUser = $manager->getRepository('OroUserBundle:User')->findOneByUsername('operator');
        $managerUser = $manager->getRepository('OroUserBundle:User')->findOneByUsername('manager');

        $issue1->setProject($bapProject);
        $issue1->setReporter($managerUser);
        $issue1->setAssignee($operatorUser);

        $issue1->setIssuePriority($manager->getRepository('OroIssueBundle:IssuePriority')->findOneByCode('major'))
            ->setIssueResolution($manager->getRepository('OroIssueBundle:IssueResolution')->findOneByCode('unresolved'))
            ->setIssueStatus($manager->getRepository('OroIssueBundle:IssueStatus')->findOneByCode('open'))
            ->setIssueType($manager->getRepository('OroIssueBundle:IssueType')->findOneByCode('task'));

        $manager->persist($issue1);

        $manager->flush();
    }
}