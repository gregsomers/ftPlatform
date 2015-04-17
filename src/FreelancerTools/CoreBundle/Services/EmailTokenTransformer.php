<?PHP

namespace FreelancerTools\CoreBundle\Services;

class EmailTokenTransformer {

    private $tokens = array(
        //invoice
        "{{{invoice.customer.name}}}" => "{{invoice.customer.name}}",
        "{{{invoice.duedate}}}" => "{{invoice.invoiceDueDate|date('d-M-Y')}}",
        "{{{invoice.guesturl}}}" => "{{ app.request.getSchemeAndHttpHost()}}{{path('invoice_guest', {'token':invoice.token})}}",
        "{{{invoice.status}}}" => "{{invoice.statusString}}",
        "{{{invoice.invoiceDate}}}" => " {{invoice.invoiceDate|date('d-M-Y')}",
        "{{{invoice.number}}}" => "{{invoice.invoiceNumber}}",
        "{{{invoice.balance}}}" => "{{invoice.balance|number_format(2, '.', ',')}}", 
        "{{{invoice.daysUntilDue}}}" => "{{invoice.getDaysUntilDue}}",
        //payment
        "{{{payment.amount}}}" => "{{payment.amount|number_format(2, '.', ',')}}",
        
    );    

    public function transform(&$text) {  
        $text = str_replace(array_keys($this->tokens), $this->tokens, $text);        
    }

}
