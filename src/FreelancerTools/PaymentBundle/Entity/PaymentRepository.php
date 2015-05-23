<?php

namespace FreelancerTools\PaymentBundle\Entity;

use FreelancerTools\CoreBundle\Entity\EntityRepository as Base;
use Doctrine\ORM\NoResultException;
use FreelancerTools\CoreBundle\Entity\User;

class PaymentRepository extends Base {
    
    public function getPaymentsByYear(User $user, $year = null) {
        if(!$year) {
            $year = date('Y');
        }
        
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb
                ->select('p')
                ->from('FreelancerToolsPaymentBundle:Payment', 'p')                
                ->andWhere('p.date >= :currentYear')
                ->andWhere('p.date < :nextYear')
                ->andWhere('p.user = :user')  
                ->setParameters(array(
                    ':currentYear' => $year . "-01-01 00-00-00",
                    ':nextYear' => ($year + 1) . "-01-01 00-00-00",
                    ':user' => $user
                ))
        ;    
        
        return $qb->getQuery()->execute();               
        
    }    

}
