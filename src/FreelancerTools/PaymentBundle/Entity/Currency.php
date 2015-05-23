<?php

namespace FreelancerTools\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FreelancerTools\CoreBundle\Entity\Entity;

/** 
 * @ORM\Table(name="payment_currency")
 * @ORM\Entity(repositoryClass="FreelancerTools\PaymentBundle\Entity\CurrencyRepository")
 */
class Currency extends Entity {      
    
    /**     
     * @ORM\Column(type="string", length=3)
     */
    protected $name;
    

    /**
     * Entity constructor
     */
    public function __construct() {
        
    }
    
    public function __toString() {
        return $this->name;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return PaymentMethod
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }  
    
}
