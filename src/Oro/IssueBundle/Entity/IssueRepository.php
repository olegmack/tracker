<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * IssueRepository
 */
class IssueRepository extends EntityRepository
{
    /**
     * Find issues by collaborator
     *
     * @param int $userId
     * @return Issue[]
     */
    public function findByCollaborator($userId)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select('i')
            ->join('i.issueStatus', 't')
            ->where("t.code IN ('open', 'reopened')")
            ->addOrderBy('i.createdAt', 'DESC')
            ->join('i.collaborators', 'u')
            ->andWhere('u.id = :user_id')
            ->setParameter('user_id', $userId);

        return $queryBuilder->getQuery()->getResult();
    }

    /**
     * Find issue by id assigned to the project with projectId
     *
     * @param int $projectId
     * @param string $type
     * @param int $excludeId
     * @return Issue[]
     */
    public function getIssuesByProjectId($projectId, $excludeId = 0, $type = 'story')
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select('i')
            ->join('i.project', 'p')
            ->join('i.issueType', 't')
            ->where('p.id = :project_id')
            ->andWhere("t.code = :type")
            ->setParameters(array('project_id' => $projectId, 'type' => $type));

        if (!empty($excludeId)) {
            $queryBuilder->andWhere('i.id != (:exclude_id)');
            $queryBuilder->setParameter('exclude_id', $excludeId);
        }

        return $queryBuilder;
    }

    /**
     * Find parent story issue
     *
     * @param int $id
     * @return Issue
     */
    public function findParentStory($id)
    {
        $queryBuilder = $this->createQueryBuilder('i')
            ->select('i')
            ->join('i.issueType', 'it')
            ->where('it.code = "story"')
            ->where('i.id = :id')
            ->setParameter('id', $id);

        return $queryBuilder->getQuery()->getSingleResult();
    }
}
