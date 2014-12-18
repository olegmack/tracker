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
        IssueResolution::CODE_UNRESOLVED  => 'Unresolved',
        IssueResolution::CODE_FIXED       => 'Fixed',
        IssueResolution::CODE_DUPLICATE   => 'Duplicate',
        IssueResolution::CODE_WONTFIX     => 'Won\'t fix',
        IssueResolution::CODE_DONE        => 'Done',
    );

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $priority = 10;
        foreach ($this->data as $code => $name) {
            $issueResolution = new IssueResolution($code);
            $issueResolution->setName($name);
            $issueResolution->setPriority($priority);
            $priority += 10;
            $manager->persist($issueResolution);
        }

        $manager->flush();
    }
}
