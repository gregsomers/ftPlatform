<?php

namespace FreelancerTools\TimeTrackerBundle\Entity;

use DateTime;
use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use FreelancerTools\CoreBundle\Entity\Entity;

/**
 * 
 * @ORM\Table(name="timeslices")
 * @ORM\Entity(repositoryClass="FreelancerTools\TimeTrackerBundle\Entity\TimesliceRepository")
 * @ORM\HasLifecycleCallbacks()
 * @JMS\ExclusionPolicy("all")
 */
class Timeslice extends Entity
{
    /**
     * @var Activity $activity
     *
     * @Assert\NotNull()
     * @ORM\ManyToOne(targetEntity="Activity", inversedBy="timeslices", cascade="persist")
     * @ORM\JoinColumn(name="activity_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * //@JMS\Expose()
     */
    protected $activity;


    /**
     * @var integer $duration (in seconds)
     *
     * @ORM\Column(type="integer", nullable=false)
     * @JMS\Expose()
     */
    protected $duration = 0;

    /**
     * @var datetime $startedAt
     *
     * @Assert\DateTime()
     * @JMS\SerializedName("startedAt")
     * @ORM\Column(name="started_at", type="datetime", nullable=true)
     * @JMS\Expose()
     * //@JMS\Type("DateTime<'m/d/Y h:m:s a'>")
     */
    protected $startedAt;

    /**
     * @var datetime $stoppedAt
     *
     * @Assert\DateTime()
     * @JMS\SerializedName("stoppedAt")
     * @ORM\Column(name="stopped_at", type="datetime", nullable=true)
     * @JMS\Expose()
     * //@JMS\Type("DateTime<'m/d/Y h:m:s a'>")
     */
    protected $stoppedAt;
    
    /**    
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose()
     */
    protected $notes;
    
    /**    
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose()
     */
    protected $status;
    
    /**     
     * @ORM\Column(name="invoiced", type="boolean", options={"default" = 0})
     * @JMS\Expose()
     */
    protected $invoiced = false;
    
    /**     
     * 
     * @ORM\ManyToOne(targetEntity="FreelancerTools\InvoicingBundle\Entity\Invoice", inversedBy="timeslices", cascade="persist")
     * @ORM\JoinColumn(name="invoice_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $invoice;
    
    /**     
     * 
     * @ORM\ManyToOne(targetEntity="FreelancerTools\InvoicingBundle\Entity\InvoiceItem", inversedBy="timeslices", cascade="persist")
     * @ORM\JoinColumn(name="invoiceItem_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $invoiceItem;
    
    /**     
     * @Assert\DateTime()     
     * @ORM\Column(name="invoiced_at", type="datetime", nullable=true)
     */
    protected $invoicedAt;


    /**
     * Entity constructor
     */
    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }
    
    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("activity_id")
     * 
     */
    public function getActivityId() {   
        return $this->getActivity()->getId();
    }
    
    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("invoiceItem_id")
     * 
     */
    public function getInvoiceItemId() { 
        if($this->getInvoiceItem()) {
            return $this->getInvoiceItem()->getId();
        }
    }
    
    /**
     * @JMS\VirtualProperty
     * @JMS\SerializedName("project_id")
     * 
     */
    public function getProjectId() {  
        if($this->getActivity()->getProject()) {
            return $this->getActivity()->getProject()->getId();
        }
    }
    
    

    /**
     * Set activity
     *
     * @param  Activity  $activity
     * @return Timeslice
     */
    public function setActivity(Activity $activity)
    {
        $this->activity = $activity;

        return $this;
    }

    /**
     * Get activity
     *
     * @return Activity
     */
    public function getActivity()
    {
        return $this->activity;
    }

    /**
     * Set duration
     *
     * @param  integer   $duration
     * @return Timeslice
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration in seconds
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set started_at
     *
     * @param  DateTime  $startedAt
     * @return Timeslice
     */
    public function setStartedAt($startedAt)
    {
        if (!$startedAt instanceof DateTime && !empty($startedAt)) {
            $startedAt = new DateTime($startedAt);
        }
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get started_at
     *
     * @return DateTime
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Set stopped_at
     *
     * @param  DateTime  $stoppedAt
     * @return Timeslice
     */
    public function setStoppedAt($stoppedAt)
    {
        if (!$stoppedAt instanceof DateTime && !empty($stoppedAt)) {
            $stoppedAt = new DateTime($stoppedAt);
        }
        $this->stoppedAt = $stoppedAt;

        return $this;
    }

    /**
     * Get stopped_at
     *
     * @return DateTime
     */
    public function getStoppedAt()
    {
        
        return $this->stoppedAt;
    }
    
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }   
      
    public function getNotes()
    {
        return $this->notes;
    }
    
    public function getStatus()
    {
        return $this->status;
    }
    
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }
    
    public function setInvoiced($invoiced)
    {
        $this->invoiced = $invoiced;

        return $this;
    }
    
    public function getInvoiced()
    {
        return $this->invoiced;
    }
    
    
    public function setInvoicedAt($invoicedAt)
    {
        if (!$invoicedAt instanceof DateTime && !empty($invoicedAt)) {
            $invoicedAt = new DateTime($invoicedAt);
        }
        $this->invoicedAt = $invoicedAt;

        return $this;
    }
   
    public function getInvoicedAt()
    {
        
        return $this->invoicedAt;
    }

    /**
     * Auto generate duration if empty
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * @return Timeslice
     */
    public function updateDuration()
    {
        if (!empty($this->startedAt) && !empty($this->stoppedAt)) {
            $this->duration = abs($this->stoppedAt->getTimestamp() - $this->startedAt->getTimestamp());
        }

        return $this;
    }

    /**
     * Get duration in seconds from start to now
     *
     * @return int
     */
    public function getCurrentDuration()
    {
        if ($this->getDuration()) {
            return $this->getDuration();
        }

        if ($this->getStartedAt() instanceof DateTime) {
            if ($this->getStoppedAt() instanceof DateTime) {
                $end = $this->getStoppedAt();
            } else {
                $end = new DateTime('now');
            }

            $duration = $this->getStartedAt()->diff($end);

            return $duration->format('%a') * 24 * 60 * 60
                + $duration->format('%h') * 60 * 60
                + $duration->format('%i') * 60
                + $duration->format('%s');
        }
    }
    
    public function getCurrentDurationString() {
        
        $total = $this->getCurrentDuration();
        
        $hours = floor($total / 3600);
        $mins = floor(($total - ($hours * 3600)) / 60);
        $secs = floor($total % 60);
        return sprintf("%02d", $hours).":".sprintf("%02d", $mins).":".sprintf("%02d", $secs);
    }    

    /**
     * Set invoice
     *
     * @param \FreelancerTools\InvoicingBundle\Entity\Invoice $invoice
     * @return Timeslice
     */
    public function setInvoice(\FreelancerTools\InvoicingBundle\Entity\Invoice $invoice = null)
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
    
    public function setInvoiceItem(\FreelancerTools\InvoicingBundle\Entity\InvoiceItem $invoiceItem = null)
    {
        $this->invoiceItem = $invoiceItem;

        return $this;
    }
    
    public function getInvoiceItem()
    {
        return $this->invoiceItem;
    }
}
