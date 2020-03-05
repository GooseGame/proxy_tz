<?php
    namespace Db;

    class DBConnector {
        private $db;
        private $config;
        private $stmt;

        function __construct()
        {
            $this->config = parse_ini_file(__DIR__ . "/../app.ini");
            $this->db = mysqli_connect($this->config['host'], $this->config['username'], $this->config['password'], $this->config['db_name']);
            $this->stmt = mysqli_stmt_init($this->db);
        }

        function query(string $query) {
            return $this->db->query($query);
        }

        function prepare(string $query) {
            return $this->stmt->prepare($query);
        }

        function getStatement() {
            return $this->stmt;
        }

        function execute($stmt) {
            try {
                mysqli_stmt_execute($stmt);
                $this->stmt = mysqli_stmt_init($this->db);
            }
            catch (Exception $e) {
                echo $e->getMessage();
            }
        }

        function fetch($query) {
            return $query->fetch_assoc();
        }
    }
?>