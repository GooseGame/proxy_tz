<?php
    namespace tz\src\spider;

    class SpiderDBDecorator extends SpiderDecorator {
        function getData() {
            return $this->parsedData;
        }
    }
?>