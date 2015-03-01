<?php

namespace FreelancerTools\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\CoreBundle\Form\CustomerType;
use FreelancerTools\CoreBundle\Entity\Customer;

/**
 * @Route("/")
 * 
 */
class DashboardController extends Controller {

    /**
     * @Route("/", name="dashboard")
     * @Template()
     */
    public function indexAction() {
        $customers = $this->getCustomerRepository()->findAll();
        $payments = $this->getPaymentsStorage()->getRepo()->getPaymentsByYear($this->getUser());

        $paidInvoicesAmount = 0;
        foreach ($payments as $paid) {
            $paidInvoicesAmount += $paid->getAmount();
        }

        $invoices = $this->getInvoiceStorage()->getRepo()->getReceivableInvoicesByYear($this->getUser());

        $accountsReceivable = 0;
        foreach ($invoices as $inv) {
            $accountsReceivable += $inv->getBalance();
        }

        $projects = $this->getProjectStorage()->findBy(array('user' => $this->getUser(), 'active' => true));
        $totalUnbilled = 0;
        $unbilledCost = 0;
        foreach ($projects as $project) {
            $totalUnbilled += $project->getUnbilledTime(true);
            $unbilledCost += $project->getUnbilledCost();
        }

        //echo $this->convertToTimeSting($totalUnbilled) . "<br/>";
        //echo $unbilledCost . "<br/>";


        return array(
            'accountsReceivable' => $accountsReceivable,
            'paidInvoicesAmount' => $paidInvoicesAmount,
            'customers' => $customers,
            'unbilledTime' => $this->convertToTimeSting($totalUnbilled),
            'unbilledCost' => $unbilledCost
        );
    }

    private function convertToTimeSting($seconds) {
        $hours = floor($seconds / 3600);
        $mins = floor(($seconds - ($hours * 3600)) / 60);
        $secs = floor($seconds % 60);

        return sprintf("%02d", $hours) . ":" . sprintf("%02d", $mins) . ":" . sprintf("%02d", $secs);
    }

    /**
     * get customer repository
     *
     * @return CustomerRepository
     */
    protected function getCustomerRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsCoreBundle:Customer');
    }
    
    protected function getTimesliceStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\TimeTrackerBundle\Entity\Timeslice');
    }  

    protected function getPaymentsStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\PaymentBundle\Entity\Payment');
    }

    protected function getInvoiceStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\InvoicingBundle\Entity\Invoice');
    }

    protected function getProjectStorage() {
        return $this->get('ft.storage')->getStorage('FreelancerTools\TimeTrackerBundle\Entity\Project');
    }

}
