<?php

namespace Oro\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('OroProjectBundle:Default:index.html.twig', array('name' => $name));
    }
}
