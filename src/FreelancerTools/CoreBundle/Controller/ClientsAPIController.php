<?php

namespace FreelancerTools\CoreBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FreelancerTools\CoreBundle\Controller\FreelancerAPIController;
use FreelancerTools\CoreBundle\Form\CustomerType;
use FreelancerTools\CoreBundle\Entity\Customer;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;


/**
 * @Route("/api/v1/clients")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class ClientsAPIController extends FreelancerAPIController {

    /**
     * @Route("", name="clients_api_get") 
     * @Method("GET")  
     */
    public function getAction(Request $request) {
        $clients = $this->getClientsRepository()
                ->createCurrentQueryBuilder('c')
                ->scopeByField('user', $this->getUser());
        
        $filter = json_decode($this->getRequest()->get('where'), true);
        if($filter) {
            foreach($filter as $key => $val) {               
                $clients->scopeByField($key, $val['=']);
            }
        }
        return $this->createView($clients->getResults(), 200);
    }
    
    /**
     * @Route("", name="clients_api_post") 
     * @Method("POST")     
     */
    public function postSingleAction(Request $request) {
        $client = new Customer();
        
        $formData = json_decode($request->getContent(), true);
        $form = $this->createForm(new CustomerType($this->getDoctrine()->getManager(), $this->getUser()), $client, array('csrf_protection' => false));

        return $this->saveForm($form, $formData);
    }
    
    /**
     * @Route("/{id}", name="clients_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {
        $client = $this->getClientsRepository()
                ->createCurrentQueryBuilder('t')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$client) {
            return $this->createView(array(), 404);
        }

        $formData = json_decode($request->getContent(), true);
        $form = $this->createForm(new CustomerType($this->getDoctrine()->getManager(), $this->getUser()), $client, array('csrf_protection' => false));

        return $this->saveForm($form, $formData);
    }
    
    /**
     * @Route("/{id}", name="clients_api_delete") 
     * @Method("DELETE")     
     */
    public function deleteAction($id) {
        $data = $this->getClientsRepository()
                ->createCurrentQueryBuilder('i')
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
