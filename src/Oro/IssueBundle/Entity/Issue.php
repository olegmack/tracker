<?php

namespace Oro\IssueBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Oro\UserBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Issue
 *
 * @ORM\Table(name="issues")
 * @ORM\Entity(repositoryClass="Oro\IssueBundle\Entity\IssueRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Issue
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="summary", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime")
     */
    private $updatedAt;

    /**
     * @var IssueType
     *
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\IssueType")
     * @ORM\JoinColumn(name="issue_type_code", referencedColumnName="code", onDelete="SET NULL")
     */
    protected $issueType;

    /**
     * @var IssuePriority
     *
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\IssuePriority")
     * @ORM\JoinColumn(name="issue_priority_code", referencedColumnName="code", onDelete="SET NULL")
     */
    protected $issuePriority;

    /**
     * @var IssueResolution
     *
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\IssueResolution")
     * @ORM\JoinColumn(name="issue_resolution_code", referencedColumnName="code", onDelete="SET NULL")
     */
    protected $issueResolution;

    /**
     * @var IssueStatus
     *
     * @ORM\ManyToOne(targetEntity="Oro\IssueBundle\Entity\IssueStatus")
     * @ORM\JoinColumn(name="issue_status_code", referencedColumnName="code", onDelete="SET NULL")
     */
    protected $issueStatus;

    /**
     * @var Project
     *
     * @ORM\ManyToOne(targetEntity="Oro\ProjectBundle\Entity\Project", inversedBy="issues")
     * @ORM\JoinColumn(name="project_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $project;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="reporter_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $reporter;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Oro\UserBundle\Entity\User")
     * @ORM\JoinColumn(name="assignee_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $assignee;

    /**
     * @var ArrayCollection User[]
     *
     * @ORM\ManyToMany(targetEntity="Oro\UserBundle\Entity\User")
     * @ORM\JoinTable(name="issue_collaborators",
     *      joinColumns={@ORM\JoinColumn(name="issue_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $collaborators;

    /**
     * @ORM\ManyToOne(targetEntity="Issue", inversedBy="children")
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\OneToMany(targetEntity="Issue", mappedBy="parent")
     */
    protected $children;

    /**
     * @var ArrayCollection Comment[]
     *
     * @ORM\OneToMany(targetEntity="Oro\IssueBundle\Entity\Comment", mappedBy="issue")
     */
    protected $comments;

    public function __construct()
    {
        $this->collaborators = new ArrayCollection();
        $this->children      = new ArrayCollection();
        $this->comments      = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set summary
     *
     * @param string $summary
     * @return Issue
     */
    public function setSummary($summary)
    {
        $this->summary = $summary;

        return $this;
    }

    /**
     * Get summary
     *
     * @return string 
     */
    public function getSummary()
    {
        return $this->summary;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Issue
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Issue
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     * @return Issue
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime 
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @return IssuePriority
     */
    public function getIssuePriority()
    {
        return $this->issuePriority;
    }

    /**
     * @param IssuePriority $issuePriority
     * @return Issue
     */
    public function setIssuePriority($issuePriority)
    {
        $this->issuePriority = $issuePriority;
        return $this;
    }

    /**
     * @return IssueResolution
     */
    public function getIssueResolution()
    {
        return $this->issueResolution;
    }

    /**
     * @param IssueResolution $issueResolution
     * @return Issue
     */
    public function setIssueResolution($issueResolution)
    {
        $this->issueResolution = $issueResolution;
        return $this;
    }

    /**
     * @return IssueStatus
     */
    public function getIssueStatus()
    {
        return $this->issueStatus;
    }

    /**
     * @param IssueStatus $issueStatus
     * @return Issue
     */
    public function setIssueStatus($issueStatus)
    {
        $this->issueStatus = $issueStatus;
        return $this;
    }

    /**
     * @return IssueType
     */
    public function getIssueType()
    {
        return $this->issueType;
    }

    /**
     * @param IssueType $issueType
     * @return Issue
     */
    public function setIssueType($issueType)
    {
        $this->issueType = $issueType;
        return $this;
    }

    /**
     * @return Project
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * @param Project $project
     * @return Issue
     */
    public function setProject($project)
    {
        $this->project = $project;
        return $this;
    }

    /**
     * @return User
     */
    public function getAssignee()
    {
        return $this->assignee;
    }

    /**
     * @param User $assignee
     * @return Issue
     */
    public function setAssignee(User $assignee)
    {
        $this->assignee = $assignee;
        return $this;
    }

    /**
     * @return User
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * @param User $reporter
     * @return Issue
     */
    public function setReporter(User $reporter)
    {
        $this->reporter = $reporter;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getCollaborators()
    {
        return $this->collaborators;
    }

    /**
     * @param User $user
     *
     * @return Issue
     */
    public function addCollaborator(User $user)
    {
        if (!$this->getCollaborators()->contains($user)) {
            $this->getCollaborators()->add($user);
        }

        return $this;
    }

    /**
     * @param User $user
     *
     * @return Issue
     */
    public function removeCollaborator(User $user)
    {
        if (!$this->getCollaborators()->contains($user)) {
            $this->getCollaborators()->removeElement($user);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return Issue
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Issue $parent
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * @ORM\PrePersist
     */
    public function beforePersist()
    {
        $this->createdAt = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->addCollaborator($this->getReporter());
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function beforeSave()
    {
        $this->updatedAt = new \DateTime('now', new \DateTimeZone('UTC'));
        $this->addCollaborator($this->getAssignee());
    }

    public function getCode()
    {
        return $this->project->getCode() . '-' . $this->id;
    }

    public function __toString()
    {
        return $this->getCode() . ': ' . $this->summary;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @param mixed $comments
     */
    public function setComments($comments)
    {
        $this->comments = $comments;
    }
}
