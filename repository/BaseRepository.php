<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 28.05.16
 * Time: 20:05
 */

namespace database\RepositoryBundle\repository;


use database\DriverBundle\connection\interfaces\StatementInterface;
use database\QueryBuilderBundle\builder\ExpressionBuilder;
use database\QueryBuilderBundle\builder\QueryBuilder;
use database\QueryBundle\query\Query;
use database\RepositoryBundle\exception\RepositoryException;
use database\RepositoryBundle\factory\RepositoryFactory;
use database\RepositoryBundle\interfaces\ResultIteratorInterface;

class BaseRepository {
    /**
     * @var RepositoryFactory
     */
    private $factory;

    public function __construct (RepositoryFactory $factory) {
        $this->factory = $factory;
    }

    /**
     * @return QueryBuilder
     */
    protected function createQueryBuilder () {
        return $this->factory->createQueryBuilder();
    }

    /**
     * @return ExpressionBuilder
     */
    protected function createExpressionBuilder () {
        return $this->factory->createExpressionBuilder();
    }

    /**
     * @param $sql
     *
     * @return Query
     */
    protected function createQuery ($sql) {
        return $this->factory->createQuery($sql);
    }

    protected function buildOneOrNullResult (StatementInterface $result) {
        if($result->rowCount() > 1){
            return null;
        }
        return $result->fetch();
    }

    /**
     * @param string $class
     * @param Query  $query
     *
     * @return ResultIteratorInterface
     * @throws RepositoryException
     */
    protected function createResultIterator ($class, Query $query) {
        return $this->factory->createResultIterator($class, $query);
    }

    /**
     * @param string $table
     * @param string $alias
     * @param array  $filters
     * @param array  $orders
     * @param int    $limit
     * @param int    $offset
     *
     * @return StatementInterface
     */
    protected function findByTable ($table, $alias, array $filters = [], array $orders = [], $limit = 0, $offset = 0) {
        $builder = $this->createQueryBuilder();

        $builder->select('*')
                ->from($table, $alias)
                ->setFirstResult($offset)
                ->setMaxResult($limit);

        $this->addFilterExpressions($builder, $filters);
        $this->addOrder($builder, $orders);

        $query = $this->createQuery($builder);
        $query->setParameters($filters);

        return $query->buildResult();
    }

    private function addOrder (QueryBuilder $builder, array $orders) {
        foreach ($orders as $columns => $direction) {
            $builder->addOrderBy($columns, $direction);
        }
    }

    private function addFilterExpressions (QueryBuilder $builder, array $filters) {
        $exprBuilder = $this->createExpressionBuilder();
        $andX = $exprBuilder->andX();
        foreach ($filters as $filter => $value) {
            $andX->add($exprBuilder->eq($filter, ':'.$filter));
        }
        if (count($filters) > 0) {
            $builder->where($andX);
        }
    }
}