<?php

namespace FreelancerTools\TimeTrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\TimeTrackerBundle\Form\ActivityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FreelancerTools\CoreBundle\Controller\FreelancerAPIController;
use FreelancerTools\TimeTrackerBundle\Entity\Activity;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;


/**
 * @Route("/api/v1/activities")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class ActivitiesAPIController extends FreelancerAPIController {

    /**
     * @Route("", name="activities_api_post") 
     * @Method("POST")
     */
    public function createAction(Request $request) {

        $formData = json_decode($request->getContent(), true);

        $project = $this->getProjectRepository() //$data['projects']
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $formData['project_id'])
                ->getSingleResult();

        if (!$project) {
            return $this->createView(array(), 404);
        }

        $activity = new Activity();
        $activity->setProject($project);
        $activity->setCustomer($project->getCustomer());
        //$activity->set

        $form = $this->createForm(new ActivityType($this->getDoctrine()->getManager(), $this->getUser()), $activity, array('csrf_protection' => false));

        return $this->saveForm($form, $formData['activity']);
    }

    /**
     * @Route("/{id}", name="activities_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {
        $activity = $this->getActivityRepository()
                ->createCurrentQueryBuilder('t')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$activity) {
            return $this->createView(array(), 404);       
            
        }
        $formData = json_decode($request->getContent(), true);
        
        $form = $this->createForm(new ActivityType($this->getDoctrine()->getManager(), $this->getUser()), $activity, array('csrf_protection' => false));       

        return $this->saveForm($form, $formData);
    }

    /**
     * @Route("/{id}", name="activities_api_delete") 
     * @Method("DELETE")     
     */
    public function deleteAction($id) {
        $data = $this->getActivityRepository()
                ->createCurrentQueryBuilder('t')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        $this->deleteEntity($data);
        return $this->createView(array(), 200);
    }

}
