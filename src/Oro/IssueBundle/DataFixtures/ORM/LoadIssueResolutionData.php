<?php
namespace Oro\IssueBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Oro\IssueBundle\Entity\IssueResolution;

class LoadIssueResolutionData extends AbstractFixture implements FixtureInterface
{
    /**
     * @var array
     */
    protected $data = array(
        'unresolved'  => 'Unresolved',
        'fixed'       => 'Fixed',
        'duplicate'   => 'Duplicate',
        'wontfix'     => 'Won\'t fix',
        'done'        => 'Done',
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        foreach ($this->data as $code => $name) {
            $issuePriority = new IssueResolution($code);
            $issuePriority->setName($name);
            $manager->persist($issuePriority);
        }

        $manager->flush();
    }
}