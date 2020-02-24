<?php
      require_once 'SpiderClientDecorator.php';
      require_once 'parser.php';

      error_reporting(0);
      $site = $_GET['site'];
      $spider = new SpiderClientDecorator(new ParseCoupons);

      $spider->checkDataInDBBeforeStart($site);
      $spider->getData();
?>