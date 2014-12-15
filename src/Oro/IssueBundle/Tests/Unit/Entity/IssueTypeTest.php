<?php

namespace Oro\IssueBundle\Tests\Unit\Entity;

use Oro\IssueBundle\Entity\IssueType;

class IssueTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var IssueType
     */
    protected $object;
    protected $code = 'testcode';

    protected function setUp()
    {
        parent::setUp();

        $this->object = new IssueType($this->code);
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
