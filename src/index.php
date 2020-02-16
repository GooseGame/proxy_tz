<?php
      require_once 'connectToProxy.php';

      error_reporting(0);
      $site = $_GET['site'];
      connect($site);
?>