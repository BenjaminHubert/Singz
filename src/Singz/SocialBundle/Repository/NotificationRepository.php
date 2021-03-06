<?php

namespace Singz\SocialBundle\Repository;

use Singz\UserBundle\Entity\User;

/**
 * NotificationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotificationRepository extends \Doctrine\ORM\EntityRepository
{
	public function getNotificationsByUser(User $user){
		return $this->createQueryBuilder('n')
			->leftJoin('n.userFrom', 'userFrom')->addSelect('userFrom')
			->leftJoin('userFrom.image', 'imageUserFrom')->addSelect('imageUserFrom')
			->leftJoin('n.userTo', 'userTo')->addSelect('userTo')
			->leftJoin('userTo.image', 'imageUserTo')->addSelect('imageUserTo')
			->leftJoin('n.publication', 'p')->addSelect('p')
			->leftJoin('p.user', 'publication_user')->addSelect('publication_user')
			->andWhere('userTo = :userTo')
			->setParameter('userTo', $user)
			->orderBy('n.date', 'DESC')
			->getQuery()
			->getResult();
	}
}
