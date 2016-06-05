<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 28.05.16
 * Time: 20:12
 */

namespace database\RepositoryBundle\factory;


use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\QueryBuilderBundle\builder\ExpressionBuilder;
use database\QueryBuilderBundle\builder\QueryBuilder;
use database\QueryBuilderBundle\factory\QueryBuilderBundleFactory;
use database\QueryBundle\factory\QueryBundleFactory;
use database\QueryBundle\query\Query;
use database\RepositoryBundle\exception\RepositoryException;
use database\RepositoryBundle\interfaces\ResultIteratorInterface;

class RepositoryFactory {
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var QueryBuilderBundleFactory
     */
    private $queryBuilderFactory;
    /**
     * @var QueryBundleFactory
     */
    private $queryFactory;

    public function __construct (ConnectionInterface $connection) {
        $this->connection = $connection;
        $this->queryBuilderFactory = new QueryBuilderBundleFactory();
        $this->queryFactory = new QueryBundleFactory($this->connection);
    }

    /**
     * @return QueryBuilder
     */
    public function createQueryBuilder () {
        return $this->queryBuilderFactory->createQueryBuilder();
    }

    /**
     * @param string|QueryBuilder $sql
     *
     * @return Query
     */
    public function createQuery ($sql) {
        $parameters = [];
        if ($sql instanceof QueryBuilder) {
            $parameters = $sql->getParameters();
        }

        return $this->queryFactory->createQuery($sql, $parameters);
    }

    /**
     * @return ExpressionBuilder
     */
    public function createExpressionBuilder () {
        return $this->queryBuilderFactory->createExpressionBuilder();
    }

    /**
     * @param string $class result iterator class
     * @param Query  $query
     *
     * @return ResultIteratorInterface
     * @throws RepositoryException
     */
    public function createResultIterator ($class, Query $query) {
        $result = $query->buildResult();
        $iterator = new $class($result);

        if (!($iterator instanceof ResultIteratorInterface)) {
            throw new RepositoryException($class.' is not a valid result iterator!');
        }

        return $iterator;
    }
}