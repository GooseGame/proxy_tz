<?php

namespace Db;

use stringEncode\Exception;

class DBConnector
{
    private $db;

    public function __construct(string $host, string $username, string $password, string $dbName)
    {
        $this->db = mysqli_connect($host, $username, $password, $dbName);
        if (!$this->db) {
            throw new Exception("Error: Unable to connect to MySQL." . PHP_EOL);
        }
    }

    public function query(string $query)
    {
        return $this->db->query($query);
    }

    public function prepare(string $query, $stmt)
    {
        return $stmt->prepare($query);
    }

    public function getInitializedStatement()
    {
        return mysqli_stmt_init($this->db);
    }

    public function escapeString(array $args): array
    {
        $result = array();
        foreach ($args as $arg) {
            $result[] = mysqli_real_escape_string($this->db, $arg);
        }
        return $result;
    }

    public function execute($stmt)
    {
        return mysqli_stmt_execute($stmt);
    }

    public function fetch($query)
    {
        return $query->fetch_assoc();
    }
}