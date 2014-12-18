<?php
namespace Oro\IssueBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Oro\IssueBundle\Entity\Issue;

class LoadIssueData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @return array
     */
    public function getDependencies()
    {
        return array(
            'Oro\IssueBundle\DataFixtures\ORM\LoadIssuePriorityData',
            'Oro\IssueBundle\DataFixtures\ORM\LoadIssueResolutionData',
            'Oro\IssueBundle\DataFixtures\ORM\LoadIssueStatusData',
            'Oro\IssueBundle\DataFixtures\ORM\LoadIssueTypeData',
            'Oro\UserBundle\DataFixtures\ORM\LoadUserData',
            'Oro\ProjectBundle\DataFixtures\ORM\LoadProjectData'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $bapProject = $this->getReference('project1');
        $oroProject = $this->getReference('project2');

        $operatorUser = $this->getReference('user_operator');
        $operatorUser2 = $this->getReference('user_operator2');
        $managerUser = $this->getReference('user_manager');

        $issue1 = new Issue();
        $issue1->setSummary('Replace calendar picker tool');
        $issue1->setDescription(
            'The usability of our calendar picker control is bad and we should replace it with the more convenient one.'
        );

        $issue1->setProject($bapProject);
        $issue1->setReporter($managerUser);
        $issue1->setAssignee($operatorUser);

        $issue1->setIssuePriority($manager->getRepository('OroIssueBundle:IssuePriority')->findOneByCode('major'))
            ->setIssueResolution($manager->getRepository('OroIssueBundle:IssueResolution')->findOneByCode('unresolved'))
            ->setIssueStatus($manager->getRepository('OroIssueBundle:IssueStatus')->findOneByCode('open'))
            ->setIssueType($manager->getRepository('OroIssueBundle:IssueType')->findOneByCode('task'))
            ->setModifiedBy($managerUser);

        $manager->persist($issue1);

        $issue2 = new Issue();
        $issue2->setSummary('REST API documentation');
        $issue2->setDescription('Provide REST API documentation.');

        $issue2->setProject($oroProject);
        $issue2->setReporter($managerUser);
        $issue2->setAssignee($managerUser);

        $issue2->setIssuePriority($manager->getRepository('OroIssueBundle:IssuePriority')->findOneByCode('major'))
            ->setIssueResolution($manager->getRepository('OroIssueBundle:IssueResolution')->findOneByCode('unresolved'))
            ->setIssueStatus($manager->getRepository('OroIssueBundle:IssueStatus')->findOneByCode('open'))
            ->setIssueType($manager->getRepository('OroIssueBundle:IssueType')->findOneByCode('story'))
            ->setModifiedBy($managerUser);

        $manager->persist($issue2);

        $issue3 = new Issue();
        $issue3->setSummary('Add possibility to display request/response example in nelmio API bundle doc');
        $issue3->setDescription('
<ul class="alternate" type="square">
<li>Override formatter service</li>
<li>Add section with response/request examples</li>
<li>Add version switcher</li>
<li>Enable ajax loading for doc of each resource</li>
</ul>');

        $issue3->setProject($oroProject);
        $issue3->setReporter($managerUser);
        $issue3->setAssignee($operatorUser2);

        $issue3->setIssuePriority($manager->getRepository('OroIssueBundle:IssuePriority')->findOneByCode('major'))
            ->setIssueResolution($manager->getRepository('OroIssueBundle:IssueResolution')->findOneByCode('unresolved'))
            ->setIssueStatus($manager->getRepository('OroIssueBundle:IssueStatus')->findOneByCode('open'))
            ->setIssueType($manager->getRepository('OroIssueBundle:IssueType')->findOneByCode('subtask'))
            ->setParent($issue2)
            ->setModifiedBy($managerUser);

        $manager->persist($issue3);

        $issue4 = new Issue();
        $issue4->setSummary('Move template fixture logic to EntityBundle');
        $issue4->setDescription('
<ul class="alternate" type="square">
<li>Move classes to entity bundle</li>
<li>Add aliases for services to keep BC</li>
<li>Ensure BC not broken, add stub classes to import export bundle</li>
</ul>');

        $issue4->setProject($oroProject);
        $issue4->setReporter($managerUser);
        $issue4->setAssignee($operatorUser2);

        $issue4->setIssuePriority($manager->getRepository('OroIssueBundle:IssuePriority')->findOneByCode('minor'))
            ->setIssueResolution($manager->getRepository('OroIssueBundle:IssueResolution')->findOneByCode('done'))
            ->setIssueStatus($manager->getRepository('OroIssueBundle:IssueStatus')->findOneByCode('closed'))
            ->setIssueType($manager->getRepository('OroIssueBundle:IssueType')->findOneByCode('subtask'))
            ->setParent($issue2)
            ->setModifiedBy($managerUser);

        $manager->persist($issue4);

        $manager->flush();

        $this->addReference('issue1', $issue1);
        $this->addReference('issue2', $issue2);
        $this->addReference('issue3', $issue3);
        $this->addReference('issue4', $issue4);
    }
}
