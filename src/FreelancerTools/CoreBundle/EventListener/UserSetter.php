<?php

namespace FreelancerTools\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

class UserSetter {

    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function prePersist(LifecycleEventArgs $args) {
        $entity = $args->getEntity();

        if (method_exists($entity, 'setUser')) {
            if(!$entity->getUser()) {
                $entity->setUser($this->getUser());
            }                
        }        
    }

    private function getUser() {
        return $this->container->get('security.context')->getToken()->getUser();
    }

}
