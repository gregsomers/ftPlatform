<?php

namespace FreelancerTools\InvoicingBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FreelancerTools\InvoicingBundle\Form\InvoiceType;
use FreelancerTools\InvoicingBundle\Form\PaymentType;
use FreelancerTools\InvoicingBundle\Form\EmailInvoiceType;
use FreelancerTools\InvoicingBundle\Entity\Invoice;
use FreelancerTools\InvoicingBundle\Entity\Payment;
use FreelancerTools\InvoicingBundle\Entity\InvoiceItem;
use \DateTime;
use \mPDF;

class InvoiceController extends Controller {

    /**
     * @Route("/invoices/", name="invoices")
     * @Template()
     */
    public function indexAction() {

        $invoices = $this->getInvoiceStorage()->getRepo()->getInvoices($this->getUser());

        return array(
            'invoices' => $invoices
        );
    }

    /**
     * @Route("/invoices/recurring/", name="invoices_recurring")
     * @Template()
     */
    public function recurringAction() {

        $invoices = $this->getInvoiceStorage()->getRepo()->getInvoices($this->getUser(), true);

        return array(
            'invoices' => $invoices
        );
    }

    /**
     * @Route("/guest/invoices/{token}", name="invoice_guest")
     * @Template("FreelancerToolsInvoicingBundle:Invoice:view.html.twig")
     * 
     */
    public function guestAction($token) {

        $invoice = $this->getInvoiceRepository()->findOneByToken($token);

        return array(
            'invoice' => $invoice,
            'user' => $this->getUser()
        );
    }

    /**
     * @Route("/invoices/delete/{id}", name="invoice_delete")

     */
    public function deleteAction($id) {

        $invoice = $this->getInvoiceRepository()->findOneById($id);

        foreach ($invoice->getTimeslices() as $slice) {
            $slice->setInvoicedAt(null);
            $slice->setInvoiced(false);
            $slice->setInvoiceItem(null);
        }

        $this->getDoctrine()->getManager()->remove($invoice);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('invoices'));
    }

    /**
     * @Route("/invoices/email/{id}", name="invoice_email")
     * @Template("FreelancerToolsInvoicingBundle:Invoice:emailOptions.html.twig")
     * //@Template("FreelancerToolsInvoicingBundle:Invoice:email.html.twig")
     */
    public function emailInvoiceAction($id) {
        $invoice = $this->getInvoiceRepository()->findOneById($id);

        $this->generatePDF($invoice, 'F');

        $data = array(
            'from' => array($this->getUser()->getEmail() => $this->getUser()->__toString()),
            'to' => explode(';', $invoice->getCustomer()->getEmailAddress()),
            'subject' => 'Invoice #' . $invoice->getInvoiceNumber()
        );
        $object = array('invoice' => $invoice, 'user' => $this->getUser());
        $notification = $this->get('ft.email.notification');
        $notification->send($data, $object, 'invoice_notification', '/tmp/Invoice-' . $invoice->getInvoiceNumber() . '.pdf');

        $this->get('session')->getFlashBag()->add(
                'success', "Invoice sent"
        );

        return $this->redirect($this->generateUrl('invoice_edit', array('id' => $invoice->getId())));
    }

    /**
     * @Route("/invoices/email/custom/{id}", name="invoice_custom_email")
     * @Template("FreelancerToolsInvoicingBundle:Invoice:emailOptions.html.twig")
     * //@Template("FreelancerToolsInvoicingBundle:Invoice:email.html.twig")
     */
    public function emailAction($id) {
        $invoice = $this->getInvoiceRepository()->findOneById($id);

        $form = $this->createForm(new EmailInvoiceType());
        $form->get('name')->setData($this->getUser()->__toString());
        $form->get('email')->setData($this->getUser()->getEmail());
        $form->get('cc')->setData($this->getUser()->getEmail());
        $form->get('to')->setData($invoice->getCustomer()->getEmailAddress());
        $form->get('subject')->setData('Invoice #' . $invoice->getInvoiceNumber());

        $template = $this->getTemplateStorage()->findOneBy(array('name' => 'invoice_notification'))->getBody();
        $this->get('ft.email_token_transformer')->transform($template);

        $twig = clone $this->get('twig');
        $twig->setLoader(new \Twig_Loader_String());
        $html = $twig->render(
                $template, array('invoice' => $invoice, 'user' => $this->getUser())
        );

        $form->get('body')->setData($html);

        return array(
            'form' => $form,
            'invoice' => $invoice
        );
    }

    /**
     * @Route("/invoices/email/send/{id}", name="invoice_email_send")
     * //@Template("FreelancerToolsInvoicingBundle:Invoice:emailOptions.html.twig")
     * @Template("FreelancerToolsInvoicingBundle:Invoice:email.html.twig")
     */
    public function emailSendAction($id, Request $request) {
        $invoice = $this->getInvoiceRepository()->findOneById($id);

        $options = $this->createForm(new EmailInvoiceType())->bind($request)->all();

        //save the pdf to a file
        $this->generatePDF($invoice, 'F');

        $data = array(
            'from' => array($options['email']->getData() => $options['name']->getData()),
            'to' => explode(';', $options['to']->getData()),
            'subject' => $options['subject']->getData(),
            'cc' => $options['cc']->getData(),
            'bcc' => $options['bcc']->getData()
        );
        $object = array('invoice' => $invoice, 'user' => $this->getUser());
        $notification = $this->get('ft.email.notification');
        $notification->send($data, $object, 'invoice_notification', '/tmp/Invoice-' . $invoice->getInvoiceNumber() . '.pdf');

        $this->get('session')->getFlashBag()->add(
                'success', "Invoice sent"
        );
        return $this->redirect($this->generateUrl('invoice_edit', array('id' => $invoice->getId())));

    }

    private function generatePDF($invoice, $mode = 'I') {

        $mpdf = new mPDF();

        $parameters = array(
            'invoice' => $invoice,
            'user' => $this->getUser()
        );

        $templateFile = "FreelancerToolsInvoicingBundle:Invoice:pdf.html.twig";
        $html = $this->get('templating')->render($templateFile, $parameters);

        //echo $html;

        $mpdf->WriteHTML($html);

        if ($mode == 'F') {
            $mpdf->Output('/tmp/Invoice-' . $invoice->getInvoiceNumber() . '.pdf', $mode);
            return true;
        } else {
            $mpdf->Output('Invoice-' . $invoice->getInvoiceNumber() . '.pdf', $mode);
        }
        exit();
    }

    /**
     * @Route("/guest/invoices/pdf/{token}", name="invoice_pdf_guest")
     * @Route("/invoices/pdf/{id}", name="invoice_pdf")
     * @Template()
     */
    public function pdfAction($token) {

        $invoice = $this->getInvoiceRepository()->findOneByToken($token);

        $this->generatePDF($invoice);
    }

    /**
     * @Route("/pdfp/{id}", name="invoice_pdfp")
     * @Template("FreelancerToolsInvoicingBundle:Invoice:pdf.html.twig")
     */
    public function pdfpAction($id) {

        $invoice = $this->getInvoiceRepository()->findOneById($id);

        $parameters = array(
            'billing' => null,
            'shipping' => null,
            'invoice' => $invoice
        );

        return $parameters;
    }

    /**
     * @Route("/invoices/add", name="invoice_add")
     * @Template("FreelancerToolsInvoicingBundle:Invoice:edit.html.twig")
     */
    public function addAction() {

        $settings = array();
        foreach ($this->getSettingRepository()->findByNamespace('invoice') as $setting) {
            $settings[$setting->getName()] = $setting->getValue();
        }

        $invoice = new Invoice();
        
        $invoice->setInvoiceNumber($settings['prefix'] . date("Y") . date("m") . $settings['next_id']);
        $invoice->setInvoiceDate(new DateTime("now"));
        $invoice->setInvoiceDueDate(new DateTime("now + 1 month"));
        $invoice->setTerms($settings['default_terms']);

        $form = $this->createForm(new InvoiceType(), $invoice);

        return array(
            'invoice' => $invoice,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/invoices/create", name="invoice_create")
     * @Template("FreelancerToolsInvoicingBundle:Invoice:edit.html.twig")
     */
    public function createAction(Request $request) {

        $nextIdEntity = $this->getSettingsStorage()->findOneBy(array('namespace' => 'invoice', 'name' => 'next_id'));
        $nextIdEntity->setValue($nextIdEntity->getValue() + 1);

        $invoice = new Invoice();
        $form = $this->createForm(new InvoiceType(), $invoice)->bind($request);

        if ($form->isValid()) {
            foreach ($invoice->getItems() as $item) {
                $item->setInvoice($invoice);
            }
            $this->getSettingsStorage()->update($nextIdEntity);
            $this->getInvoiceStorage()->update($invoice);

            $this->get('session')->getFlashBag()->add(
                    'success', "Invoice Saved."
            );
            return $this->redirect($this->generateUrl('invoice_edit', array('id' => $invoice->getId())));
        }

        return array(
            'invoice' => $invoice,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/invoices/edit/{id}", name="invoice_edit")
     * @Template("FreelancerToolsInvoicingBundle:Invoice:edit.html.twig")
     */
    public function editAction($id) {
        //$invoice = $this->getInvoiceRepository()->findOneById($id);
        $invoice = $this->getInvoiceStorage()->findOneBy(array('id' => $id));

        $form = $this->createForm(new InvoiceType(), $invoice);

        return array(
            'invoice' => $invoice,
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/invoices/deleteItem/{id}", name="invoice_delete_item")
     * 
     */
    public function deleteInvoiceItemAction($id) {

        $repo = $this->getDoctrine()->getRepository('FreelancerToolsInvoicingBundle:InvoiceItem');

        $ii = $repo->findOneById($id);
        $invoice = $ii->getInvoice();

        foreach ($ii->getTimeslices() as $slice) {
            $slice->setInvoicedAt(null);
            $slice->setInvoiced(false);
            $slice->setInvoice(null);
        }

        $this->getDoctrine()->getManager()->remove($ii);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirect($this->generateUrl('invoice_edit', array('id' => $invoice->getId())));
    }

    /**
     * @Route("/invoices/update/{id}", name="invoice_update")
     * @Template("FreelancerToolsInvoicingBundle:Invoice:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {

        $invoice = $this->getInvoiceRepository()->findOneById($id);

        $form = $this->createForm(new InvoiceType(), $invoice)->bind($request);

        if ($form->isValid()) {
            foreach ($invoice->getItems() as $item) {
                $item->setInvoice($invoice);
                $item->setUser($this->getUser());
            }

            $this->getDoctrine()->getManager()->persist($invoice);
            $this->getDoctrine()->getManager()->flush();
            $this->get('session')->getFlashBag()->add(
                    'success', "Invoice Saved."
            );
            return $this->redirect($this->generateUrl('invoice_edit', array('id' => $invoice->getId())));
        }

        return array(
            'invoice' => $invoice,
            'form' => $form->createView(),
        );
    }

    protected function getInvoiceRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsInvoicingBundle:Invoice');
    }

    protected function getEmailTemplateRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsInvoicingBundle:EmailTemplate');
    }

    public function getSettingRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsCoreBundle:Setting');
    }

    protected function getTemplateStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\CoreBundle\Entity\EmailTemplate');
    }

    protected function getInvoiceStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\InvoicingBundle\Entity\Invoice');
    }

    protected function getSettingsStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\CoreBundle\Entity\Setting');
    }

}
