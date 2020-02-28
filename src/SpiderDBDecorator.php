<?php
    namespace tz;

    class SpiderDBDecorator extends SpiderDecorator {
        function getData() {
            return $this->parsedData;
        }
    }
?>