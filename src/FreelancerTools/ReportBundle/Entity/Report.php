<?php

namespace FreelancerTools\ReportBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use FreelancerTools\CoreBundle\Entity\Entity;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;


/** 
 * @ORM\Entity()
 * @ORM\Table(name="reports")
 * 
 */
class Report extends Entity {

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
     * //@ORM\ManyToOne(targetEntity="FreelancerTools\TimeTrackerBundle\Entity\Project")
     * //@ORM\JoinColumn(name="project_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     *  @ORM\ManyToMany(targetEntity="FreelancerTools\TimeTrackerBundle\Entity\Project")
     *  @ORM\JoinTable(name="report_project")
     */
    protected $projects;

    /**
     * @var Service $service
     *
     * @ORM\ManyToOne(targetEntity="FreelancerTools\TimeTrackerBundle\Entity\Service")
     * @ORM\JoinColumn(name="service_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $service;
    
    /**     
     * @Assert\DateTime()
     * @ORM\Column(name="start", type="datetime", nullable=true)
     */
    protected $start;

    /**     
     * @Assert\DateTime()
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    protected $end;
    
    /**     
     * @ORM\Column(name="invoiced", type="boolean", options={"default" = 0})
     */
    protected $invoiced = false;
    
    /**    
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $token;

    /**
     * Entity constructor
     */
    public function __construct() {        
        $this->token = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 50);
        $this->projects = new ArrayCollection();
    }

    public function __toString() {
        return $this->customer->getName() . ": " . $this->description;
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
    
    
    public function setStart($start)
    {
        if (!$start instanceof DateTime && !empty($start)) {
            $start = new DateTime($start);
        }
        $this->start = $start;

        return $this;
    }

    /**
     * Get started_at
     *
     * @return DateTime
     */
    public function getStart()
    {
        return $this->start;
    }
    
   
    public function setEnd($stoppedAt)
    {
        if (!$stoppedAt instanceof DateTime && !empty($stoppedAt)) {
            $stoppedAt = new DateTime($stoppedAt);
        }
        $this->end = $stoppedAt;

        return $this;
    }

    
    public function getEnd()
    {
        
        return $this->end;
    }

    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }   
      
    public function getToken()
    {
        return $this->token;
    }
   
    /**
     * Add projects
     *
     * @param \FreelancerTools\TimeTrackerBundle\Entity\Project $projects
     * @return Report
     */
    public function addProject(\FreelancerTools\TimeTrackerBundle\Entity\Project $projects)
    {
        $this->projects[] = $projects;

        return $this;
    }

    /**
     * Remove projects
     *
     * @param \FreelancerTools\TimeTrackerBundle\Entity\Project $projects
     */
    public function removeProject(\FreelancerTools\TimeTrackerBundle\Entity\Project $projects)
    {
        $this->projects->removeElement($projects);
    }

    /**
     * Get projects
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProjects()
    {
        return $this->projects;
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
   
}
