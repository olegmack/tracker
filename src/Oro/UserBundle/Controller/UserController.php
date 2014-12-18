<?php

namespace Oro\UserBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Oro\UserBundle\Entity\User;

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
     *
     * @return array
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
     * @Route("/create", name="user_create")
     * @Template()
     *
     * @param Request $request
     * @return array
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
        $form = $this->createForm('oro_userbundle_user', $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
        ));

        return $form;
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/view/{id}", name="user_show")
     * @ParamConverter("entity", class="OroUserBundle:User")
     * @Method("GET")
     * @Template()
     *
     * @param User $entity
     * @return array
     */
    public function showAction(User $entity)
    {
        $issues = $this->getDoctrine()
            ->getRepository('OroIssueBundle:Issue')
            ->findBy(
                array(
                'issueStatus' => 'open',
                'assignee' => $entity->getId())
            );


        return array(
            'entity' => $entity,
            'issues' => $issues,
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
    private function createUpdateForm(User $entity)
    {
        $form = $this->createForm('oro_userbundle_user', $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getId())),
            'method' => 'POST',
        ));

        return $form;
    }
    /**
     * Edits an existing User entity.
     *
     * @Route("/update/{id}", name="user_update")
     * @ParamConverter("entity", class="OroUserBundle:User")
     * @Template()
     *
     * @param User $entity
     * @param Request $request
     * @return array
     */
    public function updateAction(User $entity, Request $request)
    {
        if (false === $this->get('security.context')->isGranted('EDIT', $entity)) {
            throw new AccessDeniedException($this->get('translator')->trans('oro.user.messages.access_denied'));
        }

        $originalPassword = $entity->getPassword();

        $editForm = $this->createUpdateForm($entity);
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

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $request->getSession()->getFlashbag()
                ->add('success', $this->get('translator')->trans('oro.user.messages.user_updated'));

            return $this->redirect($this->generateUrl('user_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form'   => $editForm->createView(),
            'my_profile' => $this->isMyProfile($entity)
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
