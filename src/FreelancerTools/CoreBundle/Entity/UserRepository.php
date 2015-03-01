<?php

namespace FreelancerTools\CoreBundle\Entity;

use FreelancerTools\CoreBundle\Entity\EntityRepository;
use Doctrine\ORM\QueryBuilder;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends EntityRepository
{
    /**
     *
     * @param string            $text
     * @param QueryBuilder      $qb
     *
     * @return UserRepository
     */
    public function search($text, QueryBuilder $qb = null)
    {
        return $this;
    }

    /**
     *
     * @param                   $date
     * @param QueryBuilder      $qb
     *
     * @return UserRepository
     */
    public function scopeByDate($date, QueryBuilder $qb = null)
    {
        return $this;
    }

}
