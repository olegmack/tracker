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
        'bug'     => 'Bug',
        'story'   => 'Story',
        'task'    => 'Task',
        'subtask' => 'Sub-task'
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $code => $name) {
            $issuePriority = new IssueType($code);
            $issuePriority->setName($name);
            $manager->persist($issuePriority);
        }

        $manager->flush();
    }
}