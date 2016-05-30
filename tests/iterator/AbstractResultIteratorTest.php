<?php
use database\DriverBundle\connection\interfaces\StatementInterface;
use database\RepositoryBundle\iterator\AbstractResultIterator;

/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 29.05.16
 * Time: 21:07
 */
class AbstractResultIteratorTest extends PHPUnit_Framework_TestCase {
    /**
     * @var AbstractResultIterator|PHPUnit_Framework_MockObject_MockObject
     */
    private $iterator;
    /**
     * @var StatementInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockStatement;

    protected function setUp () {
        $this->mockStatement = $this->getMockBuilder(StatementInterface::class)
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $this->iterator = $this->getMockBuilder(AbstractResultIterator::class)
                               ->setConstructorArgs([$this->mockStatement])
                               ->setMethods([])
                               ->getMockForAbstractClass();
    }

    /**
     * @test
     */
    public function nextMethod () {
        $this->mockStatement->expects($this->once())
                            ->method('next');
        $this->iterator->next();
    }

    /**
     * @test
     */
    public function keyMethod () {
        $this->mockStatement->expects($this->once())
                            ->method('key')
                            ->will($this->returnValue('success'));
        $this->assertSame('success', $this->iterator->key());
    }

    /**
     * @test
     */
    public function validMethod () {
        $this->mockStatement->expects($this->once())
                            ->method('valid')
                            ->will($this->returnValue('success'));
        $this->assertSame('success', $this->iterator->valid());
    }

    /**
     * @test
     */
    public function rewindMethod () {
        $this->mockStatement->expects($this->once())
                            ->method('rewind');
        $this->iterator->rewind();
    }
}