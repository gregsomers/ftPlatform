<?php

namespace FreelancerTools\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FreelancerTools\CoreBundle\Entity\Entity;
use \DateTime;

/** 
 * @ORM\Table(name="payments")
 * @ORM\Entity(repositoryClass="FreelancerTools\PaymentBundle\Entity\PaymentRepository")
 */
class Payment extends Entity {
    
    /**     
     * 
     * @ORM\ManyToOne(targetEntity="FreelancerTools\InvoicingBundle\Entity\Invoice", inversedBy="payments", cascade="persist")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $invoice;
    
    /**     
     * 
     * @ORM\ManyToOne(targetEntity="PaymentMethod")
     * @ORM\JoinColumn(name="method_id", referencedColumnName="id", nullable=false)
     */
    protected $method;
    
    /**
     * 
     * @ORM\Column(name="date", type="datetime")
     */
    protected $date;
    
    /**    
     * @ORM\Column(type="decimal", scale=2, precision=10, nullable=true)
     */
    protected $amount;
    
    /**
     * 
     * @ORM\Column(type="text", nullable=true)
     */
    protected $notes;
    
    /**
     * @ORM\OneToOne(targetEntity="FreelancerTools\PaymentBundle\Entity\Order", mappedBy="payment", cascade="persist")     
     */
    protected $order;   
    

    /**
     * Entity constructor
     */
    public function __construct() {
        
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Payment
     */
    public function setDate($date)
    {
        if (!$date instanceof DateTime && !empty($date)) {
            $date = new DateTime($date);
        }
        
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set amount
     *
     * @param string $amount
     * @return Payment
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get amount
     *
     * @return string 
     */
    public function getAmount()
    {
        return $this->amount;
    }
    

    /**
     * Set invoice
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\Invoice $invoice
     * @return Payment
     */
    public function setInvoice(\FreelancerTools\InvoicingBundle\Entity\Invoice $invoice)
    {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \FreelancerTools\InvoicingBundle\Entity\Invoice 
     */
    public function getInvoice()
    {
        return $this->invoice;
    }

    /**
     * Set method
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\PaymentMethod $method
     * @return Payment
     */
    public function setMethod(\FreelancerTools\PaymentBundle\Entity\PaymentMethod $method)
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Get method
     *
     * @return \FreelancerTools\InvoicingBundle\Entity\PaymentMethod 
     */
    public function getMethod()
    {
        return $this->method;
    }    
   
    /**
     * Set notes
     *
     * @param string $notes
     * @return Payment
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes()
    {
        return $this->notes;
    }
    
}
