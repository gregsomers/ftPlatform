<?php

namespace FreelancerTools\InvoicingBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use FreelancerTools\CoreBundle\Entity\Entity;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Table(name="invoice_item")
 * @ORM\Entity(repositoryClass="FreelancerTools\InvoicingBundle\Entity\InvoiceItemRepository")
 * @JMS\ExclusionPolicy("all")
 */
class InvoiceItem extends Entity {

    /**
     * 
     * @ORM\ManyToOne(targetEntity="Invoice", inversedBy="items", cascade="persist")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $invoice;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose()
     */
    protected $product;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose()
     */
    protected $description;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=10, nullable=true)
     * @JMS\Expose()
     */
    protected $quantity;

    /**
     * @ORM\Column(type="decimal", scale=2, precision=10, nullable=true)
     * @JMS\Expose()
     */
    protected $price;

    /**
     * @var ArrayCollection $timeslices    
     * @ORM\OneToMany(targetEntity="FreelancerTools\TimeTrackerBundle\Entity\Timeslice", mappedBy="invoiceItem")
     * @ORM\OrderBy({"startedAt" = "DESC"})
     * @JMS\Expose()
     */
    protected $timeslices;

    /**
     * Entity constructor
     */
    public function __construct() {
        $this->timeslices = new ArrayCollection();
    }

    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("invoice_id")
     */
    public function getInvoiceId() {
        return $this->getInvoice()->getId();
    }

    public function getTotal() {
        return $this->price * $this->quantity;
    }

    /**
     * Set product
     *
     * @param string $product
     * @return InvoiceItem
     */
    public function setProduct($product) {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return string 
     */
    public function getProduct() {
        return $this->product;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     * @return InvoiceItem
     */
    public function setQuantity($quantity) {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return integer 
     */
    public function getQuantity() {
        return $this->quantity;
    }

    /**
     * Set price
     *
     * @param string $price
     * @return InvoiceItem
     */
    public function setPrice($price) {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return string 
     */
    public function getPrice() {
        return $this->price;
    }

    /**
     * Set invoice
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\Invoice $invoice
     * @return InvoiceItem
     */
    public function setInvoice(\FreelancerTools\InvoicingBundle\Entity\Invoice $invoice) {
        $this->invoice = $invoice;

        return $this;
    }

    /**
     * Get invoice
     *
     * @return \FreelancerTools\InvoicingBundle\Entity\Invoice 
     */
    public function getInvoice() {
        return $this->invoice;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return InvoiceItem
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }

    /**
     * Get time slices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTimeslices() {
        return $this->timeslices;
    }

}
