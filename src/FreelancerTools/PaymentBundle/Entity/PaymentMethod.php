<?php

namespace FreelancerTools\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FreelancerTools\CoreBundle\Entity\Entity;

/** 
 * @ORM\Table(name="payment_methods")
 * @ORM\Entity()
 */
class PaymentMethod extends Entity {      
    
    /**     
     * @ORM\Column(type="string", length=255)
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
