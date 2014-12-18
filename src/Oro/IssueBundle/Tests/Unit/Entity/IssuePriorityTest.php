<?php

namespace Oro\IssueBundle\Tests\Unit\Entity;

use Oro\IssueBundle\Entity\IssuePriority;

class IssuePriorityTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssuePriority
     */
    protected $object;
    protected $code = 'testcode';

    protected function setUp()
    {
        $this->object = new IssuePriority($this->code);
    }

    public function testGetSetName()
    {
        $name = 'Test Name';
        $this->object->setName($name);
        $this->assertTrue($this->object->getName() == $name);
    }

    public function testGetCode()
    {
        $this->assertTrue($this->object->getCode() == $this->code);
    }
}
