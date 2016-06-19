RepositoryBundle
================
this bundle provides a base class for repositories

Installation
============
add the bundle in your composer.json as bellow:
```js
"require": {
    ...
    ,"LimetecBiotechnologies/database/RepositoryBundle" : "v0.2.*"
},
"repositories" : [
    ...
    ,{
        "type" : "git",
        "url" : "https://github.com/Seretos/QueryBuilderBundle"
    }
    ,{
         "type" : "git",
         "url" : "https://github.com/Seretos/QueryBundle"
    }
    ,{
         "type" : "git",
         "url" : "https://github.com/Seretos/DriverBundle"
    }
    ,{
         "type" : "git",
         "url" : "https://github.com/Seretos/RepositoryBundle"
    }
]
```

Usage
=====
create a repository and an result iterator
```php
class MyResultIterator extends AbstractResultIterator {
    public function current () {
        $current = $this->_statement->current();
        //convert the row in your custom format
        return $current;
    }
}
```
```php
class MyRepository extends BaseRepository {
    //this function returns an StatementIterator
    //every filter will be handled as "and equal" filter
    //$order = ['col1' =>'ASC','col2'=>'DESC']
    public function findBy(array $filters = [], array $orders = [], $limit = 0, $offset = 0) {
        return $this->findByTable('myTable', $filter, $orders, $limit, $offset);
    }
    
    public function myCustomFindFunction() {
        $queryBuilder = $this->createQueryBuilder();
        $expressionBuilder = $this->createExpressionBuilder();

        $query = $this->createQuery('SELECT * FROM example1');
        
        $resultStatementIterator = $query->buildResult();
        $myResultIterator = $this->createResultIterator(MyResultIterator::class,$resultStatementIterator);
        return $myResultIterator;
    }
}
```

create an instance of your Repository
mysqli:
```php
$mysqli = new mysqli($host,$user,$password,$database);
$connection = new MysqliConnection($mysqli);
$queryBuilderFactory = new QueryBuilderBundleFactory();
$queryFactory = new QueryBundleFactory($connection);

$factory = new RepositoryFactory($connection,$queryBuilderFactory, $queryFactory);
$repository = new MyRepository($factory);
```
pdo:
```php
$connection = new PdoConnection($host,$user,$password,$database);

$factory = new RepositoryFactory($connection);
$repository = new MyRepository($factory);
```