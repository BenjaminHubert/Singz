<?php
namespace Singz\SocialBundle\Repository;
/**
 * PublicationRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PublicationRepository extends \Doctrine\ORM\EntityRepository
{
    public function getNewsFeed($user) {
        return $this->createQueryBuilder('p')
            ->leftJoin('p.user', 'u')->addSelect('u')
            ->leftJoin('u.leaders', 'leaders')->addSelect('leaders')
            ->leftJoin('p.loves', 'l')->addSelect('l')
            ->where('(leaders.follower = :follower AND leaders.isPending = :pending) OR p.user = :user')
            ->setParameter('follower', $user)
            ->setParameter('user', $user)
            ->setParameter('pending', false)
            ->orderBy('p.date', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function getPublicationById($id) {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')->addSelect('u')
            ->innerJoin('p.owner', 'o')->addSelect('o')
            ->leftJoin('p.loves', 'l')->addSelect('l')
            ->leftJoin('p.thread', 't')->addSelect('t')
            ->where('p.id = :id')->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function getBrowseAll($offset, $limit, $interval, $user) {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')->addSelect('u')
            ->leftJoin('u.followers', 'f')->addSelect('f')
            ->leftJoin('p.loves', 'l')->addSelect('l')
            //->where('p.date > :interval')
            ->where('u.isPrivate = :private OR (f.follower = :follower AND f.isPending = :pending)')
            //->setParameter('interval', $interval)
            ->setParameter('private', false)
            ->setParameter('follower', $user)
            ->setParameter('pending', false)
            ->orderBy('p.numLoves', 'DESC')
            ->addOrderBy('p.date', 'DESC')
            //->setFirstResult($offset)
            //->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function getBrowseStarz($offset, $limit, $interval) {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')->addSelect('u')
            ->leftJoin('p.loves', 'l')->addSelect('l')
            //->where('p.date > :interval')->setParameter('interval', $interval)
            ->where('u.roles LIKE :starz')->setParameter('starz', '%STARZ%')
            ->orderBy('p.numLoves', 'DESC')
            ->addOrderBy('p.date', 'DESC')
            //->setFirstResult($offset)
            //->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function getBrowseSingzers($offset, $limit, $interval, $user) {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.user', 'u')->addSelect('u')
            ->leftJoin('u.followers', 'f')->addSelect('f')
            ->leftJoin('p.loves', 'l')->addSelect('l')
            //->where('p.date > :interval')->setParameter('interval', $interval)
            ->where('u.roles LIKE :singzer')->setParameter('singzer', '%SINGZER%')
            ->andWhere('u.isPrivate = :private OR (f.follower = :follower AND f.isPending = :pending)')
            ->setParameter('private', false)
            ->setParameter('follower', $user)
            ->setParameter('pending', false)
            ->orderBy('p.numLoves', 'DESC')
            ->addOrderBy('p.date', 'DESC')
            //->setFirstResult($offset)
            //->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
    public function getResingz($video) {
        return $this->createQueryBuilder('p')
            ->where('p.video = :video AND p.isResingz = :resingz')
            ->setParameter('video', $video)
            ->setParameter('resingz', true)
            ->getQuery()
            ->getResult();
    }
}
