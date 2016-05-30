<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 28.05.16
 * Time: 20:12
 */

namespace database\RepositoryBundle\factory;


use database\DriverBundle\connection\interfaces\ConnectionInterface;
use database\QueryBuilderBundle\builder\QueryBuilder;
use database\QueryBuilderBundle\expression\Expression;
use database\QueryBuilderBundle\factory\QueryBuilderFactory;
use database\QueryBundle\factory\QueryFactory;
use database\QueryBundle\query\Query;
use database\RepositoryBundle\exception\RepositoryException;
use database\RepositoryBundle\interfaces\ResultIteratorInterface;

class RepositoryFactory {
    /**
     * @var ConnectionInterface
     */
    private $connection;

    /**
     * @var QueryBuilderFactory
     */
    private $queryBuilderFactory;
    /**
     * @var QueryFactory
     */
    private $queryFactory;

    public function __construct (ConnectionInterface $connection) {
        $this->connection = $connection;
        $this->queryBuilderFactory = new QueryBuilderFactory($this->connection);
        $this->queryFactory = new QueryFactory($this->connection);
    }

    public function createQueryBuilder () {
        return new QueryBuilder($this->queryBuilderFactory);
    }

    public function createQuery ($sql) {
        return new Query($this->queryFactory, $sql);
    }

    public function createExpressionBuilder () {
        return new Expression();
    }

    public function createResultIterator ($class, Query $query) {
        $result = $query->buildResult();
        $iterator = new $class($result);

        if (!($iterator instanceof ResultIteratorInterface)) {
            throw new RepositoryException($class.' is not a valid result iterator!');
        }

        return $iterator;
    }
}