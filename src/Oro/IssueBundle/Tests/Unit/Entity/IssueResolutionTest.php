<?php

namespace Oro\IssueBundle\Tests\Unit\Entity;

use Oro\IssueBundle\Entity\IssueResolution;

class IssueResolutionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueResolution
     */
    protected $object;
    protected $code = 'testcode';

    protected function setUp()
    {
        $this->object = new IssueResolution($this->code);
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

    public function testGetSetPriority()
    {
        $priority = 100;
        $this->object->setPriority($priority);
        $this->assertTrue($this->object->getPriority() == $priority);
    }
}
