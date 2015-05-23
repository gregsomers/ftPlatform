<?php

namespace FreelancerTools\CoreBundle\Entity;

use Doctrine\ORM\EntityRepository as Base;
use Doctrine\ORM\QueryBuilder;

/**
 * EntityRepository
 *
 * Abstract repository to keep code clean.
 */
abstract class EntityRepository extends Base {

    /**
     * @var QueryBuilder
     */
    protected $builder;

    /**
     * Create a new current query builder
     *
     * @param $alias
     * @return EntityRepository
     */
    public function createCurrentQueryBuilder($alias) {
        $this->builder = $this->createQueryBuilder($alias);
        return $this;
    }

    /**
     * Return the current query builder
     *
     * @return QueryBuilder
     */
    public function getCurrentQueryBuilder() {
        return $this->builder;
    }

    /**
     * Set the current query builder
     *
     * @param QueryBuilder $qb
     * @return EntityRepository
     */
    public function setCurrentQueryBuilder(QueryBuilder $qb) {
        $this->builder = $qb;
        return $this;
    }

    /**
     * Scope by any field with value
     *
     * @param $field
     * @param $value
     * @param QueryBuilder $qb
     * @return EntityRepository
     */
    public function scopeByField($field, $value, QueryBuilder $qb = null) {
        if ($qb == null) {
            $qb = $this->builder;
        }
        $aliases = $qb->getRootAliases();
        $alias = array_shift($aliases);
        $qb->andWhere(
                $qb->expr()->eq($alias . '.' . $field, ':' . $field)
        );
        $qb->setParameter($field, $value);
        return $this;
    }

    /**
     * Check if a join alias already taken. getRootAliases() doesn't do this for me.
     *
     * @param \Doctrine\ORM\QueryBuilder $qb, QueryBuilder you wanne check
     * @param                            $alias, alias string
     *
     * @return bool
     */
    public function existsJoinAlias(QueryBuilder $qb, $alias) {
        $result = false;
        foreach ($qb->getDQLPart('join') as $joins) {
            foreach ($joins as $j) {
                $join = $j->__toString();
                if (substr($join, strrpos($join, ' ') + 1) == $alias) {
                    $result = true;
                    break;
                }
            }
            if ($result) {
                break;
            }
        }
        return $result;
    }

    public function addLeftJoins($fields, QueryBuilder $qb = null) {
        if ($qb == null) {
            $qb = $this->builder;
        }

        $aliases = $qb->getRootAliases();
        $alias = array_shift($aliases);
        foreach ($fields as $key => $field) {
            $qb->addSelect($key);
            if (strpos($field, '.') !== false) {
                $qb->leftJoin($field, $key);
            } else {
                $qb->leftJoin($alias . "." . $field, $key);
            }
        }

        return $this;
    }

    public function getSingleResult(QueryBuilder $qb = null) {
        if ($qb == null) {
            $qb = $this->builder;
        }

        try {
            return $qb->getQuery()->getSingleResult();
        } catch (\Doctrine\ORM\NoResultException $e) {
            return null;
        }
    }

    public function getResults(QueryBuilder $qb = null) {
        if ($qb == null) {
            $qb = $this->builder;
        }

        return $qb->getQuery()->execute();
    }

}
