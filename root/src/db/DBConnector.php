<?php
    namespace tz\src\db;

    class DBConnector {
        private $db;
        private $config;

        function __construct()
        {
            $this->config = parse_ini_file('.../app.ini');
            $this->db = mysqli_connect($this->config['host'], $this->config['username'], $this->config['password']);
        }

        function query(string $query) {
            return $this->db->query($query);
        }

        function fetch($query) {
            return $query->fetch_assoc();
        }
    }
?>