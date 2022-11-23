<?php

namespace app\models;

use PDO;
use app\config\Database;
use PDOStatement;

class BaseModel
{
    protected PDO $db;

    public function __construct($options = [])
    {
        $defaultOptions = [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        $options = array_replace($defaultOptions, $options);
        $host = Database::HOST;
        $dbname = Database::NAME;
        $port = Database::PORT;
        $username = Database::USERNAME;
        $password = Database::PASSWORD;
        $charset = Database::CHARSET;
        $dsn = "mysql:host=$host;dbname=$dbname;port=$port;charset=$charset";

        $this->db = new PDO($dsn, $username, $password, $options);
    }

    /**
     * get PDO instance
     * 
     * @return $db PDO instance
     */
    protected function getPdo()
    {
        return $this->db;
    }

    /**
     * Run raw sql query 
     * 
     * @param  string $sql       sql query
     * @return void
     */
    protected function raw($sql)
    {
        $this->db->query($sql);
    }

    /**
     * Run sql query
     * 
     * @param  string $sql       sql query
     * @param  array  $args      params
     * @return object            returns a PDO object
     */
    protected function run($sql, $args = [])
    {
        if (empty($args)) {
            return $this->db->query($sql);
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($args);

        return $stmt;
    }

    /**
     * Get arrrays of records
     * 
     * @param  string $sql       sql query
     * @param  array  $args      params
     * @param  object $fetchMode set return mode ie object or array
     * @return array             returns multiple records
     */
    protected function rows($sql, $args = [], $fetchMode = PDO::FETCH_ASSOC): array
    {
        return $this->run($sql, $args)->fetchAll($fetchMode);
    }

    /**
     * Get arrray of records
     * 
     * @param  string $sql       sql query
     * @param  array  $args      params
     * @param  object $fetchMode set return mode ie object or array
     * @return object            returns single record
     */
    protected function row($sql, $args = [], $fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->run($sql, $args)->fetch($fetchMode);
    }

    /**
     * Get record by id
     * 
     * @param  string $table     name of table
     * @param  integer $id       id of record
     * @param  object $fetchMode set return mode ie object or array
     * @return object            returns single record
     */
    protected function getById($table, $id, $fetchMode = PDO::FETCH_ASSOC)
    {
        return $this->run("SELECT * FROM $table WHERE id = ?", [$id])->fetch($fetchMode);
    }

    /**
     * Get number of records
     * 
     * @param  string $sql       sql query
     * @param  array  $args      params
     * @param  object $fetchMode set return mode ie object or array
     * @return integer           returns number of records
     */
    protected function count($sql, $args = [])
    {
        return $this->run($sql, $args)->rowCount();
    }

    /**
     * Get primary key of last inserted record
     */
    protected function lastInsertId()
    {
        return $this->db->lastInsertId();
    }

    /**
     * insert record
     * 
     * @param  string $table table name
     * @param  array $data  array of columns and values
     */
    protected function insert($table, $data)
    {
        //add columns into comma seperated string
        $columns = implode(',', array_keys($data));

        //get values
        $values = array_values($data);

        $placeholders = array_map(function ($val) {
            return '?';
        }, array_keys($data));

        //convert array into comma seperated string
        $placeholders = implode(',', array_values($placeholders));

        $this->run("INSERT INTO $table ($columns) VALUES ($placeholders)", $values);

        return $this->lastInsertId();
    }

    /**
     * update record
     * 
     * @param  string $table table name
     * @param  array $data  array of columns and values
     * @param  array $where array of columns and values
     */
    protected function update($table, $data, $where)
    {
        //merge data and where together
        $collection = array_merge($data, $where);

        //collect the values from collection
        $values = array_values($collection);

        //setup fields
        $fieldDetails = null;
        foreach ($data as $key => $value) {
            $fieldDetails .= "$key = ?,";
        }
        $fieldDetails = rtrim($fieldDetails, ',');

        //setup where 
        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            $whereDetails .= $i == 0 ? "$key = ?" : " AND $key = ?";
            $i++;
        }

        $stmt = $this->run("UPDATE $table SET $fieldDetails WHERE $whereDetails", $values);

        return $stmt->rowCount();
    }

    /**
     * Delete records
     * 
     * @param  string $table table name
     * @param  array $where array of columns and values
     * @param  integer $limit limit number of records
     */
    protected function delete($table, $where, $limit = 1)
    {
        //collect the values from collection
        $values = array_values($where);

        //setup where 
        $whereDetails = null;
        $i = 0;
        foreach ($where as $key => $value) {
            $whereDetails .= $i == 0 ? "$key = ?" : " AND $key = ?";
            $i++;
        }

        //if limit is a number use a limit on the query
        if (is_numeric($limit)) {
            $limit = "LIMIT $limit";
        }

        $stmt = $this->run("DELETE FROM $table WHERE $whereDetails $limit", $values);

        return $stmt->rowCount();
    }

    /**
     * Delete all records records
     * 
     * @param  string $table table name
     */
    protected function deleteAll($table)
    {
        $stmt = $this->run("DELETE FROM $table");

        return $stmt->rowCount();
    }

    /**
     * Delete record by id
     * 
     * @param  string $table table name
     * @param  integer $id id of record
     */
    protected function deleteById($table, $id)
    {
        $stmt = $this->run("DELETE FROM $table WHERE id = ?", [$id]);

        return $stmt->rowCount();
    }

    /**
     * Delete record by ids
     * 
     * @param  string $table table name
     * @param  string $column name of column
     * @param  string $ids ids of records
     */
    protected function deleteByIds(string $table, string $column, string $ids)
    {
        $stmt = $this->run("DELETE FROM $table WHERE $column IN ($ids)");

        return $stmt->rowCount();
    }

    /**
     * truncate table
     * 
     * @param  string $table table name
     */
    protected function truncate($table)
    {
        $stmt = $this->run("TRUNCATE TABLE $table");

        return $stmt->rowCount();
    }

    private function preparedQuery(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt;
    }

    protected function fetchSingle(string $sql, array $params = []): array
    {
        $result = $this->preparedQuery($sql, $params)->fetch(PDO::FETCH_ASSOC);
        return $result === false ? [] : $result;
    }

    protected function fetchAll(string $sql, array $params = []): array
    {
        return $this->preparedQuery($sql, $params)->fetchAll(PDO::FETCH_ASSOC);
    }

    protected function execute($sql, $params = []): int
    {
        return $this->preparedQuery($sql, $params)->rowCount();
    }

    protected function paramType($param)
    {
        return match (gettype($param)) {
            "integer" => PDO::PARAM_INT,
            "boolean" => PDO::PARAM_BOOL,
            "NULL" => PDO::PARAM_NULL,
            default => PDO::PARAM_STR
        };
    }
}
