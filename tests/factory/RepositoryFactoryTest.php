<?php
use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBuilderBundle\builder\QueryBuilder;
use database\QueryBuilderBundle\expression\Expression;
use database\QueryBuilderBundle\factory\QueryBuilderFactory;
use database\QueryBundle\factory\QueryFactory;
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
     * @var QueryBuilderFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockQueryBuilderFactory;

    /**
     * @var QueryFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockQueryFactory;

    protected function setUp () {
        $this->mockConnection = $this->getMockBuilder(ConnectionInterface::class)
                                     ->disableOriginalConstructor()
                                     ->getMock();
        $this->mockQueryBuilderFactory = $this->getMockBuilder(QueryBuilderFactory::class)
                                              ->disableOriginalConstructor()
                                              ->getMock();
        $this->mockQueryFactory = $this->getMockBuilder(QueryFactory::class)
                                       ->disableOriginalConstructor()
                                       ->getMock();

        $this->factoryReflection = new ReflectionClass(RepositoryFactory::class);
        $this->repositoryFactory = new RepositoryFactory($this->mockConnection);

        $queryBuilderFactoryProperty = $this->factoryReflection->getProperty('queryBuilderFactory');
        $queryBuilderFactoryProperty->setAccessible(true);

        $queryFactoryProperty = $this->factoryReflection->getProperty('queryFactory');
        $queryFactoryProperty->setAccessible(true);

        $this->assertInstanceOf(QueryBuilderFactory::class,
                                $queryBuilderFactoryProperty->getValue($this->repositoryFactory));
        $this->assertInstanceOf(QueryFactory::class,
                                $queryFactoryProperty->getValue($this->repositoryFactory));

        $queryBuilderFactoryProperty->setValue($this->repositoryFactory, $this->mockQueryBuilderFactory);
        $queryFactoryProperty->setValue($this->repositoryFactory, $this->mockQueryFactory);
    }

    /**
     * @test
     */
    public function createQueryBuilder () {
        $queryBuilder = $this->repositoryFactory->createQueryBuilder();

        $queryBuilderReflection = new ReflectionClass(QueryBuilder::class);
        $queryBuilderFactoryProperty = $queryBuilderReflection->getProperty('factory');
        $queryBuilderFactoryProperty->setAccessible(true);

        $this->assertSame($this->mockQueryBuilderFactory, $queryBuilderFactoryProperty->getValue($queryBuilder));
        $this->assertInstanceOf(QueryBuilder::class, $queryBuilder);
    }

    /**
     * @test
     */
    public function createQuery () {
        $query = $this->repositoryFactory->createQuery('SELECT');

        $queryReflection = new ReflectionClass(Query::class);
        $queryFactoryProperty = $queryReflection->getProperty('factory');
        $queryFactoryProperty->setAccessible(true);

        $this->assertSame($this->mockQueryFactory, $queryFactoryProperty->getValue($query));
        $this->assertInstanceOf(Query::class, $query);
    }

    /**
     * @test
     */
    public function createExpression () {
        $this->assertInstanceOf(Expression::class, $this->repositoryFactory->createExpressionBuilder());
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