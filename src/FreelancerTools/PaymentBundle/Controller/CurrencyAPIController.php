<?php

namespace FreelancerTools\PaymentBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\CoreBundle\Controller\FreelancerAPIController as Base;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use FreelancerTools\PaymentBundle\Entity\Currency;
use FreelancerTools\PaymentBundle\Form\CurrencyType;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;


/**
 * Currency controller.
 *
 * @Route("/api/v1/currencies")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class CurrencyAPIController extends Base
{
    
    /**
     * @Route("", name="currencies_api_get") 
     * @Method("GET")        
     */
    public function indexAction() {
        $templates = $this->getCurrencyRepository()
                ->createCurrentQueryBuilder('c')
                ->scopeByField('user', $this->getUser())
        ;

        return $this->createView($templates->getResults(), 200);
    }
    
    /**
     * @Route("", name="currencies_api_payment") 
     * @Method("POST")     
     */
    public function postAction(Request $request) {
        $entity = new Currency();

        $formData = json_decode($request->getContent(), true);        

        $form = $this->createForm(new CurrencyType(), $entity, array('csrf_protection' => false));
        return $this->saveForm($form, $formData);
    }
    
    /**
     * @Route("/{id}", name="currancies_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {       

        $entity = $this->getCurrencyRepository()
                ->createCurrentQueryBuilder('s')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$entity) {
            return $this->createView(array(), 404);
        }

        $formData = json_decode($request->getContent(), true);
        $form = $this->createForm(new CurrencyType(), $entity, array('csrf_protection' => false));
        return $this->saveForm($form, $formData);
    }
    
    /**
     * @Route("/{id}", name="currencies_api_delete") 
     * @Method("DELETE")     
     */
    public function deleteAction($id) {
        $data = $this->getCurrencyRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$data) {
            return $this->createView(array(), 404);
        }

        $this->deleteEntity($data);
        return $this->createView(array(), 200);
    }

    
}
