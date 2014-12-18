<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * IssueStatus
 *
 * @ORM\Table(name="issue_statuses")
 * @ORM\Entity
 */
class IssueStatus
{
    const CODE_OPEN       = 'Open';
    const CODE_INPROGRESS = 'In Progress';
    const CODE_RESOLVED   = 'Resolved';
    const CODE_REOPENED   = 'Reopened';
    const CODE_CLOSED     = 'Closed';

    /**
     * @var string
     *
     * @ORM\Column(name="code", type="string", length=50)
     * @ORM\Id
     */
    private $code;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="priority", type="integer")
     */
    private $priority;

    /**
     * @param string $code
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return IssueStatus
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param int $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }
}
