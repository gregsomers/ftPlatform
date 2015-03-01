<?php

namespace FreelancerTools\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Order as BaseOrder;

/**
 * @ORM\Table(name="online_order")
 * @ORM\Entity
 */
class Order extends BaseOrder {

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var integer $id
     */
    protected $id;
    
    /**
     * @ORM\OneToOne(targetEntity="FreelancerTools\PaymentBundle\Entity\Payment", inversedBy="order", cascade="persist")     
     */
    protected $payment;   
    
    
    /**
     * set payment
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\Payment $payment
     * @return Order
     */
    public function setPayment(\FreelancerTools\PaymentBundle\Entity\Payment $payment = null) {
        $this->payment = $payment;
        return $this;
    }
    
    public function getPayment() {
        return $this->payment;
    }
        

}
