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
        $activities = $this->getDoctrine()->getManager()
            ->getRepository('OroIssueBundle:IssueActivity')->findByProjectMember($this->getUser()->getId());

        $issues =  $this->getDoctrine()->getManager()
            ->getRepository('OroIssueBundle:Issue')->findByCollaborator($this->getUser()->getId());


        return array(
            'activities' => $activities,
            'issues' => $issues
        );
    }
}