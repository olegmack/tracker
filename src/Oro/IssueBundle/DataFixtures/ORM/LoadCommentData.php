<?php
namespace Oro\UserBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\Comment;
use Oro\UserBundle\Entity\User;
use Oro\ProjectBundle\Entity\Project;

class LoadCommentData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    public function getDependencies()
    {
        return array(
            'Oro\IssueBundle\DataFixtures\ORM\LoadIssueData'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $operatorUser = $manager->getRepository('OroUserBundle:User')->findOneByUsername('operator');
        $operatorUser2 = $manager->getRepository('OroUserBundle:User')->findOneByUsername('operator2');
        $managerUser = $manager->getRepository('OroUserBundle:User')->findOneByUsername('manager');

        $comment1 = new Comment();
        $comment1
            ->setAuthor($operatorUser)
            ->setBody('Implemented in branch feature/BAP-1234')
            ->setIssue($this->getReference('issue1'));

        $manager->persist($comment1);

        $comment2 = new Comment();
        $comment2
            ->setAuthor($managerUser)
            ->setBody('CR passed. Deployed on production, customer is informed about changes')
            ->setIssue($this->getReference('issue1'));

        $manager->persist($comment2);

        $comment3 = new Comment();
        $comment3
            ->setAuthor($managerUser)
            ->setBody('Issue was resolved. Not actual for now.')
            ->setIssue($this->getReference('issue3'));

        $manager->persist($comment3);

        $comment4 = new Comment();
        $comment4
            ->setAuthor($operatorUser2)
            ->setBody('Design is applied')
            ->setIssue($this->getReference('issue4'));

        $manager->persist($comment4);

        $comment5 = new Comment();
        $comment5
            ->setAuthor($operatorUser2)
            ->setBody('All issues were resolved, changes are merged to the master branch and deployed, please test them')
            ->setIssue($this->getReference('issue4'));

        $manager->persist($comment5);

        $manager->flush();
    }
}