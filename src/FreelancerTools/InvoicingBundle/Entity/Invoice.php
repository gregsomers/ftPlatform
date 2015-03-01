<?php

namespace FreelancerTools\InvoicingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FreelancerTools\CoreBundle\Entity\Entity;

/** 
 * @ORM\Table(name="invoices")
 * @ORM\Entity(repositoryClass="FreelancerTools\InvoicingBundle\Entity\InvoiceRepository")
 */
class Invoice extends Entity {

    /**
     * @var Customer $customer
     *
     * @ORM\ManyToOne(targetEntity="FreelancerTools\CoreBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $customer;

    /**
     * @ORM\OneToMany(targetEntity="InvoiceItem", mappedBy="invoice", cascade="persist")
     * //@ORM\OrderBy({"startedAt" = "DESC"})
     */
    protected $items;

    /**
     * @ORM\OneToMany(targetEntity="FreelancerTools\PaymentBundle\Entity\Payment", mappedBy="invoice", cascade="persist")     
     */
    protected $payments;

    /**
     * 
     * @ORM\Column(name="invoice_date", type="datetime")
     */
    protected $invoiceDate;

    /**
     * 
     * @ORM\Column(name="due_date", type="datetime")
     */
    protected $invoiceDueDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $status;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $invoiceNumber;
    
    /**
     * @var Customer $customer
     *
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
     * @var ArrayCollection $timeslices    
     * @ORM\OneToMany(targetEntity="FreelancerTools\TimeTrackerBundle\Entity\Timeslice", mappedBy="invoice")
     * @ORM\OrderBy({"startedAt" = "DESC"})
     */
    protected $timeslices;
    
    /**
     * @ORM\Column(name="showTimelog", type="boolean", options={"default" = 1})
     */
    protected $showTimelog = false;
    
    /**
     * @ORM\Column(type="boolean", options={"default" = 0})
     */
    protected $isRecurring = false;
    
   /**
     * 
     * @ORM\Column(type="datetime")
     */
    protected $duration;

    /**
     * Entity constructor
     */
    public function __construct() {
        $this->token = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 50);
        $this->payments = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->timeslices = new ArrayCollection();
    }
    
    public function setShowTimelog($showTimelog) {
        $this->showTimelog = $showTimelog;

        return $this;
    }

    public function getShowTimelog() {
        return $this->showTimelog;
    }

    public function __toString() {
        return $this->customer->getName() . ": " . $this->description;
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->getTotal();
        }
        return $total;
    }
    
    public function getPaid() {
        $paid = 0;
        foreach ($this->payments as $payment) {
            $paid += $payment->getAmount();
        }
        return $paid;
    }

    public function getBalance() {               
        return $this->getTotal() - $this->getPaid();
    }

    public function getStatusString() {

        $statusCodes = array(
            '0' => 'Draft',
            '1' => 'Open',
            '2' => 'Paid',
            '3' => 'Cancelled',
            '4' => 'Overdue'
        );

        return $statusCodes[$this->status];
    }

    /**
     * Set rate
     *
     * @param string $rate
     * @return Invoice
     */
    public function setRate($rate) {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return string 
     */
    public function getRate() {
        return $this->rate;
    }

    /**
     * Set rateReference
     *
     * @param string $rateReference
     * @return Invoice
     */
    public function setRateReference($rateReference) {
        $this->rateReference = $rateReference;

        return $this;
    }

    /**
     * Get rateReference
     *
     * @return string 
     */
    public function getRateReference() {
        return $this->rateReference;
    }

    /**
     * Set invoiceDate
     *
     * @param \DateTime $invoiceDate
     * @return Invoice
     */
    public function setInvoiceDate($invoiceDate) {
        $this->invoiceDate = $invoiceDate;

        return $this;
    }

    /**
     * Get invoiceDate
     *
     * @return \DateTime 
     */
    public function getInvoiceDate() {
        return $this->invoiceDate;
    }

    /**
     * Set invoiceDueDate
     *
     * @param \DateTime $invoiceDueDate
     * @return Invoice
     */
    public function setInvoiceDueDate($invoiceDueDate) {
        $this->invoiceDueDate = $invoiceDueDate;

        return $this;
    }

    /**
     * Get invoiceDueDate
     *
     * @return \DateTime 
     */
    public function getInvoiceDueDate() {
        return $this->invoiceDueDate;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Invoice
     */
    public function setStatus($status) {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Set invoiceNumber
     *
     * @param string $invoiceNumber
     * @return Invoice
     */
    public function setInvoiceNumber($invoiceNumber) {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * Get invoiceNumber
     *
     * @return string 
     */
    public function getInvoiceNumber() {
        return $this->invoiceNumber;
    }

    /**
     * Set customer
     *
     * @param \FreelancerTools\CoreBundle\Entity\Customer $customer
     * @return Invoice
     */
    public function setCustomer(\FreelancerTools\CoreBundle\Entity\Customer $customer = null) {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return \FreelancerTools\CoreBundle\Entity\Customer 
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * Add items
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\InvoiceItem $items
     * @return Invoice
     */
    public function addItem(\FreelancerTools\InvoicingBundle\Entity\InvoiceItem $items) {
        $this->items[] = $items;

        return $this;
    }

    /**
     * Remove items
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\InvoiceItem $items
     */
    public function removeItem(\FreelancerTools\InvoicingBundle\Entity\InvoiceItem $items) {
        $this->items->removeElement($items);
    }

    /**
     * Get items
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getItems() {
        return $this->items;
    }

    /**
     * Add payments
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\Payment $payments
     * @return Invoice
     */
    public function addPayment(\FreelancerTools\PaymentBundle\Entity\Payment $payments) {
        $this->payments[] = $payments;

        return $this;
    }

    /**
     * Remove payments
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\Payment $payments
     */
    public function removePayment(\FreelancerTools\PaymentBundle\Entity\Payment $payments) {
        $this->payments->removeElement($payments);
    }

    /**
     * Get payments
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPayments() {
        return $this->payments;
    }

    public function setToken($token) {
        $this->token = $token;

        return $this;
    }

    public function getToken() {
        return $this->token;
    }
    
    public function setTerms($terms) {
        $this->terms = $terms;

        return $this;
    }

    public function getTerms() {
        return $this->terms;
    }
    
    /**
     * Get time slices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTimeslices() {
        return $this->timeslices;
    }   

    /**
     * Set currency
     *
     * @param \FreelancerTools\PaymentBundle\Entity\Currency $currency
     * @return Invoice
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

    /**
     * Add timeslices
     *
     * @param \FreelancerTools\TimeTrackerBundle\Entity\Timeslice $timeslices
     * @return Invoice
     */
    public function addTimeslice(\FreelancerTools\TimeTrackerBundle\Entity\Timeslice $timeslices)
    {
        $this->timeslices[] = $timeslices;

        return $this;
    }

    /**
     * Remove timeslices
     *
     * @param \FreelancerTools\TimeTrackerBundle\Entity\Timeslice $timeslices
     */
    public function removeTimeslice(\FreelancerTools\TimeTrackerBundle\Entity\Timeslice $timeslices)
    {
        $this->timeslices->removeElement($timeslices);
    }    
}
