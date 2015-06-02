<?php

namespace FreelancerTools\PaymentBundle\Controller;

use FreelancerTools\CoreBundle\Controller\FreelancerAPIController as Base;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FreelancerTools\PaymentBundle\Form\ReceiptType;
use FreelancerTools\PaymentBundle\Entity\Receipt;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @Route("/api/v1/receipts")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class ReceiptAPIController extends Base {

    /**
     * @Route("", name="receipts_api_get")  
     * @Method("GET")  
     */
    public function getAction(Request $request) {
        $entities = $this->getReceiptRepository()
                ->createCurrentQueryBuilder('r')
                ->scopeByField('user', $this->getUser())
        ;

        $filter = json_decode($request->get('where'), true);
        if ($filter) {
            foreach ($filter as $key => $val) {
                $entities->scopeByField($key, $val['=']);
            }
        }

        return $this->createView($entities->getResults(), 200);
    }
    
    /**
     * @Route("/{id}", name="receipts_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {
        $entity = $this->getReceiptRepository()
                ->createCurrentQueryBuilder('r')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$entity) {
            return $this->createView(array(), 404);
        }

        $formData = json_decode($request->getContent(), true);
        $form = $this->createForm(new ReceiptType(), $entity, array('csrf_protection' => false));
        return $this->saveForm($form, $formData);
    }


    /**
     * @Route("", name="receipts_api_post") 
     * @Method("POST")     
     */
    public function postAction(Request $request) {
        $entity = new Receipt();
        $formData = json_decode($request->getContent(), true);

        foreach ($_FILES as $file) {
            $uploadedFile = new UploadedFile(
                    $file['tmp_name'], $file['name'], $file['type'], $file['size'], $file['error']);
        }
        $formData['file'] = $uploadedFile;

        $form = $this->createForm(new ReceiptType(), $entity, array('csrf_protection' => false));        

        $formData['date'] = (new \DateTime("today"))->format('Y-m-d');
        return $this->saveForm($form, $formData);        
    }
    
    /**
     * @Route("/{id}", name="receipts_api_del") 
     * @Method("DELETE")     
     */
    public function deleteAction($id) {
        $data = $this->getReceiptRepository()
                ->createCurrentQueryBuilder('r')
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
