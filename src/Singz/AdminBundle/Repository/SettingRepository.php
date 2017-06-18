<?php

namespace Singz\AdminBundle\Repository;

/**
 * SettingsRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SettingRepository extends \Doctrine\ORM\EntityRepository
{
    public function getSettingById($id)
    {
        return $this->createQueryBuilder('s')
            ->where('s.id = :id')->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getSettingByName($name)
    {
        return $this->createQueryBuilder('s')
            ->where('s.name = :name')->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult();
    }
}