<?php
namespace Oro\IssueBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\IssueStatus;

class LoadIssueStatusData extends AbstractFixture implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        IssueStatus::CODE_OPEN        => 'Open',
        IssueStatus::CODE_INPROGRESS  => 'In Progress',
        IssueStatus::CODE_RESOLVED    => 'Resolved',
        IssueStatus::CODE_REOPENED    => 'Reopened',
        IssueStatus::CODE_CLOSED      => 'Closed'
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $priority = 10;
        foreach ($this->data as $code => $name) {
            $issueStatus = new IssueStatus($code);
            $issueStatus->setName($name);
            $issueStatus->setPriority($priority);
            $priority += 10;
            $manager->persist($issueStatus);
        }

        $manager->flush();
    }
}
