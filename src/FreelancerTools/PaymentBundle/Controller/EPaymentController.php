<?php

namespace FreelancerTools\PaymentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Payum\Core\Request\GetHumanStatus;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use FreelancerTools\PaymentBundle\Entity\Payment;

class EPaymentController extends Controller {

    /**
     * @Route("/ePayment/start/{token}", name="ePayment")
     * @Template()
     */
    public function prepareAction($token) {
        //$paymentName = 'offline';
        $paymentName = 'ppe';       

        //$storage = $this->get('payum')->getStorage('FreelancerTools\PaymentBundle\Entity\Order');

        $invoice = $this->getInvoiceRepository()->findOneBy(array('token' => $token));

        //create payment entity to link to the invoice        
        $payment = $this->getPaymentsStorage()->create();
        $payment
                ->setMethod($this->getPaymentMethodStorage()->findOneBy(array('name' => 'PayPal')))
                ->setDate("now")
                ->setAmount(0)
                ->setInvoice($invoice)
                ->setUser($invoice->getUser())
        ;
        $this->getPaymentsStorage()->update($payment);

        //create the order
        $order = $this->getOrderStorage()->create();
        $order->setNumber(uniqid());
        $order->setCurrencyCode($invoice->getCurrency());
        $order->setTotalAmount($invoice->getBalance()*100 * 1.015);
        $order->setDescription('Invoice ' . $invoice->getInvoiceNumber());
        $order->setClientId($invoice->getCustomer()->getId());
        $order->setClientEmail($invoice->getCustomer()->getEmailAddress());
        $order->setPayment($payment);
        $this->getOrderStorage()->update($order);

        $captureToken = $this->get('payum.security.token_factory')->createCaptureToken(
                $paymentName, $order, 'ePayment_done' // the route to redirect after capture
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * @Route("/ePayment/done", name="ePayment_done")
     * @Template()
     */
    public function doneAction(Request $request) {
        $token = $this->get('payum.security.http_request_verifier')->verify($request);

        $payumpayment = $this->get('payum')->getPayment($token->getPaymentName());

        // you can invalidate the token. The url could not be requested any more.
        // $this->get('payum.security.http_request_verifier')->invalidate($token);
        // Once you have token you can get the model from the storage directly. 
        //$identity = $token->getDetails();
        //$order = $payum->getStorage($identity->getClass())->find($identity);
        // or Payum can fetch the model for you while executing a request (Preferred).
        $payumpayment->execute($status = new GetHumanStatus($token));
        $order = $status->getFirstModel();

        $payment = $order->getPayment();
        if ($status->getValue() == 'captured') {
            $payment->setAmount($payment->getInvoice()->getBalance());
            $this->getPaymentsStorage()->update($payment);

            $this->get('session')->getFlashBag()->add(
                    'success', "Payment has been received, thank you."
            );
        } elseif ($status->getValue() == 'canceled') {
            $order->setPayment(null);
            $this->getPaymentsStorage()->delete($payment);

            $this->get('session')->getFlashBag()->add(
                    'info', "Your payment has been canceled."
            );

        } else {
            //consider the transaction failed
            $order->setPayment(null);            
            $this->getPaymentsStorage()->delete($payment);
            
            $this->get('session')->getFlashBag()->add(
                    'notice', "Your payment has not been received, unknown error."
            );
        }

        return $this->redirect($this->generateUrl('invoice_guest', array('token' => $payment->getInvoice()->getToken())));



        // you have order and payment status 
        // so you can do whatever you want for example you can just print status and payment details.
        return new JsonResponse(array(
            'status' => $status->getValue(),
            'order' => array(
                'total_amount' => $order->getTotalAmount(),
                'currency_code' => $order->getCurrencyCode(),
                'details' => $order->getDetails(),
            ),
        ));
    }
    
    protected function getOrderStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\PaymentBundle\Entity\Order');
    }

    protected function getPaymentMethodStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\PaymentBundle\Entity\PaymentMethod');
    }
    
    protected function getPaymentsStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\PaymentBundle\Entity\Payment');
    } 

    protected function getInvoiceRepository() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\InvoicingBundle\Entity\Invoice');
    }

}
