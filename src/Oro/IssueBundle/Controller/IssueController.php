<?php

namespace Oro\IssueBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\IssueBundle\Entity\Issue;
use Oro\IssueBundle\Entity\IssueType;
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
     * @Route("/create/{project}/{parent}", name="issue_create", defaults={"parent" = 0})
     * @Template()
     */
    public function createAction($project, $parent, Request $request)
    {
        $project = $this->getProject($project);

        //load project
        if (empty($project) || !($project instanceof Project)) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('oro.issue.messages.project_not_found')
            );
        }

        //check for project access
        if (false === $this->get('security.context')->isGranted('VIEW', $project)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.project.messages.access_denied'));
        }

        $entity = new Issue();
        $entity->setProject($project);
        $entity->setAssignee($this->getUser());

        //init parent issue
        if ($parent > 0) {
            $em = $this->getDoctrine()->getManager();
            $parentIssue = $em->getRepository('OroIssueBundle:Issue')->findParentStory($parent);

            if (!empty($parentIssue)) {
                $subtaskType = $em->getRepository('OroIssueBundle:IssueType')->find(IssueType::TYPE_SUBTASK);
                $entity->setIssueType($subtaskType);
                $entity->setParent($parentIssue);
            } else {
                throw new AccessDeniedException(
                    $this->get('translator')->trans('oro.issue.messages.incorrect_parent_issue')
                );
            }
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            #validate task-sub-task relation

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
        $form = $this->createForm(
            'oro_issuebundle_issue',
            $entity,
            array(
                'method' => 'POST',
            )
        );

        return $form;
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
    * Creates a form to edit a Issue entity.
    *
    * @param Issue $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Issue $entity)
    {
        $form = $this->createForm(
            'oro_issuebundle_issue',
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
     * @Template()
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
     * Load project
     *
     * @param $projectId
     * @return Project
     */
    protected function getProject($projectId)
    {
        return $this->getDoctrine()->getManager()->getRepository('OroProjectBundle:Project')->findOneById($projectId);
    }
}
