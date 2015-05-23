<?php

namespace FreelancerTools\TimeTrackerBundle\Entity;

use FreelancerTools\CoreBundle\Entity\EntityRepository as Base;

class TimesliceRepository extends Base
{
    public function getTimeslicesByProject($project) {
        $qb = $this->getEntityManager()->createQueryBuilder();   
        $qb
                ->select('s,p,a')
                ->from('FreelancerToolsTimeTrackerBundle:Timeslice', 's')
                ->leftJoin('s.activity', 'a')
                ->leftJoin('a.project', 'p')
                ->andwhere('p.id = :id')
                ->andwhere('a.archived = false')
                ->addOrderBy('s.startedAt', 'desc')
                ->setParameters(array(                    
                    ':id' => $project
                ))
        ;
        
        return $qb->getQuery()->execute();     
    }
}
