<?php

namespace FreelancerTools\TimeTrackerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use FreelancerTools\CoreBundle\Entity\Entity;

/** 
 * @ORM\Table(name="activities")
 * @ORM\Entity(repositoryClass="FreelancerTools\TimeTrackerBundle\Entity\ActivityRepository")
 */
class Activity extends Entity {

    /**
     * @var Customer $customer
     *
     * @ORM\ManyToOne(targetEntity="FreelancerTools\CoreBundle\Entity\Customer")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $customer;

    /**
     * @var Project $project
     *
     * @ORM\ManyToOne(targetEntity="Project", inversedBy="activities")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $project;

    /**
     * @var Service $service
     *
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $service;

    /**
     * @var ArrayCollection $timeslices
     *
     * @JMS\Type("array")
     * @JMS\SerializedName("timeslices")
     * @ORM\OneToMany(targetEntity="Timeslice", mappedBy="activity", cascade="persist")
     * @ORM\OrderBy({"startedAt" = "DESC"})
     */
    protected $timeslices;

    /**
     * @var string $description
     *
     * @ORM\Column(type="text", nullable=false)
     */
    protected $description;

    /**
     * @var float $rate
     *
     * @ORM\Column(type="decimal", scale=2, precision=10, nullable=true)
     */
    protected $rate;

    /**
     * @var string $rateReference (considered as enum: customer|project|service)
     *
     * @JMS\SerializedName("rateReference")
     * @ORM\Column(name="rate_reference", type="string", length=255, nullable=true)
     */
    protected $rateReference;

    /**
     * @ORM\Column(name="archived", type="boolean", options={"default" = 0})
     */
    protected $archived = false;

    /**
     * Entity constructor
     */
    public function __construct() {
        $this->timeslices = new ArrayCollection();
    }

    public function __toString() {
        return $this->customer->getName() . ": " . $this->description;
    }

    /**
     * Set description
     *
     * @param  string   $description
     * @return Activity
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
     * Set rate
     *
     * @param  float    $rate
     * @return Activity
     */
    public function setRate($rate) {
        $this->rate = $rate;

        return $this;
    }

    /**
     * Get rate
     *
     * @return float
     */
    public function getRate() {
        if ($this->rate) {
            return $this->rate;
        } elseif ($this->project && $this->project->getRate()) {
            return $this->project->getRate();
        }
    }

    /**
     * Set rateReference
     *
     * @param  string   $rateReference
     * @return Activity
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
     * Set customer
     *
     * @param  Customer $customer
     * @return Activity
     */
    public function setCustomer($customer) {
        $this->customer = $customer;

        return $this;
    }

    /**
     * Get customer
     *
     * @return Customer
     */
    public function getCustomer() {
        return $this->customer;
    }

    /**
     * Set project
     *
     * @param  Project  $project
     * @return Activity
     */
    public function setProject($project) {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return Project
     */
    public function getProject() {
        return $this->project;
    }

    /**
     * Set service
     *
     * @param  Service  $service
     * @return Activity
     */
    public function setService($service) {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return Service
     */
    public function getService() {
        return $this->service;
    }

    /**
     * Add time slice
     *
     * @param  Timeslice $timeslice
     * @return Activity
     */
    public function addTimeslice(Timeslice $timeslice) {
        $this->timeslices[] = $timeslice;

        return $this;
    }

    /**
     * Get time slices
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTimeslices() {
        return $this->timeslices;
    }

    public function setArchived($archived) {
        $this->archived = $archived;

        return $this;
    }

    public function getArchived() {
        return $this->archived;
    }

    public function getTotalTime() {
        $total = 0;
        foreach ($this->timeslices as $slice) {
            $total += $slice->getCurrentDuration();
        }
        $hours = floor($total / 3600);
        $mins = floor(($total - ($hours * 3600)) / 60);
        $secs = floor($total % 60);
        return sprintf("%02d", $hours) . ":" . sprintf("%02d", $mins) . ":" . sprintf("%02d", $secs);
    }

    public function getBalanceTime() {
        $total = 0;
        foreach ($this->timeslices as $slice) {
            if (!$slice->getInvoiced()) {
                $total += $slice->getCurrentDuration();
            }
        }
        $hours = floor($total / 3600);
        $mins = floor(($total - ($hours * 3600)) / 60);
        $secs = floor($total % 60);
        return sprintf("%02d", $hours) . ":" . sprintf("%02d", $mins) . ":" . sprintf("%02d", $secs);
    }

    public function getInvoicedTime() {
        $total = $this->getInvoicedSeconds();
        $hours = floor($total / 3600);
        $mins = floor(($total - ($hours * 3600)) / 60);
        $secs = floor($total % 60);
        return sprintf("%02d", $hours) . ":" . sprintf("%02d", $mins) . ":" . sprintf("%02d", $secs);
    }

    public function getTotalSeconds() {
        $total = 0;
        foreach ($this->timeslices as $slice) {
            $total += $slice->getCurrentDuration();
        }
        return $total;
    }

    public function getInvoicedSeconds() {
        $total = 0;
        foreach ($this->timeslices as $slice) {
            if ($slice->getInvoiced()) {
                $total += $slice->getCurrentDuration();
            }
        }
        return $total;
    }

    public function getBalanceSeconds() {
        $total = 0;
        if (!$this->archived) {
            foreach ($this->timeslices as $slice) {
                if (!$slice->getInvoiced()) {
                    $total += $slice->getCurrentDuration();
                }
            }
        }
        return $total;
    }

    public function getUnbilledSlices() {
        $slices = array();
        foreach ($this->timeslices as $slice) {
            if (!$slice->getInvoiced()) {
                $slices[] = $slice;
            }
        }
        return $slices;
    }

    public function setBalanceSlicesInvoices($invoice, $invoiceItem = null) {
        foreach ($this->timeslices as $slice) {
            if (!$slice->getInvoiced()) {
                $slice->setInvoiced(true);
                $slice->setInvoice($invoice);
                $slice->setInvoiceItem($invoiceItem);
                $slice->setInvoicedAt(new \DateTime('now'));
            }
        }
    }

}
