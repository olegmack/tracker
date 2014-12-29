<?php
namespace Oro\UserBundle\Security;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter implements VoterInterface
{
    const VIEW = 'VIEW';
    const EDIT = 'EDIT';
    const EDIT_ROLE = 'EDIT_ROLE';
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
            self::EDIT,
            self::VIEW_LIST,
            self::EDIT_ROLE
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
        $supportedClass = 'Oro\UserBundle\Entity\User';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @param TokenInterface $token
     * @param \Oro\UserBundle\Entity\User|string $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        //check for supported class
        if (!$this->supportsClass($object)) {
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

        switch($attribute) {
            case self::VIEW:
                return VoterInterface::ACCESS_GRANTED;
                break;
            case self::EDIT_ROLE:
            case self::VIEW_LIST:
                if ($user->getRole('ROLE_ADMIN')) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
            case self::EDIT:
                if ($object->getUsername() == $user->getUsername()
                    || $user->getRole('ROLE_ADMIN')) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
