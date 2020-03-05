<?php
    namespace tz\src\parser;

    interface ParserComponentImpl {
        function parse(string $rawHTML, bool $isServerMode);
    }
?>