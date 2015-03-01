<?php

namespace FreelancerTools\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class FreelancerToolsCoreBundle extends Bundle {

    public function getParent() {
        return 'FOSUserBundle';
    }

}
