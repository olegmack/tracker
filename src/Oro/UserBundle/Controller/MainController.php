<?php

namespace Oro\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


class MainController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     * @Template
     */
    public function dashboardAction()
    {
        return array();
    }
}