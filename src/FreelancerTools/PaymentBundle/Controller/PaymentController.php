<?php

namespace FreelancerTools\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FreelancerTools\PaymentBundle\Form\PaymentType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \mPDF;

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
        if ($invoice->getBalance() == 0) {
            $invoice->setStatus(2);
        }

        $this->getInvoiceStorage()->update($invoice);
        $this->getPaymentsStorage()->update($payment);
        
        if ($form->get('emailNotification')->getData()) {            
            $this->emailPaymentNotification($payment);
        }

        return $this->redirect($this->generateUrl('payments'));

        /* return array(
          'payment' => $payment,
          'form' => $form->createView(),
          ); */
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
        $invoice->addPayment($payment);

        if ($invoice->getBalance() == 0) {
            $invoice->setStatus(2);
        }

        $this->getPaymentsStorage()->update($payment);

        $this->get('session')->getFlashBag()->add(
                'success', "Payment entered"
        );

        if ($form->get('emailNotification')->getData()) {            
            $this->emailPaymentNotification($payment);
        }

        return $this->redirect($this->generateUrl('invoice_edit', array('id' => $invoice->getId())));
    }

    private function emailPaymentNotification($payment) {

        //save the pdf to a file
        $this->generatePDF($payment, 'F');
        
        $data = array(
            'from' => array($this->getUser()->getEmail() => $this->getUser()->__toString()),
            'to' => explode(';', $payment->getInvoice()->getCustomer()->getEmailAddress()),
            'subject' => 'Payment received for invoice #' . $payment->getInvoice()->getInvoiceNumber()
        );
        $object = array('payment' => $payment, 'invoice' => $payment->getInvoice(), 'user' => $this->getUser());
        $notification = $this->get('ft.email.notification');
        $notification->send($data, $object, 'payment_notification', '/tmp/payment-' . $payment->getInvoice()->getInvoiceNumber(). "-" . $payment->getId() . '.pdf');
    }
    
    private function generatePDF($payment, $mode = 'I') {

        $mpdf = new mPDF();

        $parameters = array(
            'payment' => $payment,
            'user' => $this->getUser()
        );

        $templateFile = "FreelancerToolsPaymentBundle:Payment:pdf.html.twig";
        $html = $this->get('templating')->render($templateFile, $parameters);       

        $mpdf->WriteHTML($html);

        if ($mode == 'F') {
            $mpdf->Output('/tmp/payment-' . $payment->getInvoice()->getInvoiceNumber(). "-" . $payment->getId() . '.pdf', $mode);
            return true;
        } else {
            $mpdf->Output('payment-' .$payment->getInvoice()->getInvoiceNumber(). "-" . $payment->getId() . '.pdf', $mode);
        }
        exit();
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

    protected function getSettingStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\CoreBundle\Entity\Setting');
    }

}
