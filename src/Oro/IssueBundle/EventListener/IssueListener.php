<?php
namespace Oro\IssueBundle\EventListener;

use Oro\IssueBundle\Entity\Issue;
use Doctrine\ORM\EntityManager;
use Oro\IssueBundle\Entity\IssueActivity;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Oro\UserBundle\DependencyInjection\UserCallable;

class IssueListener
{
    /**
     * @var UserCallable
     */
    private $userCallable;

    /**
     * @var array
     */
    private $persistObjects;

    /**
     * @param UserCallable $userCallable
     */
    public function __construct(UserCallable $userCallable)
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
            $this->persistObjects[] = $this->createActivity($this->getUser(), $entity, IssueActivity::ACTIVITY_ISSUE_STATUS, $details);
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
        if(!empty($this->persistObjects)) {
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

        /** @var Issue $entity */
        //track activity
        $activity = $this->createActivity($this->getUser(), $entity, IssueActivity::ACTIVITY_ISSUE);

        $em = $args->getEntityManager();
        $em->persist($activity);
        $em->flush();
    }
}