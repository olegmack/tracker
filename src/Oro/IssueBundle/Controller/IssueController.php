<?php

namespace Oro\IssueBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Form\IssueType;
use Oro\ProjectBundle\Entity\Project;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Issue controller.
 *
 */
class IssueController extends Controller
{
    /**
     * Creates a new Issue entity.
     *
     * @Route("/create", name="issue_create")
     * @Method("POST")
     * @Template("OroIssueBundle:Issue:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new Issue();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if (false === $this->get('security.context')->isGranted('ACCESS', $entity)) {
                throw new AccessDeniedException($this->get('translator')->trans('oro.issue.messages.access_denied'));
            }

            $currentUser =  $this->getUser();
            $entity->setReporter($currentUser);
            $entity->addCollaborator($currentUser);
            $entity->addCollaborator($entity->getAssignee());

            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashbag()
                ->add('success', $this->get('translator')->trans('oro.issue.messages.issue_created'));

            return $this->redirect($this->generateUrl('issue_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Issue entity.
     *
     * @param Issue $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Issue $entity)
    {
        $project = $this->getLatestProject();
        $currentProject = $entity->getProject();
        if (empty($currentProject) && !empty($project)) {
            $entity->setProject($project);
        }

        $form = $this->createForm(
            new IssueType(
                $this->getDoctrine()->getManager(),
                $this->getUser(),
                $this->getAvailableProjects()
            ),
            $entity,
            array(
                'action' => $this->generateUrl('issue_create'),
                'method' => 'POST',
            )
        );

        return $form;
    }

    /**
     * Displays a form to create a new Issue entity.
     *
     * @Route("/new/{parent}", name="issue_new", defaults={"parent" = 0})
     * @Method("GET")
     * @Template()
     *
     * @param int parent id
     * @return array
     */
    public function newAction($parent)
    {
        $entity = new Issue();

        if ($parent > 0) {
            $em = $this->getDoctrine()->getManager();
            $parentIssue = $em->getRepository('OroIssueBundle:Issue')->find($parent);

            if (!empty($parentIssue)) {
                $issueType = $parentIssue->getIssueType()->getCode();

                if ($issueType == 'story') {
                    $subtaskIssueType = $em->getRepository('OroIssueBundle:IssueType')->find('subtask');
                    $entity->setIssueType($subtaskIssueType);
                    $entity->setParent($parentIssue);
                }
            }
        }

        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Issue entity.
     *
     * @Route("/view/{id}", name="issue_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroIssueBundle:Issue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.issue.messages.issue_not_found'));
        }

        if (false === $this->get('security.context')->isGranted('ACCESS', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.issue.messages.access_denied'));
        }

        return array(
            'entity'      => $entity,
        );
    }

    /**
     * Displays a form to edit an existing Issue entity.
     *
     * @Route("/edit/{id}", name="issue_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroIssueBundle:Issue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.issue.messages.issue_not_found'));
        }

        if (false === $this->get('security.context')->isGranted('ACCESS', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.issue.messages.access_denied'));
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Issue entity.
    *
    * @param Issue $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Issue $entity)
    {
        $form = $this->createForm(
            new IssueType(
                $this->getDoctrine()->getManager(),
                $this->getUser(),
                $this->getAvailableProjects()
            ),
            $entity,
            array(
                'action' => $this->generateUrl('issue_update', array('id' => $entity->getId())),
                'method' => 'POST',
            )
        );

        return $form;
    }

    /**
     * Edits an existing Issue entity.
     *
     * @Route("/update/{id}", name="issue_update")
     * @Method("POST")
     * @Template("OroIssueBundle:Issue:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroIssueBundle:Issue')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.issue.messages.issue_not_found'));
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if (false === $this->get('security.context')->isGranted('ACCESS', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.issue.messages.access_denied'));
        }

        if ($editForm->isValid()) {
            $em->flush();

            $request->getSession()->getFlashbag()
                ->add('success', $this->get('translator')->trans('oro.issue.messages.issue_updated'));

            return $this->redirect($this->generateUrl('issue_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView()
        );
    }

    /**
     * Get last visited project
     */
    public function getLatestProject()
    {
        $projectId = $this->get('session')->get('last_visited_project_id');
        if (empty($projectId)) {
            return false;
        }

        $project = $this->getDoctrine()->getManager()->getRepository('OroProjectBundle:Project')->find($projectId);
        return $project;
    }

    /**
     * Get list of available projects based on user role
     * @return mixed
     */
    protected function getAvailableProjects()
    {
        $em = $this->getDoctrine()->getManager();
        $projects = (false === $this->get('security.context')->isGranted('VIEW_LIST', new Project()))
            ? $em->getRepository('OroProjectBundle:Project')->findByMember($this->getUser()->getId())
            : $em->getRepository('OroProjectBundle:Project')->findAll();

        return $projects;
    }
}
