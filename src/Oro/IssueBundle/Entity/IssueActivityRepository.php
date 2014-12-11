<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * IssueActivityRepository
 */
class IssueActivityRepository extends EntityRepository
{
    /**
     * Get latest activity
     *
     * @param int $limit
     * @param int $userId
     * @param int $projectId
     * @param int $issueId
     * @return array
     */
    public function findByParameters($limit = null, $userId = null, $projectId = null, $issueId = null)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('a')
            ->addOrderBy('a.createdAt', 'DESC');

        if (!is_null($limit)) {
            $queryBuilder->setMaxResults($limit);
        }

        if (!is_null($userId)) {
            $queryBuilder->where('a.user = :userId')
                ->setParameter('userId', $userId);
        }

        if (!is_null($projectId)) {
            $queryBuilder
                ->join('a.issue', 'issue')
                ->where('issue.project = :projectId')
                ->setParameter('projectId', $projectId);
        }

        if (!is_null($issueId)) {
            $queryBuilder->where('a.issue = :issueId')
                ->setParameter('issueId', $issueId);
        }

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Find activities which related to projects where user is a member
     *
     * @param $userId
     * @return array
     */
    public function findByProjectMember($userId)
    {
        $queryBuilder = $this->createQueryBuilder('a')
            ->select('a')
            ->addOrderBy('a.createdAt', 'DESC')
            ->join('a.issue', 'i')
            ->join('i.project', 'p')
            ->join('p.users', 'u')
            ->where('u.id = :user_id')
            ->setParameter('user_id', $userId);

        return $queryBuilder->getQuery()->getResult();
    }
}
