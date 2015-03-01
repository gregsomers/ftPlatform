<?php

namespace FreelancerTools\TimeTrackerBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use FreelancerTools\CoreBundle\Entity\Entity;
use FreelancerTools\CoreBundle\Entity\Customer;

/** 
 * @UniqueEntity(fields={"alias", "user"})
 * @ORM\Table(
 *   name="projects",
 *   uniqueConstraints={ @ORM\UniqueConstraint(name="unique_project_alias_user", columns={"alias", "user_id"}) }
 * )
 * @ORM\Entity(repositoryClass="FreelancerTools\TimeTrackerBundle\Entity\ProjectRepository")
 */
class Project extends Entity {

    /**
     * @var Customer $customer
     *
     * @ORM\ManyToOne(targetEntity="\FreelancerTools\CoreBundle\Entity\Customer", inversedBy="projects")
     * @ORM\JoinColumn(name="customer_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $customer;

    /**
     * @var string $name
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var string $alias
     *
     * @Assert\NotBlank()
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $alias;

    /**
     * @var DateTime $startedAt
     *
     * @Assert\Date()
     * @JMS\SerializedName("startedAt")
     * @ORM\Column(name="started_at", type="datetime", nullable=true)
     */
    protected $startedAt;

    /**
     * @var DateTime $stoppedAt
     *
     * @Assert\Date()
     * @JMS\SerializedName("stoppedAt")
     * @ORM\Column(name="stopped_at", type="datetime", nullable=true)
     */
    protected $stoppedAt;

    /**
     * @var DateTime $deadline
     *
     * @Assert\Date()
     * @ORM\Column(name="deadline", type="datetime", nullable=true)
     */
    protected $deadline;

    /**
     * @var string $description
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    /**
     * @var integer $budgetPrice
     *
     * @JMS\SerializedName("budgetPrice")
     * @ORM\Column(name="budget_price", type="integer", nullable=true)
     */
    protected $budgetPrice;

    /**
     * @var integer $fixedPrice
     *
     * @JMS\SerializedName("fixedPrice")
     * @ORM\Column(name="fixed_price", type="integer", length=255, nullable=true)
     */
    protected $fixedPrice;

    /**
     * @var integer $budgetTime
     *
     * @JMS\SerializedName("budgetTime")
     * @ORM\Column(name="budget_time", type="integer", length=255, nullable=true)
     */
    protected $budgetTime;

    /**
     * @var float $rate
     *
     * @ORM\Column(type="decimal", scale=2, precision=10, nullable=true)
     */
    protected $rate;

    /**
     * @ORM\Column(name="active", type="boolean")
     */
    protected $active = true;

    /**
     * @ORM\OneToMany(targetEntity="Activity", mappedBy="project")
     * @ORM\OrderBy({"updatedAt" = "DESC"})
     * */
    private $activities;

    /**
     * Entity constructor
     */
    public function __construct() {
        $this->activities = new ArrayCollection();
    }

    public function getUnbilledTime($seconds = false) {
        $total = 0;
        foreach ($this->activities as $activity) {
            $total += $activity->getBalanceSeconds();
        }
        $hours = floor($total / 3600);
        $mins = floor(($total - ($hours * 3600)) / 60);
        $secs = floor($total % 60);
        if ($seconds) {
            return $total;
        } else {
            return sprintf("%02d", $hours) . ":" . sprintf("%02d", $mins) . ":" . sprintf("%02d", $secs);
        }
    }

    public function getUnbilledCost() {
        $total = 0;
        foreach ($this->activities as $activity) {
            $total += $activity->getBalanceSeconds() * $activity->getRate() / 3600;
        }
        return $total;
    }

    public function getBilledTime() {
        $total = 0;
        foreach ($this->activities as $activity) {
            $total += $activity->getInvoicedSeconds();
        }
        $hours = floor($total / 3600);
        $mins = floor(($total - ($hours * 3600)) / 60);
        $secs = floor($total % 60);

        return sprintf("%02d", $hours) . ":" . sprintf("%02d", $mins) . ":" . sprintf("%02d", $secs);
    }

    public function getTotalTime() {
        $total = 0;
        foreach ($this->activities as $activity) {
            $total += $activity->getTotalSeconds();
        }
        $hours = floor($total / 3600);
        $mins = floor(($total - ($hours * 3600)) / 60);
        $secs = floor($total % 60);

        return sprintf("%02d", $hours) . ":" . sprintf("%02d", $mins) . ":" . sprintf("%02d", $secs);
    }

    /**
     * Set customer
     *
     * @param  Customer $customer
     * @return Project
     */
    public function setCustomer(Customer $customer) {
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
     * Set name
     *
     * @param  string  $name
     * @return Project
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set alias
     *
     * @param  string  $alias
     * @return Project
     */
    public function setAlias($alias) {
        $this->alias = $alias;

        return $this;
    }

    /**
     * Get alias
     *
     * @return string
     */
    public function getAlias() {
        return $this->alias;
    }

    /**
     * Set startedAt
     *
     * @param  DateTime $startedAt
     * @return Project
     */
    public function setStartedAt($startedAt) {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * Get startedAt
     *
     * @return datetime
     */
    public function getStartedAt() {
        return $this->startedAt;
    }

    /**
     * Set stoppedAt
     *
     * @param  datetime $stoppedAt
     * @return Project
     */
    public function setStoppedAt($stoppedAt) {
        $this->stoppedAt = $stoppedAt;

        return $this;
    }

    /**
     * Get stoppedAt
     *
     * @return datetime
     */
    public function getStoppedAt() {
        return $this->stoppedAt;
    }

    /**
     * Set deadline
     *
     * @param  datetime $deadline
     * @return Project
     */
    public function setDeadline($deadline) {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return datetime
     */
    public function getDeadline() {
        return $this->deadline;
    }

    /**
     * Set description
     *
     * @param  string  $description
     * @return Project
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
     * Set budgetPrice
     *
     * @param  integer $budgetPrice
     * @return Project
     */
    public function setBudgetPrice($budgetPrice) {
        $this->budgetPrice = $budgetPrice;

        return $this;
    }

    /**
     * Get budgetPrice
     *
     * @return int
     */
    public function getBudgetPrice() {
        return $this->budgetPrice;
    }

    /**
     * Set fixedPrice
     *
     * @param  integer $fixedPrice
     * @return Project
     */
    public function setFixedPrice($fixedPrice) {
        $this->fixedPrice = $fixedPrice;

        return $this;
    }

    /**
     * Get fixedPrice
     *
     * @return int
     */
    public function getFixedPrice() {
        return $this->fixedPrice;
    }

    /**
     * Set budgetTime
     *
     * @param  integer $budgetTime
     * @return Project
     */
    public function setBudgetTime($budgetTime) {
        $this->budgetTime = $budgetTime;

        return $this;
    }

    /**
     * Get budgetTime
     *
     * @return int
     */
    public function getBudgetTime() {
        return $this->budgetTime;
    }

    /**
     * Set rate
     *
     * @param  float   $rate
     * @return Project
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
        return $this->rate;
    }

    public function setActive($active) {
        $this->active = $active;

        return $this;
    }

    public function getActive() {
        return $this->active;
    }

    /**
     * get project as string
     *
     * @return string
     */
    public function __toString() {
        return (empty($this->name)) ? $this->getId() : $this->getName();
    }

    /**
     * Add activities
     *
     * @param \FreelancerTools\TimeTrackerBundle\Entity\Activity $activities
     * @return Project
     */
    public function addActivity(\FreelancerTools\TimeTrackerBundle\Entity\Activity $activities) {
        $this->activities[] = $activities;

        return $this;
    }

    /**
     * Remove activities
     *
     * @param \FreelancerTools\TimeTrackerBundle\Entity\Activity $activities
     */
    public function removeActivity(\FreelancerTools\TimeTrackerBundle\Entity\Activity $activities) {
        $this->activities->removeElement($activities);
    }

    /**
     * Get activities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getActivities() {
        return $this->activities;
    }

}
