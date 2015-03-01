<?php

namespace FreelancerTools\CoreBundle\Services;

use Doctrine\ORM\EntityManager;
use FreelancerTools\CoreBundle\Storage\DoctrineStorage;

class FreelancerStorage {

    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $objectManager;
    protected $models = array();

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function __construct(EntityManager $objectManager) {
        $this->objectManager = $objectManager;
    }

    public function getStorage($model) {
        
        if(array_key_exists($model, $this->models)) {
            return $this->models[$model];
        } else {
            return new DoctrineStorage($this->objectManager, $model);
        }      
        
    }

}
