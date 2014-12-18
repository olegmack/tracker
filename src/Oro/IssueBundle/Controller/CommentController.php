<?php

namespace Oro\IssueBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;

use Oro\IssueBundle\Entity\Comment;
use Oro\IssueBundle\Entity\Issue;

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
     * @ParamConverter("issue", class="OroIssueBundle:Issue", options={"id" = "issueId"})
     * @Template()
     *
     * @param Issue $issue
     * @return RedirectResponse
     */
    public function createAction(Issue $issue)
    {
        $comment = new Comment();
        $comment->setIssue($issue);

        $form = $this->createForm('oro_issuebundle_comment', $comment);
        $form->handleRequest($this->getRequest());

        if (false === $this->get('security.context')->isGranted('CREATE', $comment)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.comment.messages.access_denied'));
        }

        if ($form->isValid()) {
            $comment->setAuthor($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($comment);
            $em->flush();

            return $this->redirect($this->generateUrl('issue_show', array('id' => $issue->getId())));
        }

        return array(
            'form' => $form->createView(),
            'issue_id' => $issue->getId()
        );
    }

    /**
     * Edits an existing comment entity.
     *
     * @Route("/update/{id}", name="comment_update")
     * @ParamConverter("entity", class="OroIssueBundle:Comment")
     * @Template()
     *
     * @param Comment $entity
     * @param Request $request
     * @return array
     */
    public function updateAction(Comment $entity, Request $request)
    {
        if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.comment.messages.access_denied'));
        }

        $editForm = $this->createForm('oro_issuebundle_comment', $entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
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
     * @ParamConverter("entity", class="OroIssueBundle:Comment")
     *
     * @param Comment $entity
     * @return RedirectResponse
     */
    public function deleteAction(Comment $entity)
    {
        if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.comment.messages.access_denied'));
        }

        $em = $this->getDoctrine()->getManager();
        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('issue_show', array('id' => $entity->getIssue()->getId())));
    }
}
