<?php
namespace Oro\IssueBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\IssueType;

class LoadIssueTypeData extends AbstractFixture implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        IssueType::TYPE_BUG     => 'Bug',
        IssueType::TYPE_STORY   => 'Story',
        IssueType::TYPE_TASK    => 'Task',
        IssueType::TYPE_SUBTASK => 'Sub-task'
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $priority = 10;
        foreach ($this->data as $code => $name) {
            $issueType = new IssueType($code);
            $issueType->setName($name);
            $issueType->setPriority($priority);
            $priority += 10;
            $manager->persist($issueType);
        }

        $manager->flush();
    }
}
