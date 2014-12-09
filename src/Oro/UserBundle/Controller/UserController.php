<?php

namespace Oro\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Oro\UserBundle\Entity\User;
use Oro\UserBundle\Form\UserType;

/**
 * User controller.
 *
 * @Route("/user")
 */
class UserController extends Controller
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="user")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('OroUserBundle:User')->findAll();

        return array(
            'entities' => $entities,
        );
    }
    /**
     * Creates a new User entity.
     *
     * @Route("/", name="user_create")
     * @Method("POST")
     * @Template("OroUserBundle:User:new.html.twig")
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $entity->avatarUpload();

            //store password
            $plainPassword = $form->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $entity->setPassword($this->encodePassword($entity, $plainPassword));
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            $request->getSession()->getFlashbag()
                ->add('success', $this->get('translator')->trans('oro.user.messages.user_added'));

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $form->createView()
        );
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $form = $this->createForm(new UserType($this->isMyProfile($entity)), $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="user_new")
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $entity = new User();
        $form   = $this->createCreateForm($entity);

        return array(
            'entity' => $entity,
            'form'   => $form->createView(),
        );
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="user_show")
     * @Method("GET")
     * @Template()
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.user.messages.user_not_found'));
        }

        $issues = $this->getDoctrine()
            ->getRepository('OroIssueBundle:Issue')
            ->findBy(
                array(
                'issueStatus' => 'open',
                'assignee' => $id)
            );


        return array(
            'entity' => $entity,
            'issues' => $issues,
            'my_profile' => $this->isMyProfile($entity)
        );
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="user_edit")
     * @Method("GET")
     * @Template()
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroUserBundle:User')->find($id);

        if (false === $this->get('security.context')->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.user.messages.access_denied'));
        }

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.user.messages.user_not_found'));
        }

        $editForm = $this->createEditForm($entity);

        return array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'my_profile' => $this->isMyProfile($entity)
        );
    }

    /**
    * Creates a form to edit a User entity.
    *
    * @param User $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(User $entity)
    {
        $form = $this->createForm(new UserType($this->isMyProfile($entity)), $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="user_update")
     * @Method("POST")
     * @Template("OroUserBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('OroUserBundle:User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException($this->get('translator')->trans('oro.user.messages.user_not_found'));
        }

        if (false === $this->get('security.context')->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.user.messages.access_denied'));
        }

        $originalPassword = $entity->getPassword();

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            //upload avatar
            $entity->avatarUpload();

            //store password
            $plainPassword = $editForm->get('plainPassword')->getData();
            if (!empty($plainPassword)) {
                $entity->setPassword($this->encodePassword($entity, $plainPassword));
            } else {
                $entity->setPassword($originalPassword);
            }

            $em->flush();

            $request->getSession()->getFlashbag()
                ->add('success', $this->get('translator')->trans('oro.user.messages.user_updated'));

            return $this->redirect($this->generateUrl('user_show', array('id' => $id)));
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView()
        );
    }

    /**
     * Encode plain password
     *
     * @param User $user
     * @param $plainPassword
     * @return string
     */
    protected function encodePassword(User $user, $plainPassword)
    {
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        return $encoder->encodePassword($plainPassword, $user->getSalt());
    }

    /**
     * Compare user entity with current user
     *
     * @param User $user
     * @return bool
     */
    protected function isMyProfile($user)
    {
        $authUser = $this->getUser();
        return ($user->getUsername() == $authUser->getUsername());
    }
}
