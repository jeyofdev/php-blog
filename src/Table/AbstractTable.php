<?php

    namespace jeyofdev\php\blog\Table;


    use jeyofdev\php\blog\Pagination\Pagination;
    use jeyofdev\php\blog\Exception\ExecuteQueryFailedException;
    use jeyofdev\php\blog\Exception\RuntimeException;
    use PDO;
    use PDOStatement;


    /**
     * Manage the standard queries
     * 
     * @author jeyofdev <jgregoire.pro@gmail.com>
     */
    abstract class AbstractTable implements TableInterface, QueryInterface
    {
        /**
         * @var PDO
         */
        protected $connection;



        /**
         * The name of the table
         *
         * @var string
         */
        protected $tableName;



        /**
         * The name of the current class
         *
         * @var string
         */
        protected $className;



        /**
         * @var PostTable|CategoryTable
         */
        protected $table;



        /**
         * The columns of the table
         *
         * @var array
         */
        protected $columns = [];



        /**
         * @var PDOStatement
         */
        protected $query;



        /**
         * @var Pagination
         */
        protected $pagination;



        /**
         * The allowed values ​​for the clause 'order by'
         */
        const DIRECTION_ALLOWED = ["ASC", "DESC"];



        /**
         * @param PDO $connection
         */
        public function __construct (PDO $connection)
        {
            $this->connection = $connection;

            if (is_null($this->tableName) || is_null($this->className)) {
                $pos = strrpos(get_class($this), "\\") + 1;
                $name = substr(get_class($this), $pos);

                if (is_null($this->tableName)) {
                    throw (new RuntimeException())->propertyValueIsNull($name, "tableName");
                } else if (is_null($this->className)) {
                    throw (new RuntimeException())->propertyValueIsNull($name, "className");
                }
            } else {
                $this->table = new $this->className();
            }

            $this->columns = $this->table
                ->setColumns($this->table)
                ->getColumns();
        }



        /**
         * {@inheritDoc}
         */
        public function find (array $params, $fetchMode = PDO::FETCH_CLASS)
        {
            $where = $this->setWhere($params);

            $sql = "SELECT * FROM {$this->tableName} WHERE $where";
            $query = $this->prepare($sql, $params, $fetchMode);

            return $this->fetch($query);
        }



        /**
         * {@inheritDoc}
         */
        public function findAll (int $fetchMode = PDO::FETCH_CLASS) : array
        {
            $sql = "SELECT * FROM {$this->tableName}";
            $query = $this->query($sql, $fetchMode);

            return $this->fetchAll($query);
        }



        /**
         * {@inheritDoc}
         */
        public function findAllBy (?string $orderBy = null, string $direction = "ASC", ?int $limit = null, ?int $offset = null, int $fetchMode = PDO::FETCH_CLASS) : array
        {
            $sql = "SELECT * FROM {$this->tableName}";

            if (!is_null($orderBy)) {
                $direction = strtoupper($direction);
                $sql .= " ORDER BY $orderBy $direction";
            }

            if (!is_null($limit)) {
                $sql .= " LIMIT $limit";
            }

            if (!is_null($offset)) {
                $sql .= " OFFSET $offset";
            }
            
            $query = $this->query($sql, $fetchMode);

            return $this->fetchAll($query);
        }



        /**
         * {@inheritDoc}
         */
        public function create (array $params, ?string $tableName = null) : self
        {
            $tableName = !is_null($tableName) ? $tableName : $this->tableName;
            $set = $this->setQueryParams($params);

            $sql = "INSERT INTO $tableName SET $set[0]";
            $query = $this->prepare($sql, $set[1]);

            if (!$query) {
                throw (new ExecuteQueryFailedException())->createHasFailed("id", $this->tableName);
            }

            return $this;
        }



        /**
         * {@inheritDoc}
         */
        public function update (array $params, array $where) : self
        {
            $set = $this->setQueryParams($params);
            $where = $this->setQueryParams($where);

            $sql = "UPDATE {$this->tableName} SET $set[0] WHERE $where[0]";
            $query = $this->prepare($sql, array_merge($set[1], $where[1]));

            if (!$query) {
                throw (new ExecuteQueryFailedException())->updateHasFailed("id", $this->tableName);
            }

            return $this;
        }



        /**
         * {@inheritDoc}
         */
        public function delete (array $params, ?string $tableName = null) : self
        {
            $tableName = is_null($tableName) ? $this->tableName : $tableName;
            $where = $this->setWhere($params);

            $sql = "DELETE FROM $tableName WHERE $where";
            $query = $this->prepare($sql, $params);

            if (!$query) {
                throw (new ExecuteQueryFailedException())->deleteHasFailed("id", $this->tableName);
            }

            return $this;
        }



        /**
         * {@inheritDoc}
         */
        public function exists (array $params, ?int $except = null) : bool
        {
            $where = $this->setWhere($params);

            if (!is_null($except)) {
                $where .= " AND id != :id";
                $params["id"] = $except;
            }

            $sql = "SELECT COUNT(id) FROM {$this->tableName} WHERE $where";
            $query = $this->prepare($sql, $params, PDO::FETCH_NUM);
            
            return (int)$this->fetch($query)[0] > 0;
        }



        /**
         * {@inheritDoc}
         */
        public function countAll (string $column, int $fetchMode = PDO::FETCH_NUM) : int
        {
            $sql = "SELECT COUNT($column) FROM {$this->tableName}";
            $query = $this->query($sql, $fetchMode);

            return $this->fetch($query)[0];
        }



        /**
         * Set the parameters of where clause
         *
         * @return string|null
         */
        public function setWhere (array $params) : ?string
        {
            if (!is_null($params)) {
                $items = [];
                foreach ($params as $k => $v) {
                    $items[] = "$k = :$k";
                }
            }

            return isset($items) ? implode(", ", $items) : null;
        }



        /**
         * Set the parameters of a prepare query
         *
         * @return array
         */
        protected function setQueryParams (array $params) : array
        {
            $items = [];
            $itemsValues = [];

            foreach ($params as $k => $v) {
                $items[] = "$k = :$k";
                $itemsValues[$k] = $v;
            }

            $items = implode(", ", $items);

            return [$items, $itemsValues];
        }



        /**
         * {@inheritDoc}
         */
        public function query (string $sql, int $fetchMode = PDO::FETCH_CLASS) : PDOStatement
        {
            $this->query = $this->connection->query($sql);
            $this->setFetchMode($fetchMode);

            return $this->query;
        }



        /**
         * {@inheritDoc}
         */
        public function prepare (string $sql, array $params = [], int $fetchMode = PDO::FETCH_CLASS) : PDOStatement
        {
            $this->query = $this->connection->prepare($sql);
            $this->query->execute($params);
            $this->setFetchMode($fetchMode);

            return $this->query;
        }



        /**
         * {@inheritDoc}
         */
        public function fetch ()
        {
            return $this->query->fetch();
        }



        /**
         * {@inheritDoc}
         */
        public function fetchAll () : array
        {
            return $this->query->fetchAll();
        }



        /**
         * {@inheritDoc}
         */
        public function setFetchMode (int $fetchMode) : void
        {
            if ($fetchMode === PDO::FETCH_CLASS) {
                $this->query->setFetchMode($fetchMode, $this->className);
            } else {
                $this->query->setFetchMode($fetchMode);
            }
        }



        /**
         * Get the value of pagination
         *
         * @return Pagination|null
         */
        public function getPagination () : ?Pagination
        {
            return $this->pagination;
        }



        /**
         * Check that a value is allowed in a clause of a query
         *
         * @param mixed $value
         * @return bool|void
         */
        protected function checkIfValueIsAllowed (string $clause, $value, array $allowed)
        {
            if (in_array($value, $allowed)) {
                return true;
            } else {
                if ($clause === "orderBy") {
                    throw (new RuntimeException())->columnNotExistInDatabase($value);
                } else if ($clause === "direction") {
                    throw (new RuntimeException())->valueNotAllowed($value, $clause);
                }
            }
        }
    }