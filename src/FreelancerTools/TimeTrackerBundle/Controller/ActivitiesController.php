<?php
namespace FreelancerTools\TimeTrackerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\TimeTrackerBundle\Form\ActivityType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use \DateTime;

/**
 * @Route("/activities")
 */
class ActivitiesController extends Controller {

    /**
     * @Route("/", name="activities")
     * @Template()
     */
    public function indexAction() {

        $activities = $this->getActivityStorage()->getRepo()->getAllActive();

        return array('activities' => $activities);
    }

    /**
     * @Route("/show/{id}", name="activity_show")
     * @Template()
     */
    public function showAction($id) {
        $activity = $this->getActivityStorage()->getRepo()->getSingleActivity($id);

        if (!$activity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }

        return array('activity' => $activity);
    }

    /**
     * @Route("/add", name="activity_add")
     * @Template()
     */
    public function addAction() {

        $form = $this->createForm(new ActivityType($this->getDoctrine()->getManager(), $this->getUser()), $this->getActivityStorage()->create());

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/create", name="activity_create")
     * @Template("FreelancerToolsTimeTrackerBundle:Activities:add.html.twig")
     */
    public function createAction(Request $request) {
        $activity = $this->getActivityStorage()->create();
        $form = $this->createForm(new ActivityType($this->getDoctrine()->getManager(), $this->getUser()), $activity);
        $form->bind($request);

        if ($form->isValid()) {
            $activity->setUser($this->getUser());
            $this->getActivityStorage()->update($activity);
            $this->get('session')->getFlashBag()->add(
                    'success', "Activity has been created."
            );
            return $this->redirect($this->generateUrl('projects', array('id' => $activity->getProject()->getId())));
        }
        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/timeslice/stop/{id}", name="activity_stop_time_slice")
     */
    public function stopTimeSliceAction($id) {
        $timeslice = $this->getTimesliceStorage()->findOneBy(array('id' => $id));
        $timeslice->setStoppedAt(new DateTime('now'));
        $this->getTimesliceStorage()->update($timeslice);

        return $this->redirect($this->generateUrl('activities'));
    }

    /**
     * @Route("/timeslice/create/{id}", name="activity_create_time_slice")
     */
    public function createTimeSliceAction($id) {
        $activity = $this->getActivityStorage()->findOneBy(array('id' => $id));

        $timeslice = $this->getTimesliceStorage()
                ->create()
                ->setActivity($activity)
                ->setUser($this->getUser())
                ->setStartedAt(new DateTime('now'));
        $activity
                ->addTimeslice($timeslice)
                ->setUpdatedAt(new DateTime('now'));

        $this->getActivityStorage()->update($activity);
        $this->getTimesliceStorage()->update($timeslice);

        return new RedirectResponse($this->get('request')->server->get('HTTP_REFERER'));
        //return $this->redirect($this->generateUrl('activities'));
    }

    /**
     * @Route("/edit/{id}", name="activity_edit")
     * @Template()
     */
    public function editAction($id) {
        $activity = $this->getActivityStorage()->findOneBy(array('id' => $id));

        $form = $this->createForm(new ActivityType($this->getDoctrine()->getManager(), $this->getUser()), $activity);
        return array(
            'form' => $form->createView(),
            'activity' => $activity
        );
    }

    /**
     * @Route("/{id}/update", name="activity_update")
     * @Template("FreelancerToolsTimeTrackerBundle:Activities:edit.html.twig")
     */
    public function updateAction(Request $request, $id) {
        $activity = $this->getActivityStorage()->findOneBy(array('id' => $id));
        $form = $this->createForm(new ActivityType($this->getDoctrine()->getManager(), $this->getUser()), $activity)->bind($request);

        if ($form->isValid()) {
            $this->getActivityStorage()->update($activity);
            $this->get('session')->getFlashBag()->add(
                    'success', "Activity has been updated."
            );
            return $this->redirect($this->generateUrl('activity_show', array('id' => $activity->getId())));
        }
        return array(
            'form' => $form->createView(),
            'activity' => $activity
        );
    }

    /**
     * @Route("/delete/{id}", name="activity_delete")
     * 
     */
    public function deleteAction($id) {

        $activity = $this->getActivityStorage()->findOneBy(array('id' => $id));

        if (!$activity) {
            throw $this->createNotFoundException('Unable to find entity.');
        }

        $this->getActivityStorage()->delete($activity);

        return new RedirectResponse($this->get('request')->server->get('HTTP_REFERER'));
    }

    protected function getActivityStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\TimeTrackerBundle\Entity\Activity');
    }

    protected function getTimesliceStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\TimeTrackerBundle\Entity\Timeslice');
    }

}
