<?php

namespace Oro\IssueBundle\EventListener;

use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

use Oro\IssueBundle\Entity\Issue;
use Oro\UserBundle\Entity\User;
use Oro\IssueBundle\Entity\IssueActivity;
use Oro\UserBundle\Provider\UserProvider;

class IssueListener
{
    /**
     * @var UserProvider
     */
    private $userCallable;

    /**
     * @var array
     */
    private $persistObjects;

    /**
     * @param UserProvider $userCallable
     */
    public function __construct(UserProvider $userCallable)
    {
        $this->userCallable = $userCallable;
    }

    /**
     * @param PreUpdateEventArgs $args
     */
    public function preUpdate(PreUpdateEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->isIssueEntity($entity)) {
            return;
        }

        if ($args->hasChangedField('issueStatus')) {
            $details = sprintf(
                'changed status from "%s" to "%s"',
                $args->getOldValue('issueStatus')->getName(),
                $args->getNewValue('issueStatus')->getName()
            );

            /** var Issue $entity */
            $this->persistObjects[] = $this->createActivity(
                $this->getUser(),
                $entity,
                IssueActivity::ACTIVITY_ISSUE_STATUS,
                $details
            );
        }
    }

    /**
     * @param object $entity
     * @return bool
     */
    protected function isIssueEntity($entity)
    {
        return $entity instanceof Issue;
    }

    /**
     * Create activity
     *
     * @param User $user
     * @param Issue $issue
     * @param string $type
     * @param string $details
     * @return \Oro\IssueBundle\Entity\IssueActivity
     */
    protected function createActivity($user, $issue, $type, $details = '')
    {
        $activity = new IssueActivity();
        $activity
            ->setUser($user)
            ->setIssue($issue)
            ->setDetails($details)
            ->setType($type);

        return $activity;
    }

    /**
     * Get current user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->userCallable->getCurrentUser();
    }

    /**
     * @param PostFlushEventArgs $args
     */
    public function postFlush(PostFlushEventArgs $args)
    {
        if (!empty($this->persistObjects)) {
            $em = $args->getEntityManager();
            foreach ($this->persistObjects as $object) {
                $em->persist($object);
            }

            $this->persistObjects = [];
            $em->flush();
        }
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->isIssueEntity($entity)) {
            return;
        }

        //add assignee as collaborator
        $entity->addCollaborator($entity->getAssignee());

        /** @var Issue $entity */
        //track activity
        $modifiedBy = $entity->getModifiedBy();
        if (empty($modifiedBy)) {
            $modifiedBy = $this->getUser();
        }

        $activity = $this->createActivity($modifiedBy, $entity, IssueActivity::ACTIVITY_ISSUE);

        $em = $args->getEntityManager();
        $em->persist($activity);
        $em->flush();
    }

    /**
     * @param OnFlushEventArgs $args
     */
    public function onFlush(OnFlushEventArgs $args)
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates()
        );

        foreach ($entities as $entity) {
            if (!$this->isIssueEntity($entity)) {
                continue;
            }

            $entity->addCollaborator($entity->getAssignee());
            $meta = $em->getClassMetadata(get_class($entity));
            $uow->computeChangeSet($meta, $entity);
        }
    }
}
