<?php
/**
 * Created by PhpStorm.
 * User: aappen
 * Date: 29.05.16
 * Time: 04:32
 */

namespace database\RepositoryBundle\iterator;


use database\DriverBundle\connection\interfaces\StatementInterface;
use database\RepositoryBundle\interfaces\ResultIteratorInterface;

abstract class AbstractResultIterator implements ResultIteratorInterface {
    /**
     * @var StatementInterface
     */
    protected $_statement;

    public function __construct (StatementInterface $statement) {
        $this->_statement = $statement;
    }

    /**
     * Move forward to next element
     * @link  http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next () {
        $this->_statement->next();
    }

    /**
     * Return the key of the current element
     * @link  http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key () {
        return $this->_statement->key();
    }

    /**
     * Checks if current position is valid
     * @link  http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid () {
        return $this->_statement->valid();
    }

    /**
     * Rewind the Iterator to the first element
     * @link  http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind () {
        $this->_statement->rewind();
    }
}