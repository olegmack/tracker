<?php

namespace Oro\ProjectBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Oro\ProjectBundle\Entity\Project;
use Oro\ProjectBundle\Form\ProjectType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = (false === $this->get('security.context')->isGranted('VIEW_LIST', new Project()))
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
     * @Method("POST")
     * @Template("OroProjectBundle:Project:new.html.twig")
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
     * @param Project $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(Project $entity)
    {
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('project_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new Project entity.
     *
     * @Route("/new", name="project_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new Project();

        if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.project.messages.access_denied'));
        }

        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a Project entity.
     *
     * @Route("/view/{id}", name="project_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroProjectBundle:Project')->find($id);

        if (false === $this->get('security.context')->isGranted('VIEW', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.project.messages.access_denied'));
        }

        if (!$entity) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('oro.project.messages.project_not_found')
            );
        }

        $this->setLastVisitedProject($entity);

        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Displays a form to edit an existing Project entity.
     *
     * @Route("/edit/{id}", name="project_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroProjectBundle:Project')->find($id);

        if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.project.messages.access_denied'));
        }

        if (!$entity) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('oro.project.messages.project_not_found')
            );
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
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
        $form = $this->createForm(new ProjectType(), $entity, array(
            'action' => $this->generateUrl('project_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        return $form;
    }
    /**
     * Edits an existing Project entity.
     *
     * @Route("/update/{id}", name="project_update")
     * @Method("POST")
     * @Template("OroProjectBundle:Project:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroProjectBundle:Project')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException(
                $this->get('translator')->trans('oro.project.messages.project_not_found')
            );
        }

        if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.project.messages.access_denied'));
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            $request->getSession()->getFlashbag()
                ->add('success', 'Project information is updated');

            return $this->redirect($this->generateUrl('project_show', array('id' => $id)));
        }

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        );
    }
    /**
     * Deletes a Project entity.
     *
     * @Route("/delete/{id}", name="project_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('OroProjectBundle:Project')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException(
                    $this->get('translator')->trans('oro.project.messages.project_not_found')
                );
            }

            if (false === $this->get('security.context')->isGranted('MODIFY', $entity)) {
                throw new AccessDeniedException($this->get('translator')->trans('oro.project.messages.access_denied'));
            }

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
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('project_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete', 'attr' => array('class' => 'btn btn-danger')))
            ->getForm();
    }

    /**
     * Store last visited project id in session
     *
     * @param Project $project
     * @return $this
     */
    protected function setLastVisitedProject($project)
    {
        $this->get('session')->set('last_visited_project_id', $project->getId());
        return $this;
    }
}
