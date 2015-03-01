<?php

namespace FreelancerTools\InvoicingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FreelancerTools\CoreBundle\Entity\Entity;

/**
 * Dime\TimetrackerBundle\Entity\Invoice
 *
 * @ORM\Table(name="quote")
 * @ORM\Entity()
 */
class Quote extends Entity {

    /**
     * @var Customer $customer
     *
     * @ORM\ManyToOne(targetEntity="FreelancerTools\CoreBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $customer;

    /**
     * @ORM\OneToMany(targetEntity="QuoteItem", mappedBy="invoice", cascade="persist")
     * //@ORM\OrderBy({"startedAt" = "DESC"})
     */
    protected $items;    

    /**
     * 
     * @ORM\Column(type="datetime")
     */
    protected $quoteDate;

    /**
     * 
     * @ORM\Column(type="datetime")
     */
    protected $quoteExpiresDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $quoteNumber;
    
    /**     
     * @ORM\ManyToOne(targetEntity="FreelancerTools\PaymentBundle\Entity\Currency")
     * @ORM\JoinColumn(name="currency_id", referencedColumnName="id", nullable=false)
     */
    protected $currency;
    
    /**
     * @ORM\Column(type="text")
     */
    protected $terms;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $token;
   

    /**
     * Entity constructor
     */
    public function __construct() {
        $this->token = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 50);        
        $this->items = new ArrayCollection();        
    }  
    
    public function getStatusString() {

        $statusCodes = array(
            '0' => 'Draft',
            '1' => 'Sent',
            '2' => 'Approved',
            '3' => 'Rejected',
            '4' => 'Cancelled'
        );

        return $statusCodes[$this->status];
    }


    /**
     * Set quoteDate
     *
     * @param \DateTime $quoteDate
     * @return Quote
     */
    public function setQuoteDate($quoteDate)
    {
        $this->quoteDate = $quoteDate;

        return $this;
    }

    /**
     * Get quoteDate
     *
     * @return \DateTime 
     */
    public function getQuoteDate()
    {
        return $this->quoteDate;
    }

    /**
     * Set quoteExpiresDate
     *
     * @param \DateTime $quoteExpiresDate
     * @return Quote
     */
    public function setQuoteExpiresDate($quoteExpiresDate)
    {
        $this->quoteExpiresDate = $quoteExpiresDate;

        return $this;
    }

    /**
     * Get quoteExpiresDate
     *
     * @return \DateTime 
     */
    public function getQuoteExpiresDate()
    {
        return $this->quoteExpiresDate;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Quote
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set quoteNumber
     *
     * @param string $quoteNumber
     * @return Quote
     */
    public function setQuoteNumber($quoteNumber)
    {
        $this->quoteNumber = $quoteNumber;

        return $this;
    }

    /**
     * Get quoteNumber
     *
     * @return string 
     */
    public function getQuoteNumber()
    {
        return $this->quoteNumber;
    }

    /**
     * Set terms
     *
     * @param string $terms
     * @return Quote
     */
    public function setTerms($terms)
    {
        $this->terms = $terms;

        return $this;
    }

    /**
     * Get terms
     *
     * @return string 
     */
    public function getTerms()
    {
        return $this->terms;
    }

    /**
     * Set token
     *
     * @param string $token
     * @return Quote
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string 
     */
    public function getToken()
    {
        return $this->token;
    }    

    /**
     * Set customer
     *
     * @param \FreelancerTools\CoreBundle\Entity\Customer $customer
     * @return Quote
     */
    public function setCustomer(\FreelancerTools\CoreBundle\Entity\Customer $customer = null)
    {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \FreelancerTools\CoreBundle\Entity\Customer 
     */
    public function getCustomer()
    {
        return $this->customer;
    }

    /**
     * Add items
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\QuoteItem $items
     * @return Quote
     */
    public function addItem(\FreelancerTools\InvoicingBundle\Entity\QuoteItem $items)
    {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\QuoteItem $items
     */
    public function removeItem(\FreelancerTools\InvoicingBundle\Entity\QuoteItem $items)
    {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set currency
     *
     * @param \FreelancerTools\PaymentBundle\Entity\Currency $currency
     * @return Quote
     */
    public function setCurrency(\FreelancerTools\PaymentBundle\Entity\Currency $currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return \FreelancerTools\PaymentBundle\Entity\Currency 
     */
    public function getCurrency()
    {
        return $this->currency;
    }   
}
