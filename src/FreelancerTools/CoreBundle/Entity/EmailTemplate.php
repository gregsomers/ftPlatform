<?php

namespace FreelancerTools\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FreelancerTools\CoreBundle\Entity\Entity;

/** 
 * @ORM\Table(name="emailTemplates")
 * @ORM\Entity()
 */
class EmailTemplate extends Entity {    
    
    /**     
     * @ORM\Column(type="string", length=255)
     */
    protected $name;
    
       
    /**
     * 
     * @ORM\Column(type="text", nullable=false)
     */
    protected $body;   
    

    /**
     * Entity constructor
     */
    public function __construct() {
        
    }


    /**
     * Set name
     *
     * @param string $name
     * @return EmailTemplate
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

    /**
     * Set body
     *
     * @param string $body
     * @return EmailTemplate
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string 
     */
    public function getBody()
    {
        return $this->body;
    }

}
