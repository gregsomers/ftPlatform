<?php

namespace FreelancerTools\InvoicingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FreelancerTools\CoreBundle\Entity\Entity;

/**
 * @ORM\Table(name="quote_item")
 * @ORM\Entity()
 */
class QuoteItem extends Entity {
    
    /**     
     * 
     * @ORM\ManyToOne(targetEntity="Quote", inversedBy="items", cascade="persist")
     * @ORM\JoinColumn(name="quote_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $quote;
    
    /**     
     * @ORM\Column(type="string", length=255)
     */
    protected $product;
    
    /**     
     * @ORM\Column(type="text")
     */
    protected $description;
    
    /**     
     * @ORM\Column(type="decimal", scale=2, precision=10, nullable=true)
     */
    protected $quantity;
    
    /**    
     * @ORM\Column(type="decimal", scale=2, precision=10, nullable=true)
     */
    protected $price;    
    

    /**
     * Entity constructor
     */
    public function __construct() {
            
    }

    /**
     * Set product
     *
     * @param string $product
     * @return QuoteItem
     */
    public function setProduct($product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return string 
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return QuoteItem
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set quantity
     *
     * @param string $quantity
     * @return QuoteItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string 
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return QuoteItem
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice()
    {
        return $this->price;
    }    

    /**
     * Set quote
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\Quote $quote
     * @return QuoteItem
     */
    public function setQuote(\FreelancerTools\InvoicingBundle\Entity\Quote $quote)
    {
        $this->quote = $quote;

        return $this;
    }

    /**
     * Get quote
     *
     * @return \FreelancerTools\InvoicingBundle\Entity\Quote 
     */
    public function getQuote()
    {
        return $this->quote;
    }   
}
