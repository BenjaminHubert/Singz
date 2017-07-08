<?php

namespace Singz\CoreBundle\Repository;

/**
 * ProjectRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ProjectRepository extends \Doctrine\ORM\EntityRepository
{
	public function getProject($id){
		return $this->createQueryBuilder('p')
			// contributions
			->leftJoin('p.contributions', 'c', 'WITH', 'c.isValidated = :contributionValidated')
				->addSelect('c')
				->setParameter('contributionValidated', true)
			// id project
			->andWhere('p.id = :id')
				->setParameter('id', $id)
		
			->getQuery()
			->getOneOrNullResult()
		;
	}

    public function findAllProjectsInfo(){
        return $this->createQueryBuilder('p')
            ->leftJoin('p.requester', 'u')->addSelect('u')
            ->getQuery()
            ->getResult();
    }
}
