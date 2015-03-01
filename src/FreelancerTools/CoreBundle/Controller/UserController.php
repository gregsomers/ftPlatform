<?php

namespace FreelancerTools\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\CoreBundle\Form\CustomerType;
use FreelancerTools\CoreBundle\Entity\Customer;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * @Route("/users")
 * @PreAuthorize("hasRole('ROLE_SUPER_ADMIN')")
 */
class UserController extends Controller {

    /**
     * @Route("/", name="users")
     * @Template()
     */
    public function indexAction() {
        $users = $this->getUserRepository()->findAll();                     
        return array('users' => $users);
    }

    

    /**
     * get customer repository
     *
     * @return CustomerRepository
     */
    public function getUserRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsCoreBundle:Customer');
    }

}
