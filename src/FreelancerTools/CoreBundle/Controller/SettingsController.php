<?php

namespace FreelancerTools\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use FreelancerTools\CoreBundle\Form\SettingsType;
use FreelancerTools\CoreBundle\Entity\Customer;
use FreelancerTools\CoreBundle\Entity\Setting;

/**
 * @Route("/settings")
 * 
 */
class SettingsController extends Controller {

    /**
     * @Route("/", name="settings")
     * @Template()
     */
    public function indexAction() {

        foreach ($this->getSettingRepository()->findAll() as $setting) {
            $settings[$setting->getNamespace() . "_" . $setting->getName()] = $setting;
        }

        $form = $this->createForm(new SettingsType($settings));

        return array(
            'form' => $form,
        );
    }

    /**
     * @Route("/update", name="settings_update")
     * @Template("FreelancerToolsCoreBundle:Settings:index.html.twig")
     */
    public function updateAction(Request $request) {
        
        $em = $this->getDoctrine()->getManager();

        foreach ($this->getSettingRepository()->findAll() as $setting) {
            $settings[$setting->getNamespace() . "_" . $setting->getName()] = $setting;
        }

        $form = $this->createForm(new SettingsType($settings))->bind($request);
        
        $data = $form->all();
        if($data['email_password_blank']->getData()) {
            //password change requested
            $password = $this->get('ft.encryption')->encrypt($data['email_password_blank']->getData());
            //$password = $this->encryptPassword($data['email_password_blank']->getData());
            $data['email_password']->getData()->setValue($password);            
        } else {
            unset($data['email_password']);
        }
        
        //print_r(get_class_methods($data));
        foreach($data as $setting) {
            if($setting instanceof Setting) {
                $em->persist($setting->getData());
            }
            //print_r(get_class_methods($setting));
            //echo get_class($setting->getData());
            //echo "<br/>";
            //$em->persist($setting->getData());
            //echo $setting->getName();
        }
        $em->flush();

        return array(
            'form' => $form,
        );
    }

    private function encryptPassword($plaintext) {

        # --- ENCRYPTION ---
        # the key should be random binary, use scrypt, bcrypt or PBKDF2 to
        # convert a string into a key
        # key is specified using hexadecimal 
        $key = pack('H*', hash_hmac('ripemd160', $this->container->getParameter('secret'), 'secret'));

        # show key size use either 16, 24 or 32 byte keys for AES-128, 192
        # and 256 respectively
        $key_size = strlen($key);
        //echo "Key size: " . $key_size . "\n";

        //$plaintext = "pwrzyfvmmmbtkzvb";

        # create a random IV to use with CBC encoding
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

        # creates a cipher text compatible with AES (Rijndael block size = 128)
        # to keep the text confidential 
        # only suitable for encoded input that never ends with value 00h
        # (because of default zero padding)
        $ciphertext = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plaintext, MCRYPT_MODE_CBC, $iv);

        # prepend the IV for it to be available for decryption
        $ciphertext = $iv . $ciphertext;

        # encode the resulting cipher text so it can be represented by a string
        $ciphertext_base64 = base64_encode($ciphertext);
        
        return $ciphertext_base64;

        //echo $ciphertext_base64 . "\n";

        # === WARNING ===
        # Resulting cipher text has no integrity or authenticity added
        # and is not protected against padding oracle attacks.
        # --- DECRYPTION ---

        $ciphertext_dec = base64_decode('ZtEk8KVau8MYq8Ner/w9zHO7HngPDfFmqN+8PPtDfk4=');

        # retrieves the IV, iv_size should be created using mcrypt_get_iv_size()
        $iv_dec = substr($ciphertext_dec, 0, $iv_size);

        # retrieves the cipher text (everything except the $iv_size in the front)
        $ciphertext_dec = substr($ciphertext_dec, $iv_size);

        # may remove 00h valued characters from end of plain text
        $plaintext_dec = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $ciphertext_dec, MCRYPT_MODE_CBC, $iv_dec);

        //echo $plaintext_dec . "\n";
    }

    public function getSettingRepository() {
        return $this->getDoctrine()->getRepository('FreelancerToolsCoreBundle:Setting');
    }

}
