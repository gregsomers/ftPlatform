<?php

namespace FreelancerTools\PaymentBundle\Controller;

use FreelancerTools\CoreBundle\Controller\FreelancerAPIController as Base;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FreelancerTools\PaymentBundle\Form\PaymentType;
use FreelancerTools\PaymentBundle\Entity\Payment;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;


/**
 * @Route("/api/v1/payments")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class PaymentAPIController extends Base {

    /**
     * @Route("", name="payments_api_get")  
     * @Method("GET")  
     */
    public function getAction(Request $request) {
        $payments = $this->getPaymentRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
        ;

        $filter = json_decode($this->getRequest()->get('where'), true);
        if ($filter) {
            foreach ($filter as $key => $val) {
                $payments->scopeByField($key, $val['=']);
            }
        }

        return $this->createView($payments->getResults(), 200);
    }

    /**
     * @Route("/{id}", name="payments_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {
        $payment = $this->getPaymentRepository()
                ->createCurrentQueryBuilder('t')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$payment) {
            return $this->createView(array(), 404);
        }

        $formData = json_decode($request->getContent(), true);
        unset($formData['invoice']);
        $formData['invoice'] = $formData['invoice_id'];
        $formData['method'] = $formData['method']['id'];

        $form = $this->createForm(new PaymentType(), $payment, array('csrf_protection' => false));

        return $this->saveForm($form, $formData);
    }

    /**
     * @Route("/{id}", name="payments_api_delete") 
     * @Method("DELETE")     
     */
    public function deleteAction($id) {
        $data = $this->getPaymentRepository()
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

    /**
     * @Route("/email", name="payments_api_post") 
     * @Method("POST")     
     */
    public function postSingleAction(Request $request) {
        $formData = json_decode($request->getContent(), true);
        $payment = $this->getPaymentRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $formData['payment_id'])
                ->getSingleResult();
        if (!$payment) {
            return $this->createView(array(), 404);
        }

        //send email notification
        $data = array(
            'from' => array($this->getUser()->getEmail() => $this->getUser()->__toString()),
            'to' => explode(';', $payment->getInvoice()->getCustomer()->getEmailAddress()),
            'subject' => 'Payment Received for Invoice #' . $payment->getInvoice()->getInvoiceNumber()
        );
        $data = array_merge($data, $this->getEmailCCandBCC());
        $object = array('payment' => $payment, 'invoice' => $payment->getInvoice(), 'user' => $this->getUser());
        $this->get('ft.email.notification')->send($data, $object, 'payment_notification', $this->get('ft.pdf')->generatePaymentPDF($payment, $this->getUser(), 'F'));

        return $this->createView(array(), 200);
    }

    /**
     * @Route("", name="payments_api_payment") 
     * @Method("POST")     
     */
    public function sendPaymentEmailAction(Request $request) {
        $payment = new Payment();

        $formData = json_decode($request->getContent(), true);
        $formData['invoice'] = $formData['invoice_id'];
        $formData['method'] = $formData['method']['id'];

        $form = $this->createForm(new PaymentType(), $payment, array('csrf_protection' => false));
        return $this->saveForm($form, $formData);
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
