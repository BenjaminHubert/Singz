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
}
