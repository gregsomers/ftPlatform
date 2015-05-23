<?php

namespace FreelancerTools\ReportBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use FreelancerTools\ReportBundle\Form\ReportType;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\ReportBundle\Entity\Report;

class ReportController extends Controller {

    /**
     * @Route("/report", name="report")
     * @Template()
     */
    public function indexAction() {

        $form = $this->createForm(new ReportType(), new Report());

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/report/generate", name="report_generate")
     * @Template("FreelancerToolsReportBundle:Report:index.html.twig")
     */
    public function generateAction(Request $request) {
        $report = new Report();
        $form = $this->createForm(new ReportType(), $report);
        $form->bind($request);

        if ($form->isValid()) {
            /* $customer = $form->get('customer')->getData();
              $project = $form->get('project')->getData();
              $start = $form->get('start')->getData();
              $end = $form->get('end')->getData(); */

            //$report = new Report();
            $report->setUser($this->getUser());
            /*
              $report->setCustomer($customer);
              $report->setProject($project);
              $report->setStart($start);
              $report->setEnd($end); */
            $this->getDoctrine()->getManager()->persist($report);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirect($this->generateUrl('report_show', array("id" => $report->getId())));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/report/invoice/{id}", name="report_show_invoice")
     * @Template("FreelancerToolsReportBundle:Report:guest.html.twig")
     */
    public function showInvoiceAction($id) {
        $invoice = $this->getInvoiceRepository()->findOneById($id);
        $itemIds = array();
        foreach($invoice->getItems() as $item) {
            $itemIds[] = $item->getId();
        }

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb
                ->select('a,s')
                ->from('FreelancerToolsTimeTrackerBundle:Activity', 'a')
                ->leftJoin('a.timeslices', 's')
                ->andWhere('s.invoice = :invoice OR s.invoiceItem IN(:itemIds)')
                ->setParameters(array(
                    ':invoice' => $invoice->getId(),
                    ':itemIds' => array_values($itemIds)
                ))
        ;

        $q = $qb->getQuery();
        $activities = $q->execute();
        
        return array(
            'invoice' => $invoice,
            'activities' => $activities,
            //'report' => $report
        );
    }
    
    /**
     * @Route("/guest/report/invoice/{token}", name="report_guest_show_invoice")
     * @Template("FreelancerToolsReportBundle:Report:guest.html.twig")
     */
    public function guestShowInvoiceAction($token) {
        
        $invoice = $this->getInvoiceRepository()->findOneByToken($token);
        
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb
                ->select('a,s')
                ->from('FreelancerToolsTimeTrackerBundle:Activity', 'a')
                ->leftJoin('a.timeslices', 's')
                ->andWhere('s.invoice = :invoice')
                ->setParameters(array(
                    ':invoice' => $invoice->getId()
                ))
        ;

        $q = $qb->getQuery();
        $activities = $q->execute();
        
        return array(
            'activities' => $activities,
            'invoice' => $invoice,
        );
        
    }

    /**
     * @Route("/report/{id}", name="report_show")
     * @Template()
     */
    public function showAction($id) {

        $repo = $this->getDoctrine()->getRepository('FreelancerToolsReportBundle:Report');
        $report = $repo->findOneById($id);

        return($this->getShowContent($report));
    }

    /**
     * @Route("/report/{id}/markInvoiced", name="report_mark_invoiced")
     * @Template()
     */
    public function markInvoicedAction($id) {

        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository('FreelancerToolsReportBundle:Report');
        $report = $repo->findOneById($id);

        $ret = $this->getShowContent($report);
        foreach ($ret['activities'] as $activity) {
            foreach ($activity->getTimeslices() as $slice) {
                $slice->setInvoiced(true);
                $slice->setInvoicedAt("now");
                $em->persist($slice);
            }
        }

        //change this report to show the invoiced slices
        $report->setInvoiced(true);
        $em->persist($report);

        $em->flush();
        return $this->redirect($this->generateUrl('report_show', array("id" => $report->getId())));
    }

    /**
     * @Route("/guest/report/{token}", name="report_guest_show")
     * @Template("FreelancerToolsReportBundle:Report:guest.html.twig")
     */
    public function guestAction($token) {

        $repo = $this->getDoctrine()->getRepository('FreelancerToolsReportBundle:Report');
        $report = $repo->findOneByToken($token);

        return($this->getShowContent($report));
    }

    private function getShowContent($report) {
        $parameters = array();

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb
                ->select('a,s')
                ->from('FreelancerToolsTimeTrackerBundle:Activity', 'a')
                ->leftJoin('a.timeslices', 's')
        ;

        if ($report->getCustomer()) {
            $parameters[':customer'] = $report->getCustomer();
            $qb->andWhere('a.customer = :customer');
        }
        if ($report->getProjects()) {
            $ids = array();
            foreach ($report->getProjects() as $p) {
                $ids[] = $p->getId();
            }
            $parameters[':projects'] = $ids;
            $qb->andWhere('a.project IN (:projects)');
        }
        if ($report->getStart()) {
            $parameters[':startedAt'] = $report->getStart();
            $qb->andWhere('s.startedAt > :startedAt');
        }
        if ($report->getEnd()) {
            $parameters[':stoppedAt'] = $report->getEnd();
            $qb->andWhere('s.stoppedAt < :stoppedAt');
        }

        $parameters[':invoiced'] = $report->getInvoiced();
        $qb->andWhere('s.invoiced = :invoiced');


        $qb->setParameters($parameters);
        $q = $qb->getQuery();
        $activities = $q->execute();

        return array(
            'activities' => $activities,
            'report' => $report
        );
    }
    
    
    protected function getInvoiceRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsInvoicingBundle:Invoice');
    }

}
