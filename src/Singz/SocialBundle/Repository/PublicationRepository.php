<?php
namespace Singz\SocialBundle\Repository;
use Singz\SocialBundle\Entity\Publication;

/**
 * PublicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PublicationRepository extends \Doctrine\ORM\EntityRepository
{    
    public function getPublicationById($id) {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')->addSelect('u')
            ->innerJoin('p.owner', 'o')->addSelect('o')
            ->leftJoin('p.loves', 'l')->addSelect('l')
            ->leftJoin('p.thread', 't')->addSelect('t')
            ->where('p.id = :id')->setParameter('id', $id)
            ->andWhere('p.state = :state')->setParameter('state', Publication::STATE_VISIBLE)
            ->andWhere('u.enabled = :isEnabled')->setParameter('isEnabled', true)
            ->andWhere('o.enabled = :isEnabled')->setParameter('isEnabled', true)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    public function getPublicationByHashtag($user, $tag) {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')->addSelect('u')
            ->leftJoin('p.owner', 'o')->addSelect('o')
            ->leftJoin('u.leaders', 'leaders')->addSelect('leaders')
            ->where('p.description LIKE :tag AND (leaders.follower = :follower AND leaders.isPending = :pending)')
            ->setParameter('tag', "%#".$tag."%")
            ->setParameter('follower', $user)
            ->setParameter('pending', false)
            ->andWhere('u.enabled = :isEnabled')->setParameter('isEnabled', true)
            ->andWhere('o.enabled = :isEnabled')->setParameter('isEnabled', true)
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
    
    public function getResingz($video) {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')->addSelect('u')
            ->innerJoin('p.owner', 'o')->addSelect('o')
            ->where('p.video = :video AND p.isResingz = :resingz')
            ->setParameter('video', $video)
            ->setParameter('resingz', true)
            ->andWhere('u.enabled = :isEnabled')->setParameter('isEnabled', true)
            ->andWhere('o.enabled = :isEnabled')->setParameter('isEnabled', true)
            ->andWhere('p.state = :state')->setParameter('state', Publication::STATE_VISIBLE)
            ->getQuery()
            ->getResult();
    }
    
	public function getPublications($user, $filter = 'all', $offset = 0, $limit = 0, $userId = null){
		$queryBuilder = $this->createQueryBuilder('p')
			// only enabled users
			->leftJoin('p.user', 'user', 'WITH', 'user.enabled = :userIsEnabled')
				->setParameter('userIsEnabled', true)
				->addSelect('user')
			// get user's image
			->leftJoin('user.image', 'image')
				->addSelect('image')
			// only enabled owners
			->leftJoin('p.owner', 'owner', 'WITH', 'owner.enabled = :ownerIsEnabled')
				->setParameter('ownerIsEnabled', true)
				->addSelect('owner')
			// get loves
			->leftJoin('p.loves', 'loves')
				->addSelect('loves')
			// get thread
			->leftJoin('p.thread', 'thread')
				->addSelect('thread')
			// get video
			->leftJoin('p.video', 'video')
				->addSelect('video')
			// only visible publications
			->andWhere('p.state = :state')
				->setParameter('state', Publication::STATE_VISIBLE)
			->groupBy('p.id')
			// set max result
			->setFirstResult($offset)
		    ->setMaxResults($limit)
		;
		// Apply filters
		if($filter == 'starz' || $filter == 'singzer' || $filter == 'all'){
			$queryBuilder
				->orderBy('p.numLoves', 'DESC')
				->addOrderBy('p.date', 'DESC')
			;
		}
		if($filter == 'starz'){
			$queryBuilder
				->andWhere('user.roles LIKE :starz')
					->setParameter('starz', '%STARZ%')
			;
		}
		if($filter == 'singzer'){
			$queryBuilder
				->andWhere('user.roles LIKE :singzer')
					->setParameter('singzer', '%SINGZER%')
			;
		}
		if($filter == 'singzer' || $filter == 'all'){
			$queryBuilder
				->leftJoin('user.followers', 'f')->addSelect('f')
					->andWhere('user.isPrivate = :private OR (f.follower = :follower AND f.isPending = :pending)')
						->setParameter('private', false)
						->setParameter('follower', $user)
						->setParameter('pending', false)
			;
		}
		if($filter == 'feed'){
			$queryBuilder
				->leftJoin('user.leaders', 'leaders')
					->addSelect('leaders')
				->andWhere('(leaders.follower = :follower AND leaders.isPending = :pending) OR p.user = :user')
					->setParameter('follower', $user)
					->setParameter('user', $user)
					->setParameter('pending', false)
				->orderBy('p.date', 'DESC')
			;
		}
		if($filter ='user' && $userId != null){
			$queryBuilder
				->andWhere('user.id = :userId')
					->setParameter('userId', $userId)
			;
		}
		
		return $queryBuilder
			->getQuery()
			->getResult()
		;
	}

}
