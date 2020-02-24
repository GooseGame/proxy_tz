<?php
    require_once 'Spider.php';
    class SpiderDBDecorator extends SpiderDecorator {
        function getData() {
            return $this->parsedData;
        }
    }
?>