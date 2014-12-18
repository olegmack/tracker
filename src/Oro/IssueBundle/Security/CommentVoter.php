<?php

namespace Oro\IssueBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

use Oro\UserBundle\Entity\User;

class CommentVoter implements VoterInterface
{
    const CREATE = 'CREATE';
    const MODIFY = 'MODIFY';

    /**
     * @param string $attribute
     * @return bool
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::CREATE,
            self::MODIFY
        ));
    }

    /**
     * @param string $class
     * @return bool
     */
    public function supportsClass($class)
    {
        $supportedClass = 'Oro\IssueBundle\Entity\Comment';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface $token
     * @param \Oro\ProjectBundle\Entity\Project $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        //check for supported class
        if (!$this->supportsClass(get_class($object))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        //check for supported attribute
        if (!isset($attributes[0]) || !$this->supportsAttribute($attributes[0])) {
            return VoterInterface::ACCESS_ABSTAIN;
        } else {
            //use only first attribute
            $attribute = $attributes[0];
        }

        //get auth user
        $user = $token->getUser();
        if (!$user instanceof User) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::CREATE:
                if ($user->getRole('ROLE_ADMIN')
                    || $user->getRole('ROLE_MANAGER')
                    || ($user->getRole('ROLE_USER')
                        && $object->getIssue()->getProject()->isMember($user->getUsername()))
                ) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::MODIFY:
                if ($user->getRole('ROLE_ADMIN')
                    || ($user->getRole('ROLE_USER') && $object->getAuthor()->getUsername() == $user->getUsername())
                ) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

        }

        return VoterInterface::ACCESS_DENIED;
    }
}
