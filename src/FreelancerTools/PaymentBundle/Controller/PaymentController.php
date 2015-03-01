<?php

namespace FreelancerTools\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FreelancerTools\PaymentBundle\Form\PaymentType;
use Symfony\Component\HttpFoundation\RedirectResponse;


class PaymentController extends Controller {
    
    /**
     * @Route("/payments", name="payments")
     * @Template()
     */
    public function indexAction() {
        $payments = $this->getPaymentsStorage()->findBy(array('user' => $this->getUser()));        

        return array(
            'payments' => $payments,            
        );
    }
    
    /**
     * @Route("/payment/{id}", name="payment_edit")
     * @Template()
     */
    public function editAction($id) {
        $payment = $this->getPaymentsStorage()->findOneBy(array('id' => $id));        
        $form = $this->createForm(new PaymentType(), $payment);

        return array(
            'payment' => $payment, 
            'form' => $form->createView(),
        );
    }
    
    /**
     * @Route("/payment/update/{id}", name="payment_update")
     * @Template("FreelancerToolsPaymentBundle:Payment:edit.html.twig")
     */
    public function updateAction($id, Request $request) {
        $payment = $this->getPaymentsStorage()->findOneBy(array('id' => $id));  
        $form = $this->createForm(new PaymentType(), $payment)->bind($request);
        //if($form->isValid()) {
        $invoice = $payment->getInvoice();
        if($invoice->getBalance() == 0) {
            $invoice->setStatus(2);
        }
        
        $this->getInvoiceStorage()->update($invoice);
        $this->getPaymentsStorage()->update($payment);       
        
        return $this->redirect($this->generateUrl('payments'));       

        /*return array(
            'payment' => $payment, 
            'form' => $form->createView(),
        );*/
    }

    /**
     * @Route("/payment/invoice/{id}", name="invoice_payment")
     * @Template("FreelancerToolsPaymentBundle:Payment:payment.html.twig")
     */
    public function paymentAction($id) {
        $invoice = $this->getInvoiceStorage()->findOneBy(array('id' => $id)); 

        $payment = $this->getPaymentsStorage()->create();
        $payment->setAmount($invoice->getBalance())->setDate("now");        

        $form = $this->createForm(new PaymentType(), $payment);

        return array(
            'invoice' => $invoice,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/payment/invoices/submit/{id}", name="invoice_payment_submit")
     * @Template("FreelancerToolsPaymentBundle:Payment:payment.html.twig")
     */
    public function paymentSubmitAction($id, Request $request) {
        $invoice = $this->getInvoiceStorage()->findOneBy(array('id' => $id)); 

        $payment = $this->getPaymentsStorage()->create();

        $form = $this->createForm(new PaymentType(), $payment)->bind($request);

        //if($form->isValid()) {            
        //$payment->setInvoice($invoice)->setUser($this->getUser());  
        $payment->setInvoice($invoice);
        
        if($invoice->getBalance() - $payment->getAmount() == 0) {
            $invoice->setStatus(2);
        }
        
        $this->getPaymentsStorage()->update($payment);        

        $this->get('session')->getFlashBag()->add(
                'success', "Payment entered"
        );
        return $this->redirect($this->generateUrl('invoice_edit', array('id' => $invoice->getId())));        
    }
    
    /**
     * @Route("/delete/{id}", name="payment_delete")
     * 
     */
    public function deleteAction($id) {
        $payment = $this->getPaymentsStorage()->findOneBy(array('id' => $id));  

        if (!$payment) {
            throw $this->createNotFoundException('Unable to find entity.');
        }

        $this->getPaymentsStorage()->delete($payment);

        return new RedirectResponse($this->get('request')->server->get('HTTP_REFERER'));
    }
    
    protected function getPaymentsStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\PaymentBundle\Entity\Payment');
    }
    
    protected function getInvoiceStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\InvoicingBundle\Entity\Invoice');
    } 
}
