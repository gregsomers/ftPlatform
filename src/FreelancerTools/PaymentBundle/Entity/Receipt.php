<?php

namespace FreelancerTools\PaymentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FreelancerTools\CoreBundle\Entity\Entity;
use \DateTime;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="receipt")
 * @ORM\Entity(repositoryClass="FreelancerTools\PaymentBundle\Entity\ReceiptRepository")
 */
class Receipt extends Entity {

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose()
     */
    protected $merchant;

    /**
     * 
     * @ORM\Column(name="date", type="date")
     * @JMS\Expose()
     */
    protected $date;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @JMS\Expose()
     */
    protected $total;

    /**
     * 
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose()
     */
    protected $notes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @JMS\Expose()
     */
    public $path;
    private $file;

    /**
     * Entity constructor
     */
    public function __construct() {
        $this->date = new DateTime("now");
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null) {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @return UploadedFile
     */
    public function getFile() {
        return $this->file;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUpload() {
        if (null !== $this->getFile()) {
            $filename = sha1(uniqid(mt_rand(), true));
            $this->path = $filename.'.'.$this->getFile()->getClientOriginalExtension();
            //$this->path = $this->getFile()->getClientOriginalName();
        }
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     */
    public function upload() {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // use the original file name here but you should
        // sanitize it at least to avoid any security issues
        // move takes the target directory and then the
        // target filename to move to
        $this->getFile()->move(
                $this->getUploadRootDir(), $this->path//$this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        //$this->path = $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->file = null;
    }

    /**
     * @ORM\PreRemove()
     */
    public function storeFilenameForRemove() {
        if (isset($this->path)) {
            @unlink($this->getUploadRootDir() . $this->path);
        }        
    }
    
    public function getUploadRootDir()
    {        
        return __DIR__.'/../../../../../../../web/files/';
    }

    /**
     * Set merchant
     *
     * @param string $merchant
     * @return Receipt
     */
    public function setMerchant($merchant) {
        $this->merchant = $merchant;

        return $this;
    }

    /**
     * Get merchant
     *
     * @return string 
     */
    public function getMerchant() {
        return $this->merchant;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Receipt
     */
    public function setDate($date) {
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
    public function getDate() {
        return $this->date;
    }

    /**
     * Set total
     *
     * @param string $total
     * @return Receipt
     */
    public function setTotal($total) {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return string 
     */
    public function getTotal() {
        return $this->total;
    }

    /**
     * Set notes
     *
     * @param string $notes
     * @return Receipt
     */
    public function setNotes($notes) {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string 
     */
    public function getNotes() {
        return $this->notes;
    }

}
