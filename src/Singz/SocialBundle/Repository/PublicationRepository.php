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

    public function findTrending(){
        return $this->getEntityManager()
            ->createQuery(
                'SELECT * FROM publication'
            )
            ->getResult();
    }
}
