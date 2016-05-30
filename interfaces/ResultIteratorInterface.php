<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 29.05.16
 * Time: 17:00
 */

namespace database\RepositoryBundle\interfaces;


use database\DriverBundle\connection\interfaces\StatementInterface;

interface ResultIteratorInterface extends \Iterator {
    public function __construct (StatementInterface $statement);
}