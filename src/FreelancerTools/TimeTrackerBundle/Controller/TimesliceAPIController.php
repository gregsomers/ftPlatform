<?php

namespace FreelancerTools\TimeTrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\TimeTrackerBundle\Form\TimesliceType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FOS\RestBundle\View\View;
use FreelancerTools\CoreBundle\Controller\FreelancerAPIController;
use FreelancerTools\TimeTrackerBundle\Entity\Timeslice;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;


/**
 * @Route("/api/v1/timeslices")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class TimesliceAPIController extends FreelancerAPIController {

    /**
     * @Route("", name="timeslices_api_get") 
     * //@Method({"GET", "POST"})   
     * @Method("GET")  
     */
    public function getAction(Request $request) {
        $slices = $this->getTimesliceRepository() //$data['projects']
                ->createCurrentQueryBuilder('t')
                ->scopeByField('user', $this->getUser())
                ;
        
        $filter = json_decode($this->getRequest()->get('where'), true);
        //print_r($filter);        
        if($filter) {
            foreach($filter as $key => $val) {               
                $slices->scopeByField($key, $val['=']);
            }
        }

        return $this->createView($slices->getResults(), 200);
    }

    /**
     * @Route("", name="timeslices_api_post") 
     * @Method("POST")
     */
    public function createAction(Request $request) {

        $formData = json_decode($request->getContent(), true);        

        $activity = $this->getActivityRepository() //$data['projects']
                ->createCurrentQueryBuilder('a')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $formData['activity_id'])
                ->getSingleResult();

        if (!$activity) {
            return $this->createView(array(), 404);
        }

        $form = $this->createForm(new TimesliceType($this->getDoctrine()->getManager(), $this->getUser(), $activity->getCustomer()), new Timeslice(), array('csrf_protection' => false));
        //can't change the activity after it's been associated
        $formData['activity'] = $activity->getId();
        return $this->saveForm($form, $formData);
    }

    /**
     * @Route("/{id}", name="timeslices_api_single") 
     * @Method("GET")     
     */
    public function getSingleAction($id) {
        $data = $this->getTimesliceRepository()
                ->createCurrentQueryBuilder('t')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        $view = new View($data, 200);
        $view->setFormat('json');
        return $view;
    }

    /**
     * @Route("/{id}", name="timeslices_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {
        $slice = $this->getTimesliceRepository()
                ->createCurrentQueryBuilder('t')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$slice) {
            return $this->createView(array(), 404);
        }

        $formData = json_decode($request->getContent(), true);
        $form = $this->createForm(new TimesliceType($this->getDoctrine()->getManager(), $this->getUser(), $slice->getActivity()->getCustomer()), $slice, array('csrf_protection' => false));
        //can't change the activity after it's been associated
        $formData['activity'] = $slice->getActivity()->getId();
        return $this->saveForm($form, $formData);
    }
    
    /**
     * @Route("/{id}", name="timeslices_api_delete") 
     * @Method("DELETE")     
     */
    public function deleteAction($id) {
        $data = $this->getTimesliceRepository()
                ->createCurrentQueryBuilder('t')
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
