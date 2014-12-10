<?php

namespace Oro\IssueBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\IssueBundle\Entity\Comment;
use Oro\IssueBundle\Form\CommentType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Issue controller.
 *
 * @Route("/comment")
 */
class CommentController extends Controller
{
    /**
     * Add comment
     *
     * @Template("OroIssueBundle:Comment:new.html.twig")
     *
     * @param $issueId
     * @return array
     */
    public function newAction($issueId)
    {
        $em = $this->getDoctrine()->getManager();
        $issue = $em->getRepository('OroIssueBundle:Issue')->find($issueId);
        if (!$issue) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.issue.messages.issue_not_found'));
        }

        $comment = new Comment();
        $comment->setIssue($issue);

        if (false === $this->get('security.context')->isGranted('CREATE', $comment)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.comment.messages.access_denied'));
        }

        $form = $this->createForm(new CommentType(), $comment);
        return array(
            'form' => $form->createView(),
            'issue_id' => $issueId
        );
    }

    /**
     * Creates a new Comment entity.
     *
     * @Route("/create/{issueId}", name="comment_create")
     * @Method("POST")
     *
     * @param $issueId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction($issueId)
    {
        $em = $this->getDoctrine()->getManager();
        $issue = $em->getRepository('OroIssueBundle:Issue')->find($issueId);
        if (!$issue) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.issue.messages.issue_not_found'));
        }
        $comment = new Comment();
        $comment->setIssue($issue);

        $form = $this->createForm(new CommentType(), $comment);
        $form->handleRequest($this->getRequest());

        if (false === $this->get('security.context')->isGranted('CREATE', $comment)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.comment.messages.access_denied'));
        }

        if ($form->isValid()) {
            $comment->setAuthor($this->getUser());

            $em->persist($comment);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('issue_show', array('id' => $issue->getId())));
    }

    /**
     * Displays a form to edit an existing comment entity.
     *
     * @Route("/edit/{id}", name="comment_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroIssueBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.comment.messages.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.comment.messages.access_denied'));
        }

        $editForm = $this->createForm(new CommentType(), $entity);

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
        );
    }

    /**
     * Edits an existing comment entity.
     *
     * @Route("/update/{id}", name="comment_update")
     * @Method("POST")
     * @Template("OroIssueBundle:Comment:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroIssueBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.comment.messages.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.comment.messages.access_denied'));
        }

        $editForm = $this->createForm(new CommentType(), $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();
            return $this->redirect($this->generateUrl('issue_show', array('id' => $entity->getIssue()->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView()
        );
    }

    /**
     * Delete an existing comment entity.
     *
     * @Route("/delete/{id}", name="comment_delete")
     * @Method("GET")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('OroIssueBundle:Comment')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.comment.messages.not_found'));
        }

        if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.comment.messages.access_denied'));
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('issue_show', array('id' => $entity->getIssue()->getId())));
    }

}