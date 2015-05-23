<?php

namespace FreelancerTools\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\CoreBundle\Controller\FreelancerAPIController as Base;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FreelancerTools\PaymentBundle\Entity\PaymentMethod;
use FreelancerTools\PaymentBundle\Form\PaymentMethodType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;


/**
 * PaymentMethod controller.
 *
 * @Route("/api/v1/paymentmethods")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class PaymentMethodAPIController extends Base
{

    /**
     * Lists all PaymentMethod entities.
     *
     * @Route("", name="paymentmethod_api_get")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $paymentMethods = $this->getPaymentMethodRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
        ;
        

        return $this->createView($paymentMethods->getResults(), 200);
        
    }
    
    /**
     * @Route("", name="paymentmethod_api_post") 
     * @Method("POST")     
     */
    public function postAction(Request $request) {
        $entity = new PaymentMethod();

        $formData = json_decode($request->getContent(), true);        

        $form = $this->createForm(new PaymentMethodType(), $entity, array('csrf_protection' => false));
        return $this->saveForm($form, $formData);
    }
    
    /**
     * @Route("/{id}", name="paymentmethod_api_delete") 
     * @Method("DELETE")     
     */
    public function deleteAction($id) {
        $data = $this->getPaymentMethodRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$data) {
            return $this->createView(array(), 404);
        }
        
        $payments = $this->getPaymentRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('method', $data)
                ->getResults();
        
        if(count($payments) > 0) {
            return $this->createView(array('errors' => "Cannot delete a method which existing payments were made with."), 400);
        }
        
        $this->deleteEntity($data);       
        return $this->createView(array(), 200);
    }

    
}
