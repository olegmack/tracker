<?php

namespace Oro\ProjectBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends EntityRepository
{
    public function findByMember($userId)
    {
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p')
            ->join('p.users', 'u')
            ->where('u.id = :user_id')
            ->setParameter('user_id', $userId);

        return $queryBuilder->getQuery()->getResult();
    }
}
