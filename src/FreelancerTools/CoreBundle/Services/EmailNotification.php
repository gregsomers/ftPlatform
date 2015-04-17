<?PHP

namespace FreelancerTools\CoreBundle\Services;

use Doctrine\ORM\EntityManager;

class EmailNotification {

    private $templating;
    private $storage;
    private $securityContext;
    private $encryption;
    private $transformer;
    private $twig;

    /**
     * @param \Doctrine\Common\Persistence\ObjectManager $objectManager
     */
    public function __construct($templating, $storage, $securityContext, $encryption, $transformer, $twig) {
        $this->templating = $templating;
        $this->storage = $storage;
        $this->securityContext = $securityContext;
        $this->encryption = $encryption;
        $this->transformer = $transformer;
        $this->twig = $twig;
    }

    public function send($data, $object, $templateName, $attachedFile = null) { 

        $message = \Swift_Message::newInstance()
                ->setSubject($data['subject'])
                ->setFrom($data['from'])
                ->setTo($data['to'])
                ->setCharset('UTF-8')
                ->setContentType('text/html')
                ->setBody(
                $this->templating->render(
                        'FreelancerToolsInvoicingBundle:Invoice:email.html.twig', array('body' => $this->renderEmailTemplate($object, $templateName))
                )
                )
        ;
        if($attachedFile) {
            $message->attach(\Swift_Attachment::fromPath($attachedFile));
        }
        if(isset($data['cc'])) {            
            $message->setCc($data['cc']);        
        } else {
            //$message->setCc($this->securityContext->getToken()->getUser()->getEmail());  
        }
        
        if(isset($data['bcc'])) {            
            $message->setBcc($data['bcc']);  
        }

        $mailer = \Swift_Mailer::newInstance($this->getTransport());
        $mailer->send($message);
    }

    protected function renderEmailTemplate($object, $templateName) {
        $template = $this->getTemplateStorage()->findOneBy(array('name' => $templateName, 'user' => $this->securityContext->getToken()->getUser()))->getBody();
        $this->transformer->transform($template);

        $twig = clone $this->twig;
        $twig->setLoader(new \Twig_Loader_String());
        return $twig->render($template, $object);
    }

    protected function getTransport() {
        $serverSettings = array();
        foreach ($this->getSettingStorage()->findBy(array('namespace' => 'email', 'user' => $this->securityContext->getToken()->getUser())) as $setting) {
            $serverSettings[$setting->getName()] = $setting->getValue();
        }
        $password = $this->encryption->decrypt($serverSettings['password']);

        $transport = \Swift_SmtpTransport::newInstance();
        $transport->setUsername($serverSettings['user'])
                ->setPassword($password)
                ->setHost($serverSettings['host'])
                ->setPort($serverSettings['port'])
                ->setEncryption($serverSettings['security'])
        ;
        return $transport;
    }

    protected function getSettingStorage() {
        return $this->storage->getStorage('FreelancerTools\CoreBundle\Entity\Setting');
    }

    protected function getTemplateStorage() {
        return $this->storage->getStorage('FreelancerTools\CoreBundle\Entity\EmailTemplate');
    }

}
