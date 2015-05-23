<?PHP

namespace FreelancerTools\CoreBundle\Services;
use \mPDF;


class PDFCreator {

    private $templating;   

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function __construct($templating) {
        $this->templating = $templating;        
    }

   public function generateInvoicePDF($invoice, $user, $mode = 'I') {

        $mpdf = new mPDF();

        $parameters = array(
            'invoice' => $invoice,
            'user' => $user
        );

        $templateFile = "FreelancerToolsInvoicingBundle:Invoice:pdf.html.twig";
        $html = $this->templating->render($templateFile, $parameters);

        $mpdf->WriteHTML($html);

        if ($mode == 'F') {
            $mpdf->Output('/tmp/Invoice-' . $invoice->getInvoiceNumber() . '.pdf', $mode);
            return true;
        } else {
            $mpdf->Output('Invoice-' . $invoice->getInvoiceNumber() . '.pdf', $mode);
        }
        exit();
    }
    
    public function generatePaymentPDF ($payment, $user, $mode = 'I') {
        
        $mpdf = new mPDF();
        
        $parameters = array(
            'payment' => $payment,
            'user' => $user
        );
        
        $templateFile = "FreelancerToolsPaymentBundle:Payment:pdf.html.twig";
        $html = $this->templating->render($templateFile, $parameters);  
        
        $mpdf->WriteHTML($html);

        if ($mode == 'F') {
            $mpdf->Output('/tmp/payment-' . $payment->getInvoice()->getInvoiceNumber(). "-" . $payment->getId() . '.pdf', $mode);
            return '/tmp/payment-' . $payment->getInvoice()->getInvoiceNumber(). "-" . $payment->getId() . '.pdf';
        } else {
            $mpdf->Output('payment-' .$payment->getInvoice()->getInvoiceNumber(). "-" . $payment->getId() . '.pdf', $mode);
        }
        exit();
        
    }

}
