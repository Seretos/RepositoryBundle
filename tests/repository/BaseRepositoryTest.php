<?php
use database\RepositoryBundle\factory\RepositoryFactory;
use database\RepositoryBundle\repository\BaseRepository;
use database\QueryBuilderBundle\builder\QueryBuilder;
use database\QueryBuilderBundle\expression\AndExpression;
use database\QueryBuilderBundle\expression\Expression;
use database\QueryBundle\query\Query;

/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 28.05.16
 * Time: 22:26
 */
class BaseRepositoryTest extends PHPUnit_Framework_TestCase {
    /**
     * @var BaseRepository
     */
    private $repository;

    /**
     * @var RepositoryFactory|PHPUnit_Framework_MockObject_MockObject
     */
    private $mockFactory;

    /**
     * @var ReflectionClass
     */
    private $repositoryReflection;

    protected function setUp () {
        $this->repositoryReflection = new ReflectionClass(BaseRepository::class);
        $this->mockFactory = $this->getMockBuilder(RepositoryFactory::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();
        $this->repository = new BaseRepository($this->mockFactory);
    }

    /**
     * @test
     */
    public function createQueryBuilder () {
        $this->mockFactory->expects($this->once())
                          ->method('createQueryBuilder')
                          ->will($this->returnValue('success'));
        $this->assertSame('success', $this->callProtectedMethod('createQueryBuilder'));
    }

    /**
     * @test
     */
    public function createExpressionBuilder () {
        $this->mockFactory->expects($this->once())
                          ->method('createExpressionBuilder')
                          ->will($this->returnValue('success'));
        $this->assertSame('success', $this->callProtectedMethod('createExpressionBuilder'));
    }

    /**
     * @test
     */
    public function createResultIterator () {
        $mockQuery = $this->getMockBuilder(Query::class)
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->mockFactory->expects($this->once())
                          ->method('createResultIterator')
                          ->with('test', $mockQuery)
                          ->will($this->returnValue('success'));
        $this->assertSame('success', $this->callProtectedMethod('createResultIterator', ['test', $mockQuery]));
    }

    /**
     * @test
     */
    public function findByTable_withAllParams () {
        $filters = ['col1' => 'val1', 'col2' => 'val2'];
        $orders = ['col1,col2' => 'ASC', 'col3' => 'DESC'];
        $this->createFindByTableBuilderMock($filters, $orders, 2, 1);

        $this->assertSame('success',
                          $this->callProtectedMethod('findByTable',
                                                     ['example1', 'e1', $filters, $orders, 2, 1]));
    }

    /**
     * @test
     */
    public function findByTable () {
        $this->createFindByTableBuilderMock();

        $this->assertSame('success', $this->callProtectedMethod('findByTable', ['example1', 'e1']));
    }

    /**
     * @test
     */
    public function findByTable_withFilter () {
        $filters = ['col1' => 'val1'];
        $this->createFindByTableBuilderMock($filters);

        $this->assertSame('success',
                          $this->callProtectedMethod('findByTable',
                                                     ['example1', 'e1', $filters]));
    }

    private function createFindByTableExpressionMock ($filters = []) {
        $mockExpression = $this->getMockBuilder(Expression::class)
                               ->disableOriginalConstructor()
                               ->getMock();
        $mockAndExpression = $this->getMockBuilder(AndExpression::class)
                                  ->disableOriginalConstructor()
                                  ->getMock();

        $this->mockFactory->expects($this->once())
                          ->method('createExpressionBuilder')
                          ->will($this->returnValue($mockExpression));

        $mockExpression->expects($this->once())
                       ->method('andX')
                       ->will($this->returnValue($mockAndExpression));

        $index = 1;
        foreach ($filters as $key => $value) {
            $mockExpression->expects($this->at($index))
                           ->method('eq')
                           ->with($key, ':'.$key)
                           ->will($this->returnValue('compare success'));

            $mockAndExpression->expects($this->at($index - 1))
                              ->method('add')
                              ->with('compare success')
                              ->will($this->returnValue(""));
            $index++;
        }

        return $mockAndExpression;
    }

    private function createFindByTableBuilderMock ($filters = [], $orders = [], $limit = 0, $offset = 0) {
        $mockAndExpression = $this->createFindByTableExpressionMock($filters);
        $mockBuilder = $this->getMockBuilder(QueryBuilder::class)
                            ->disableOriginalConstructor()
                            ->getMock();
        $mockQuery = $this->getMockBuilder(Query::class)
                          ->disableOriginalConstructor()
                          ->getMock();

        $this->mockFactory->expects($this->once())
                          ->method('createQueryBuilder')
                          ->will($this->returnValue($mockBuilder));

        $index = 0;
        $mockBuilder->expects($this->at($index))
                    ->method('select')
                    ->with('*')
                    ->will($this->returnValue($mockBuilder));
        $index++;
        $mockBuilder->expects($this->at($index))
                    ->method('from')
                    ->with('example1', 'e1')
                    ->will($this->returnValue($mockBuilder));
        $index++;
        $mockBuilder->expects($this->at($index))
                    ->method('setFirstResult')
                    ->with($offset)
                    ->will($this->returnValue($mockBuilder));
        $index++;
        $mockBuilder->expects($this->at($index))
                    ->method('setMaxResult')
                    ->with($limit)
                    ->will($this->returnValue($mockBuilder));
        if (count($filters) > 0) {
            $index++;
            $mockBuilder->expects($this->at($index))
                        ->method('where')
                        ->with($mockAndExpression)
                        ->will($this->returnValue($mockBuilder));
        }

        foreach ($orders as $columns => $direction) {
            $index++;
            $mockBuilder->expects($this->at($index))
                        ->method('addOrderBy')
                        ->with($columns, $direction)
                        ->will($this->returnValue($mockBuilder));
        }

        $mockBuilder->expects($this->once())
                    ->method('buildQuery')
                    ->will($this->returnValue($mockQuery));

        $mockQuery->expects($this->once())
                  ->method('setParameters')
                  ->with($filters);

        $mockQuery->expects($this->once())
                  ->method('buildResult')
                  ->will($this->returnValue('success'));

        return $mockBuilder;
    }

    private function callProtectedMethod ($method, array $args = []) {
        $reflectionMethod = $this->repositoryReflection->getMethod($method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($this->repository, $args);
    }
}