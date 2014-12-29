<?php

namespace Oro\IssueBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Oro\IssueBundle\Entity\IssueActivity;

class ActivityListener
{
    /** @var ContainerInterface  */
    protected $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param LifecycleEventArgs $args
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if (!$this->isActivityEntity($entity)) {
            return;
        }

        //send e-mail to issue collaborators
        /** @var IssueActivity $entity */
        $activityType = $entity->getType();

        $collaborators = $entity->getIssue()->getCollaborators();
        if (!empty($collaborators)) {
            $senderEmail = $this->container->getParameter('oro.sender_email');
            $senderName = $this->container->getParameter('oro.sender_name');

            $emails = array();
            foreach ($collaborators as $collaborator) {
                $emails[] = $collaborator->getEmail();
            }

            $message = \Swift_Message::newInstance()
                ->setSubject('[TRACKER] (' . $entity->getIssue()->getCode() . ') ' . $entity->getIssue()->getSummary())
                ->setFrom($senderEmail, $senderName)
                ->setTo($emails)
                ->setBody(
                    $this->container->get('templating')
                        ->render(
                            'OroIssueBundle:Activity/Renderer:email_' . $activityType  . '.txt.twig',
                            array('activity' => $entity)
                        ),
                    'text/html'
                );

            $this->container->get('mailer')->send($message);
        }
    }

    /**
     * @param object $entity
     * @return bool
     */
    protected function isActivityEntity($entity)
    {
        return $entity instanceof IssueActivity;
    }
}
