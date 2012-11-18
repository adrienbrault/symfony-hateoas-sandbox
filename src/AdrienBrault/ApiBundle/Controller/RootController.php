<?php

namespace AdrienBrault\ApiBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use AdrienBrault\ApiBundle\Model\Root;

class RootController extends FOSRestController
{
    /**
     * @Method("GET")
     * @Route("/", name = "api_root")
     */
    public function indexAction()
    {
        return $this->view(new Root());
    }
}
