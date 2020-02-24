<?php
    class MyDB {
        private $db;
        function __construct()
        {
            $this->db = mysqli_connect('localhost', 'host', '1234');
        }
        function get() {
            return $this->db;
        }
        function query($string) {
            return $this->db->query($string);
        }
    }
?>