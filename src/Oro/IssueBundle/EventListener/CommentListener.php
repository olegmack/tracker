<?php

namespace Oro\IssueBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Event\LifecycleEventArgs;

use Oro\IssueBundle\Entity\Comment;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\IssueActivity;

class CommentListener
{
    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->isCommentEntity($entity)) {
            return;
        }

        /** @var Comment $entity */
        $em = $args->getEntityManager();

        //track activity
        $this->createActivityFromComment($em, $entity);

        //add collaborator
        $this->addCollaborator($em, $entity);
    }

    /**
     * @param object $entity
     * @return bool
     */
    protected function isCommentEntity($entity)
    {
        return $entity instanceof Comment;
    }

    /**
     * Add collaborator to issue based on comment information
     *
     * @param EntityManager $entityManager
     * @param Comment $comment
     */
    protected function addCollaborator($entityManager, $comment)
    {
        /** @var Issue $issue */
        $issue = $comment->getIssue();
        $issue->addCollaborator($comment->getAuthor());

        $entityManager->persist($issue);
        $entityManager->flush();
    }

    /**
     * Create activity
     *
     * @param EntityManager $entityManager
     * @param Comment $comment
     */
    protected function createActivityFromComment($entityManager, $comment)
    {
        $activity = new IssueActivity();
        $activity
            ->setUser($comment->getAuthor())
            ->setIssue($comment->getIssue())
            ->setDetails($comment->getBody())
            ->setType(IssueActivity::ACTIVITY_COMMENT);

        $entityManager->persist($activity);
        $entityManager->flush($activity);
    }
}
