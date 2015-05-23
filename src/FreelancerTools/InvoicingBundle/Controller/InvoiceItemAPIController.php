<?php

namespace FreelancerTools\InvoicingBundle\Controller;

use FreelancerTools\CoreBundle\Controller\FreelancerAPIController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\InvoicingBundle\Form\InvoiceItemType;
use FreelancerTools\InvoicingBundle\Entity\InvoiceItem;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;

/**
 * @Route("/api/v1/invoiceitems")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class InvoiceItemAPIController extends FreelancerAPIController {
    
    /**
     * @Route("", name="invoiceitems_api_post") 
     * @Method("POST")
     */
    public function createAction(Request $request) {

        $formData = json_decode($request->getContent(), true); 

        $invoice = $this->getInvoiceRepository() 
                ->createCurrentQueryBuilder('i')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $formData['invoice_id'])                
                ->getSingleResult();

        if (!$invoice) {
            return $this->createView(array(), 404);
        }
        
        $invoiceItem = new InvoiceItem();
        $invoiceItem->setInvoice($invoice);
        
        $form = $this->createForm(new InvoiceItemType(), $invoiceItem, array('csrf_protection' => false));       
        return $this->saveForm($form, $formData);
    }
    
    /**
     * @Route("/{id}", name="invoiceitems_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {
        $item = $this->getInvoiceItemRepository()
                ->createCurrentQueryBuilder('t')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$item) {
            return $this->createView(array(), 404);
        }

        $formData = json_decode($request->getContent(), true);
        $form = $this->createForm(new InvoiceItemType(), $item, array('csrf_protection' => false));        
        return $this->saveForm($form, $formData);
    }
    
    /**
     * @Route("/{id}", name="invoiceitems_api_delete") 
     * @Method("DELETE")     
     */
    public function deleteAction($id) {
        $data = $this->getInvoiceItemRepository()
                ->createCurrentQueryBuilder('ii')
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
