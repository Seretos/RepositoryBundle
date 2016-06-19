<?php
use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBuilderBundle\builder\QueryBuilder;
use database\QueryBuilderBundle\factory\QueryBuilderBundleFactory;
use database\QueryBundle\factory\QueryBundleFactory;
use database\QueryBundle\query\Query;
use database\RepositoryBundle\exception\RepositoryException;
use database\RepositoryBundle\factory\RepositoryFactory;
use database\RepositoryBundle\tests\factory\util\ExampleInvalidIterator;
use database\RepositoryBundle\tests\factory\util\ExampleResultIterator;

/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 28.05.16
 * Time: 23:50
 */
class RepositoryFactoryTest extends PHPUnit_Framework_TestCase {

    /**
     * @var RepositoryFactory
     */
    private $repositoryFactory;

    /**
     * @var ReflectionClass
     */
    private $factoryReflection;

    /**
     * @var ConnectionInterface|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockConnection;

    /**
     * @var QueryBuilderBundleFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockQueryBuilderFactory;

    /**
     * @var QueryBundleFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockQueryFactory;

    protected function setUp () {
        $this->mockConnection = $this->getMockBuilder(ConnectionInterface::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
        $this->mockQueryBuilderFactory = $this->getMockBuilder(QueryBuilderBundleFactory::class)
                                              ->disableOriginalConstructor()
                                              ->getMock();
        $this->mockQueryFactory = $this->getMockBuilder(QueryBundleFactory::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->factoryReflection = new ReflectionClass(RepositoryFactory::class);
        $this->repositoryFactory = new RepositoryFactory($this->mockConnection,
                                                         $this->mockQueryBuilderFactory,
                                                         $this->mockQueryFactory);

        $queryBuilderFactoryProperty = $this->factoryReflection->getProperty('queryBuilderFactory');
        $queryBuilderFactoryProperty->setAccessible(true);

        $queryFactoryProperty = $this->factoryReflection->getProperty('queryFactory');
        $queryFactoryProperty->setAccessible(true);

        $this->assertInstanceOf(QueryBuilderBundleFactory::class,
                                $queryBuilderFactoryProperty->getValue($this->repositoryFactory));
        $this->assertInstanceOf(QueryBundleFactory::class,
                                $queryFactoryProperty->getValue($this->repositoryFactory));

        $queryBuilderFactoryProperty->setValue($this->repositoryFactory, $this->mockQueryBuilderFactory);
        $queryFactoryProperty->setValue($this->repositoryFactory, $this->mockQueryFactory);
    }

    /**
     * @test
     */
    public function createQueryBuilder () {
        $this->mockQueryBuilderFactory->expects($this->once())
                                      ->method('createQueryBuilder')
                                      ->will($this->returnValue('success'));
        $this->assertSame('success', $this->repositoryFactory->createQueryBuilder());
    }

    /**
     * @test
     */
    public function createQuery () {
        $this->mockQueryFactory->expects($this->once())
                               ->method('createQuery')
                               ->with('SELECT', [])
                               ->will($this->returnValue('success'));
        $this->assertSame('success', $this->repositoryFactory->createQuery('SELECT'));
    }

    /**
     * @test
     */
    public function createQuery_withBuilder () {
        $mockBuilder = $this->getMockBuilder(QueryBuilder::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        $mockBuilder->expects($this->once())
                    ->method('getParameters')
                    ->will($this->returnValue('test'));

        $this->mockQueryFactory->expects($this->once())
                               ->method('createQuery')
                               ->with($mockBuilder, 'test')
                               ->will($this->returnValue('success'));
        $this->assertSame('success', $this->repositoryFactory->createQuery($mockBuilder));
    }

    /**
     * @test
     */
    public function createExpression () {
        $this->mockQueryBuilderFactory->expects($this->once())
                                      ->method('createExpressionBuilder')
                                      ->will($this->returnValue('success'));
        $this->assertSame('success', $this->repositoryFactory->createExpressionBuilder());
    }

    /**
     * @test
     */
    public function createResultIterator_withException () {
        /* @var $mockQuery Query|PHPUnit_Framework_MockObject_MockObject */
        $mockQuery = $this->getMockBuilder(Query::class)
                          ->disableOriginalConstructor()
                          ->getMock();
        $mockQuery->expects($this->once())
                  ->method('buildResult')
                  ->will($this->returnValue('success'));

        $this->setExpectedExceptionRegExp(RepositoryException::class);
        $this->repositoryFactory->createResultIterator(ExampleInvalidIterator::class, $mockQuery);
    }

    /**
     * @test
     */
    public function createResultIterator () {
        /* @var $mockQuery Query|PHPUnit_Framework_MockObject_MockObject */
        $mockQuery = $this->getMockBuilder(Query::class)
                          ->disableOriginalConstructor()
                          ->getMock();
        $mockStatement = $this->getMockBuilder(StatementInterface::class)
                              ->disableOriginalConstructor()
                              ->getMock();
        $mockQuery->expects($this->once())
                  ->method('buildResult')
                  ->will($this->returnValue($mockStatement));

        /* @var $result ExampleResultIterator */
        $result = $this->repositoryFactory->createResultIterator(ExampleResultIterator::class, $mockQuery);
        $this->assertInstanceOf(ExampleResultIterator::class, $result);
        $this->assertSame($mockStatement, $result->getStatement());
    }
}