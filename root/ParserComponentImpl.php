<?php
    namespace tz;

    interface ParserComponentImpl {
        function parse(string $rawHTML, bool $isServerMode);
    }
?>