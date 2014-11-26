<?php

namespace Oro\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class UserController extends Controller
{
    /**
     * @Route("/", name="dashboard")
     * @Template
     */
    public function dashboardAction()
    {
        return array();
    }

    /**
     * @Route("/user/list", name="user_list")
     * @Template
     */
    public function listAction()
    {
        return array();
    }

    /**
     * @Route("/user/new", name="user_new")
     * @Template
     */
    public function newAction()
    {
        return array();
    }


}
