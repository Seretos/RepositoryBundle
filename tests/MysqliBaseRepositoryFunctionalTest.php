<?php
use database\DriverBundle\connection\mysqli\MysqliConnection;
use database\QueryBuilderBundle\factory\QueryBuilderBundleFactory;
use database\QueryBundle\factory\QueryBundleFactory;
use database\RepositoryBundle\factory\RepositoryFactory;
use database\RepositoryBundle\repository\BaseRepository;
use database\RepositoryBundle\tests\AbstractBaseRepositoryFunctionalTest;

/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 30.05.16
 * Time: 20:14
 */
class MysqliBaseRepositoryFunctionalTest extends AbstractBaseRepositoryFunctionalTest {
    protected function setUp () {
        parent::setUp();
        $mysqli = new mysqli(self::CONFIG['host'],
                             self::CONFIG['user'],
                             self::CONFIG['password'],
                             self::CONFIG['database']);

        $this->connection = new MysqliConnection($mysqli);

        $factory = new RepositoryFactory($this->connection,
                                         new QueryBuilderBundleFactory(),
                                         new QueryBundleFactory($this->connection));
        $this->repository = new BaseRepository($factory);
    }
}