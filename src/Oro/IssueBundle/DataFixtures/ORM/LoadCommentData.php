<?php

namespace Oro\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\AbstractFixture;

use Oro\IssueBundle\Entity\Comment;

class LoadCommentData extends AbstractFixture implements DependentFixtureInterface
{
    /**
     * @return array
     */
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
        $operatorUser = $this->getReference('user_operator');
        $operatorUser2 = $this->getReference('user_operator2');
        $managerUser = $this->getReference('user_manager');

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
            ->setBody('All issues were resolved, changes are merged to the master branch and deployed.')
            ->setIssue($this->getReference('issue4'));

        $manager->persist($comment5);

        $manager->flush();
    }
}
