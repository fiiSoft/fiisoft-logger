<?php

namespace FiiSoft\Logger\Writer;

interface LogsWriter
{
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function write($message, array $context = []);
}