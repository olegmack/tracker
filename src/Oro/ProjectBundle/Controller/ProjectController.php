<?php

namespace Oro\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Oro\ProjectBundle\Entity\Project;

/**
 * Project controller.
 */
class ProjectController extends Controller
{
    /**
     * Lists all Project entities.
     *
     * @Route("/", name="project")
     * @Method("GET")
     * @Template()
     *
     * @return array()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities =
            (false === $this->get('security.context')->isGranted('VIEW_LIST', 'Oro\ProjectBundle\Entity\Project'))
            ? $em->getRepository('OroProjectBundle:Project')->findByMember($this->getUser()->getId())
            : $em->getRepository('OroProjectBundle:Project')->findAll();

        return array(
            'entities' => $entities,
        );
    }

    /**
     * Creates a new Project entity.
     *
     * @Route("/create", name="project_create")
     * @Template()
     *
     * @param Request $request
     * @return array()
     */
    public function createAction(Request $request)
    {
        $entity = new Project();

        if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.project.messages.access_denied'));
        }

        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashbag()
                ->add('success', $this->get('translator')->trans('oro.project.messages.project_added'));

            return $this->redirect($this->generateUrl('project_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Creates a form to create a Project entity.
     *
     * @param Project $entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm('oro_projectbundle_project', $entity);

        return $form;
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/view/{id}", name="project_show")
     * @ParamConverter("entity", class="OroProjectBundle:Project")
     * @Template()
     *
     * @param Project $entity
     * @return array
     */
    public function showAction(Project $entity)
    {
        if (false === $this->get('security.context')->isGranted('VIEW', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.project.messages.access_denied'));
        }

        $deleteForm = $this->createDeleteForm($entity);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
    * Creates a form to edit a Project entity.
    *
    * @param Project $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(Project $entity)
    {
        $form = $this->createForm('oro_projectbundle_project', $entity);

        return $form;
    }

    /**
     * Edits an existing Project entity.
     *
     * @Route("/update/{id}", name="project_update")
     * @ParamConverter("entity", class="OroProjectBundle:Project")
     * @Template()
     *
     * @param Project $entity
     * @param Request $request
     * @return array
     */
    public function updateAction(Project $entity, Request $request)
    {
        if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.project.messages.access_denied'));
        }

        $deleteForm = $this->createDeleteForm($entity);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $request->getSession()->getFlashbag()
                ->add('success', $this->get('translator')->trans('oro.project.messages.project_updated'));

            return $this->redirect($this->generateUrl('project_show', array('id' => $entity->getId())));
        }

        return array(
            'entity'      => $entity,
            'form'        => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a Project entity.
     *
     * @Route("/delete/{id}", name="project_delete")
     * @ParamConverter("entity", class="OroProjectBundle:Project")
     * @Method("DELETE")
     *
     * @param Project $entity
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Project $entity, Request $request)
    {
        $form = $this->createDeleteForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
                throw new AccessDeniedException($this->get('translator')->trans('oro.project.messages.access_denied'));
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($entity);
            $em->flush();

            $request->getSession()->getFlashbag()
                ->add('success', $this->get('translator')->trans('oro.project.messages.project_deleted'));
        }

        return $this->redirect($this->generateUrl('project'));
    }

    /**
     * Creates a form to delete a Project entity by id.
     *
     * @param Project $entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Project $entity)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_delete', array('id' => $entity->getId())))
            ->setMethod('DELETE')
            ->add(
                'submit',
                'submit',
                array(
                    'label' => $this->get('translator')->trans('oro.project.delete_label'),
                    'attr' => array('class' => 'btn btn-danger')
                )
            )
            ->getForm();
    }
}
