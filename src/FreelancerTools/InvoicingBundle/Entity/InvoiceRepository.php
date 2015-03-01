<?php

namespace FreelancerTools\InvoicingBundle\Entity;

use Doctrine\ORM\EntityRepository;
use FreelancerTools\CoreBundle\Entity\User;

class InvoiceRepository extends EntityRepository {

    public function getInvoices(User $user, $recurring = false) {
        $qb = $this->createQueryBuilder("q");
        $qb
                ->select('i,ii,c,p,o')
                ->from('FreelancerToolsInvoicingBundle:Invoice', 'i')
                ->leftJoin('i.items', 'ii')
                ->leftJoin('i.customer', 'c')
                ->leftJoin('i.payments', 'p')
                ->leftJoin('p.order', 'o')
                ->andWhere('i.user = :user')
                ->setParameter(':user', $user->getId())
        ;

        if ($recurring) {
            $qb->andWhere('i.isRecurring = true');
        }

        return $qb->getQuery()->getResult();
    }

    public function getInvoice($id) {
        $qb = $this->createQueryBuilder("q");
        $qb
                ->select('i,ii,c,p,o')
                ->from('FreelancerToolsInvoicingBundle:Invoice', 'i')
                ->leftJoin('i.items', 'ii')
                ->leftJoin('i.customer', 'c')
                ->leftJoin('i.payments', 'p')
                ->leftJoin('p.order', 'o')
                ->where('i.id = :id')
                ->setParameter(':id', $id)
        ;
        try {
            return $qb->getQuery()->getSingleResult();
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function getReceivableInvoicesByYear(User $user, $year = null) {
        if (!$year) {
            $year = date('Y');
        }

        $qb = $this->createQueryBuilder("q");
        $qb
                ->select('i')
                ->from('FreelancerToolsInvoicingBundle:Invoice', 'i')
                ->andWhere('i.status >= 1')
                ->andWhere('i.invoiceDate >= :currentYear')
                ->andWhere('i.user = :user')
                ->setParameters(array(
                    ':currentYear' => $year . "-01-01 00-00-00",
                    ':user' => $user
                ))
        ;
        return $qb->getQuery()->getResult();
    }

}
