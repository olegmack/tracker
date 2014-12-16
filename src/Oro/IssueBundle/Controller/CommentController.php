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
     * Creates a new Comment entity.
     *
     * @Route("/create/{issueId}", name="comment_create")
     * @Template("OroIssueBundle:Comment:create.html.twig")
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

        $form = $this->createForm('oro_issuebundle_comment', $comment);
        $form->handleRequest($this->getRequest());

        if (false === $this->get('security.context')->isGranted('CREATE', $comment)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.comment.messages.access_denied'));
        }

        if ($form->isValid()) {
            $comment->setAuthor($this->getUser());

            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('id' => $issue->getId())));
        }

        return array(
            'form' => $form->createView(),
            'issue_id' => $issueId
        );
    }

    /**
     * Edits an existing comment entity.
     *
     * @Route("/update/{id}", name="comment_update")
     * @Template()
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

        $editForm = $this->createForm('oro_issuebundle_comment', $entity);
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
