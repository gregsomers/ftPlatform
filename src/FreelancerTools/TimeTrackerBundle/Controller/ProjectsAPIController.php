<?php

namespace FreelancerTools\TimeTrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\TimeTrackerBundle\Form\ProjectType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use FreelancerTools\TimeTrackerBundle\Entity\Project;
use FreelancerTools\CoreBundle\Controller\FreelancerAPIController as Base;
use JMS\SecurityExtraBundle\Annotation\PreAuthorize;


/**
 * @Route("/api/v1/projects")
 * @PreAuthorize("hasRole('ROLE_USER')")
 */
class ProjectsAPIController extends Base {

    /**
     * @Route("/", name="project_api_list") 
     * @Method("GET")     
     */
    public function getProjectsAction() {
        $projects = $this->getProjectRepository()
                ->createCurrentQueryBuilder('p')
                ->addLeftJoins(array('a' => 'activities', 's' => 'a.timeslices', 'c' => 'customer'))
                ->scopeByField('user', $this->getUser())
        ;

        return $this->createView($projects->getResults(), 200);
    }

    /**
     * @Route("/{id}", name="project_api_get") 
     * @Method("GET")     
     */
    public function getProjectAction($id) {
        $data = $this->getProjectRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->addLeftJoins(array('a' => 'activities', 's' => 'a.timeslices', 'c' => 'customer'))
                ->getSingleResult();

        return $this->createView($data, 200);
    }

    /**
     * @Route("/{id}", name="projects_api_put") 
     * @Method("PUT")     
     */
    public function putSingleAction(Request $request, $id) {
        $project = $this->getProjectRepository()
                ->createCurrentQueryBuilder('p')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        if (!$project) {
            return $this->createView(array(), 404);
        }
        $formData = json_decode($request->getContent(), true);
        $formData['customer'] = $formData['client']['id'];

        $form = $this->createForm(new ProjectType($this->getDoctrine()->getManager(), $this->getUser()), $project, array('csrf_protection' => false));

        return $this->saveForm($form, $formData);
    }

    /**
     * @Route("", name="projects_api_post") 
     * @Method("POST")     
     */
    public function postSingleAction(Request $request) {
        $project = new Project();       

        $formData = json_decode($request->getContent(), true);
        $formData['customer'] = $formData['client']['id'];

        $form = $this->createForm(new ProjectType($this->getDoctrine()->getManager(), $this->getUser()), $project, array('csrf_protection' => false));

        return $this->saveForm($form, $formData);
    }
    
    /**
     * @Route("/{id}", name="projects_api_delete") 
     * @Method("DELETE")     
     */
    public function deleteAction($id) {
        $data = $this->getProjectRepository()
                ->createCurrentQueryBuilder('t')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        $this->deleteEntity($data);
        return $this->createView(array(), 200);
    }

    /**
     * @Route("/{projectId}/activities/{activityId}/timeslices/{id}", name="project_api_timeslice") 
     * @Method("GET")     
     */
    public function getTimesliceAction($id) {
        $data = $this->getTimesliceRepository()
                ->createCurrentQueryBuilder('c')
                ->scopeByField('user', $this->getUser())
                ->scopeByField('id', $id)
                ->getSingleResult();

        return $this->createView($data, 200);       
    }

}
