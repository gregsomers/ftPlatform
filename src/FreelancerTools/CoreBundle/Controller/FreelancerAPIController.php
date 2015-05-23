<?php

namespace FreelancerTools\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\View\View;
use Symfony\Component\Form\Form;

class FreelancerAPIController extends Controller {

    
    /**
     * Create a rest view
     *
     * @param null $data
     * @param int  $statuscode
     *
     * @return \FOS\RestBundle\View\View
     */
    protected function createView($data = null, $statuscode = 200)
    {
        $view = new View($data, $statuscode);
        $view->setFormat('json');
        return $view;
    }
    
    /**
     * save form
     *
     * @param Form  $form
     * @param array $data
     *
     * @return View
     */
    protected function saveForm(Form $form, $data)
    {
        // clean array from non existing keys to avoid extra data
        foreach ($data as $key => $value) {
            if (!$form->has($key)) {
                unset($data[$key]);
            }
        }
        //print_r($data);
        // bind data to form
        $form->bind($data);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $form->getData();            
            // save change to database
            $em->persist($entity);
            $em->flush();
            $em->refresh($entity);
            // push back the new object
            $view = $this->createView($entity, 200);
        } else {
            $errors[] = $form->getErrorsAsString();            
            foreach ($form->getErrors() as $er) {
                $errors[] = $er->getMessage();
            }            
            // return error string from form
            $view = $this->createView(array('errors' => $errors), 400);
        }
        return $view;
    }
    
    public function deleteEntity($entity) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();
    }    

    public function getClientsRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsCoreBundle:Customer'); 
    }
    
    public function getInvoiceRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsInvoicingBundle:Invoice');
    }

    public function getInvoiceItemRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsInvoicingBundle:InvoiceItem');
    }
    
    public function getProjectRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsTimeTrackerBundle:Project');
    }

    public function getTimesliceRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsTimeTrackerBundle:Timeslice');
    }
    
    public function getActivityRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsTimeTrackerBundle:Activity');
    }
    
    public function getSettingRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsCoreBundle:Setting');
    }
    
    protected function getTemplateRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsCoreBundle:EmailTemplate');
    }
    
    protected function getPaymentRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsPaymentBundle:Payment');
    }
    
    protected function getPaymentMethodRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsPaymentBundle:PaymentMethod');
    }
    protected function getCurrencyRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsPaymentBundle:Currency');
    }

}
