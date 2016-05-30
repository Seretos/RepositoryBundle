<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 29.05.16
 * Time: 20:58
 */

namespace database\RepositoryBundle\tests\factory\util;


use database\RepositoryBundle\iterator\AbstractResultIterator;

class ExampleResultIterator extends AbstractResultIterator {

    /**
     * Return the current element
     * @link  http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current () {
        // TODO: Implement current() method.
    }

    public function getStatement () {
        return $this->_statement;
    }
}