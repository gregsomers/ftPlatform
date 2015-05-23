<?php

namespace FreelancerTools\InvoicingBundle\Controller;

use FreelancerTools\CoreBundle\Controller\FreelancerAPIController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\InvoicingBundle\Form\InvoiceType;
use FreelancerTools\InvoicingBundle\Entity\Invoice;
use FreelancerTools\PaymentBundle\Entity\Payment;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;


/**
 * @Route("/api/v1/invoices")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class InvoiceAPIController extends FreelancerAPIController {

    /**
     * @Route("", name="invoice_api_get")  
     * @Method("GET")  
     */
    public function getAction(Request $request) {
        $slices = $this->getInvoiceRepository()
                ->createCurrentQueryBuilder('i')
                ->addLeftJoins(array('c' => 'i.customer', 
                    'p' => 'payments', 'pm' => 'p.method', 'oo' => 'p.order',                    
                    'ii' => 'items', 's' => 'ii.timeslices', 'a' => 's.activity'))
                ->scopeByField('user', $this->getUser())
        ;

        $filter = json_decode($this->getRequest()->get('where'), true);
        if ($filter) {
            foreach ($filter as $key => $val) {
                $slices->scopeByField($key, $val['=']);
            }
        }

        return $this->createView($slices->getResults(), 200);
    }

    /**
     * @Route("/{id}", name="invoice_api_single_get") 
     * @Method("GET")     
     */
    public function getSingleAction($id) {
        $data = $this->getInvoiceRepository()
                ->createCurrentQueryBuilder('i')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->addLeftJoins(array('c' => 'i.customer', 
                    'p' => 'payments', 'pm' => 'p.method', 'oo' => 'p.order',                    
                    'ii' => 'items', 's' => 'ii.timeslices', 'a' => 's.activity'))
                ->getSingleResult();

        return $this->createView($data, 200);
    }

    /**
     * @Route("", name="invoice_api_post") 
     * @Method("POST")     
     */
    public function postSingleAction(Request $request) {
        $invoice = new Invoice();

        if (!$invoice) {
            return $this->createView(array(), 404);
        }

        $formData = json_decode($request->getContent(), true);
        unset($formData['items']);
        $formData['currency'] = $formData['currency_id'];
        $formData['customer'] = $formData['client']['id'];

        $form = $this->createForm(new InvoiceType(), $invoice, array('csrf_protection' => false));

        return $this->saveForm($form, $formData);
    }

    /**
     * @Route("/{id}", name="invoice_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {
        $invoice = $this->getInvoiceRepository()
                ->createCurrentQueryBuilder('t')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$invoice) {
            return $this->createView(array(), 404);
        }

        $formData = json_decode($request->getContent(), true);
        unset($formData['items']);
        $formData['currency'] = $formData['currency_id'];
        $formData['customer'] = $formData['client']['id'];

        $form = $this->createForm(new InvoiceType(), $invoice, array('csrf_protection' => false));

        return $this->saveForm($form, $formData);
    }

    /**
     * @Route("/{id}", name="invoice_api_delete") 
     * @Method("DELETE")     
     */
    public function deleteAction($id) {
        $data = $this->getInvoiceRepository()
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

    /**
     * @Route("/emailCustom", name="invoice_api_email_custom") 
     * @Method("POST")     
     */
    public function emailCustomAction(Request $request) {
        //send a custom email
        $data = json_decode($request->getContent(), true);
        $data['from'] = array($data['email'] => $data['name']);

        $invoice = $this->getInvoiceRepository()
                ->createCurrentQueryBuilder('i')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $data['invoice_id'])
                ->getSingleResult();

        if (!$invoice) {
            return $this->createView(array(), 404);
        }
        $this->get('ft.pdf')->generateInvoicePDF($invoice, $this->getUser(), 'F');

        $object = array('invoice' => $invoice, 'user' => $this->getUser(), 'payment' => new Payment());
        $notification = $this->get('ft.email.notification');
        $notification->send($data, $object, null, '/tmp/Invoice-' . $invoice->getInvoiceNumber() . '.pdf');

        return $this->createView(array(), 200);
    }

    /**
     * @Route("/email", name="invoice_api_email") 
     * @Method("POST")     
     */
    public function emailAction(Request $request) {
        $data = json_decode($request->getContent(), true);     
        
        $invoice = $this->getInvoiceRepository()
                ->createCurrentQueryBuilder('i')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $data['invoice_id'])
                ->getSingleResult();
        
        $data = array(
            'from' => array($this->getUser()->getEmail() => $this->getUser()->__toString()),
            'to' => explode(';', $invoice->getCustomer()->getEmailAddress()),
            'subject' => 'Invoice #' . $invoice->getInvoiceNumber()
        );        
        $data = array_merge($data, $this->getEmailCCandBCC());       

        if (!$invoice) {
            return $this->createView(array(), 404);
        }
        $this->get('ft.pdf')->generateInvoicePDF($invoice, $this->getUser(), 'F');

        $object = array('invoice' => $invoice, 'user' => $this->getUser());
        $notification = $this->get('ft.email.notification');
        $notification->send($data, $object, 'invoice_notification', '/tmp/Invoice-' . $invoice->getInvoiceNumber() . '.pdf');

        return $this->createView(array(), 200);
    }

    private function getEmailCCandBCC() {
        $cc = $this->getSettingRepository()
                        ->createCurrentQueryBuilder('s')
                        ->scopeByField('user', $this->getUser())
                        ->scopeByField('namespace', 'invoice')
                        ->scopeByField('name', 'alwayscc')
                        ->getSingleResult()->getValue();

        $bcc = $this->getSettingRepository()
                        ->createCurrentQueryBuilder('s')
                        ->scopeByField('user', $this->getUser())
                        ->scopeByField('namespace', 'invoice')
                        ->scopeByField('name', 'alwaysbcc')
                        ->getSingleResult()->getValue();
        $ar = array();
        if ($cc) {
            $ar['cc'] = explode(';', $cc);
        }
        if ($bcc) {
            $ar['bcc'] = explode(';', $bcc);
        }

        return $ar;
    }

}
