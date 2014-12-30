<?php
namespace Oro\ProjectBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class ProjectVoter implements VoterInterface
{
    const VIEW      = 'VIEW';
    const MODIFY    = 'MODIFY';
    const VIEW_LIST = 'VIEW_LIST';

    /**
     * Check for supported actions
     *
     * @param string $attribute
     * @return bool
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::MODIFY,
            self::VIEW_LIST
        ));
    }

    /**
     * Check for supported class
     *
     * @param string|object $object
     * @return bool
     */
    public function supportsClass($object)
    {
        //check for supported class
        $class = (is_object($object)) ? get_class($object) : $object;

        $supportedClass = 'Oro\ProjectBundle\Entity\Project';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface $token
     * @param \Oro\ProjectBundle\Entity\Project|string $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        //check for supported attribute
        if (!($attribute = $this->getAttribute($object, $attributes))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        if (is_string($object)) {
            $object = new $object;
        }

        //get auth user
        $user = $token->getUser();

        switch($attribute) {
            case self::VIEW:
                if ($user->getRole('ROLE_ADMIN')
                    || $user->getRole('ROLE_MANAGER')
                    || ($user->getRole('ROLE_USER') && $object->isMember($user->getUsername()))
                ) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case self::VIEW_LIST:
            case self::MODIFY:
                if ($user->getRole('ROLE_ADMIN') || $user->getRole('ROLE_MANAGER')) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }

    /**
     * @param string|object $object
     * @param array $attributes
     * @return int
     */
    protected function getAttribute($object, $attributes)
    {
        $attribute = false;
        if ($this->supportsClass($object)
            && isset($attributes[0])
            && $this->supportsAttribute($attributes[0])) {
            //use only first attribute
            $attribute = $attributes[0];
        }

        return $attribute;
    }
}
