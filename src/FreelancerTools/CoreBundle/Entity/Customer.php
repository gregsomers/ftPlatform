<?php

namespace FreelancerTools\CoreBundle\Entity;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use FreelancerTools\TimeTrackerBundle\Entity\Project;

/**
 * @UniqueEntity(fields={"alias", "user"})
 * @ORM\Table(
 *   name="customers",
 *   uniqueConstraints={ @ORM\UniqueConstraint(name="unique_customer_alias_user", columns={"alias", "user_id"}) }
 * )
 * @ORM\Entity(repositoryClass="FreelancerTools\CoreBundle\Entity\CustomerRepository")
 */
class Customer extends Entity {

    /**
     * @var string $name
     *
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @var string $address
     * @ORM\Column(type="text", nullable=true)
     */
    protected $address;

    /**
     * @var string $contact
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $contact;

    /**
     * @var string $phoneNumer
     * @ORM\Column(type="string", length=25, nullable=true)
     */
    protected $phoneNumber;

    /**
     * @var string $emailAddress
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $emailAddress;

    /**
     * @var string $alias
     *      
     * @Gedmo\Slug(fields={"name"})
     * @ORM\Column(type="string", length=30, nullable=true)
     */
    protected $alias;
    
    /**
     * @ORM\OneToMany(targetEntity="\FreelancerTools\TimeTrackerBundle\Entity\Project", mappedBy="customer", cascade={"remove"})
     */
    protected $projects;
    /**
     * Entity constructor
     */
    public function __construct() {
        //$this->tags = new ArrayCollection();
        $this->projects = new ArrayCollection();
    }

    /**
     * Set name
     *
     * @param  string   $name
     * @return Customer
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
     * @param  string   $alias
     * @return Customer
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
     * get customer as string
     *
     * @return string
     */
    public function __toString() {
        $customer = $this->getName();
        if (empty($customer)) {
            $customer = $this->getId();
        }

        return $customer;
    }

    /**
     * Add tag
     *
     * @param  Tag $tag
     * @return Customer
     */
    public function addTag(Tag $tag) {
        $this->tags[] = $tag;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param Tag $tags
     * @return Activity
     */
    public function removeTag(Tag $tag) {
        $this->tags->removeElement($tag);

        return $this;
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags() {
        return $this->tags;
    }

    /**
     * Set tags
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tags
     * @return Customer
     */
    public function setTags(ArrayCollection $tags) {
        $this->tags = $tags;

        return $this;
    }

   
    /**
     * Set address
     *
     * @param string $address
     * @return Customer
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string 
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set contact
     *
     * @param string $contact
     * @return Customer
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set phoneNumer
     *
     * @param string $phoneNumer
     * @return Customer
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumer
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set emailAddress
     *
     * @param string $emailAddress
     * @return Customer
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
    
    
    public function addProject(Project $project) {
        $this->projects[] = $project;

        return $this;
    }

    public function removeProject(Project $project) {
        $this->projects->removeElement($project);

        return $this;
    }

    public function getProjects() {
        return $this->projects;
    }

    public function setProjects(ArrayCollection $projects) {
        $this->projects = $projects;

        return $this;
    }

   
}
