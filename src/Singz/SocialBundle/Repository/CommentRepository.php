<?php

namespace Singz\SocialBundle\Repository;

/**
 * CommentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CommentRepository extends \Doctrine\ORM\EntityRepository
{
	public function findAllCommentsInfo(){
		$qb = $this->createQueryBuilder('c')
			->leftJoin('c.author', 'author')
				->addSelect('author')
			->leftJoin('c.thread', 'thread')
				->addSelect('thread')
			->leftJoin('thread.publication', 'publication')
				->addSelect('publication')
			
		;
		
		return $qb
			->getQuery()
			->getResult()
		;
	}

    public function getLastWeekComments($lastweek){
        return $this->createQueryBuilder('c')
            ->where('c.createdAt > :lastweek')
            ->setParameter('lastweek', $lastweek)
            ->getQuery()
            ->getResult()
        ;
    }
}
