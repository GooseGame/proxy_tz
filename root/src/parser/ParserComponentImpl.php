<?php
    namespace Parser;

    interface ParserComponentImpl {
        function parse(string $rawHTML, bool $isServerMode);
    }
?>