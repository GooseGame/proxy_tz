<?php

namespace Parser;

interface ParserComponentImpl
{
    public function parse(string $rawHTML);
}