<?php
namespace database\RepositoryBundle\tests;

use database\DriverBundle\tests\AbstractFunctionalDatabaseTest;
use database\RepositoryBundle\repository\BaseRepository;
use ReflectionClass;

/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 29.05.16
 * Time: 00:25
 */
abstract class AbstractBaseRepositoryFunctionalTest extends AbstractFunctionalDatabaseTest {
    /**
     * @var BaseRepository
     */
    protected $repository;

    /**
     * @test
     */
    public function findByTable () {
        $result = $this->callReflectionMethod('findByTable', ['example1', 'e1', [], [], 1, 0]);

        $this->assertEquals([['id' => 1, 'info' => 'test0']], iterator_to_array($result));

        $result = $this->callReflectionMethod('findByTable', ['example1', 'e1', ['info' => 'test1'], []]);
        $this->assertEquals([['id' => 2, 'info' => 'test1']], iterator_to_array($result));

        $result = $this->callReflectionMethod('findByTable', ['example1', 'e1', [], ['info' => 'DESC'], 2, 0]);

        $this->assertEquals([['id' => 10, 'info' => 'test9'], ['id' => 9, 'info' => 'test8']],
                            iterator_to_array($result));
    }

    private function callReflectionMethod ($method, array $args = []) {
        $reflection = new ReflectionClass(BaseRepository::class);
        $reflectionMethod = $reflection->getMethod($method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod->invokeArgs($this->repository, $args);
    }
}