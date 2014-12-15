<?php
namespace Oro\IssueBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\IssuePriority;

class LoadIssuePriorityData extends AbstractFixture implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        'trivial'  => 'Trivial',
        'minor'    => 'Minor',
        'major'    => 'Major',
        'critical' => 'Critical',
        'blocker'  => 'Blocker',
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $code => $name) {
            $issuePriority = new IssuePriority($code);
            $issuePriority->setName($name);
            $manager->persist($issuePriority);
        }

        $manager->flush();
    }
}
