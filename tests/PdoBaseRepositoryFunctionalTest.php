<?php
use database\DriverBundle\connection\pdo\PdoConnection;
use database\RepositoryBundle\factory\RepositoryFactory;
use database\RepositoryBundle\repository\BaseRepository;
use database\RepositoryBundle\tests\AbstractBaseRepositoryFunctionalTest;

/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 30.05.16
 * Time: 20:14
 */
class PdoBaseRepositoryFunctionalTest extends AbstractBaseRepositoryFunctionalTest {
    protected function setUp () {
        parent::setUp();
        $this->connection = new PdoConnection(self::CONFIG['host'],
                                              self::CONFIG['user'],
                                              self::CONFIG['password'],
                                              self::CONFIG['database']);

        $factory = new RepositoryFactory($this->connection);
        $this->repository = new BaseRepository($factory);
    }
}