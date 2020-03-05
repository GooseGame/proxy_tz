<?php
    namespace Spider;

    class SpiderDBDecorator extends SpiderDecorator {
        function getData() {
            return $this->parsedData;
        }
    }
?>