<?php
namespace Oro\IssueBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class IssueVoter implements VoterInterface
{
    const ACCESS = 'ACCESS';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::ACCESS
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'Oro\IssueBundle\Entity\Issue';

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
        $attribute = $attributes[0];
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        //get auth user
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::ACCESS:
                if ($user->getRole('ROLE_ADMIN')
                    || $user->getRole('ROLE_MANAGER')
                    || ($user->getRole('ROLE_USER') && $object->getProject()->isMember($user->getUsername()))
                ) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}