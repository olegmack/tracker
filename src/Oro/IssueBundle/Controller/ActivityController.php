<?php

namespace Oro\IssueBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\IssueBundle\Entity\IssueActivity;

/**
 * Activity controller.
 *
 * @Route("/activity")
 */
class ActivityController extends Controller
{
    /**
     * Provide list of activities
     *
     * @param int $limit
     * @param int $userId
     * @param int $projectId
     * @param int $issueId
     * @return array
     *
     * @Template()
     */
    public function listAction($limit = 20, $userId = null, $projectId = null, $issueId = null)
    {
        $activities = $this->getDoctrine()->getManager()
            ->getRepository('OroIssueBundle:IssueActivity')->findByParameters($limit, $userId, $projectId, $issueId);

        return array(
            'activities' => $activities
        );
    }

}