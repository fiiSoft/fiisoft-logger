<?php

namespace FiiSoft\Tools\Logger\Writer\Adapter;

use FiiSoft\Tools\Logger\Writer\LogsWriter;

final class ConsoleWriter implements LogsWriter
{
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function write($message, array $context = [])
    {
        if (!isset($context['newLine']) || $context['newLine'] !== false) {
            echo $message, PHP_EOL;
        } else {
            echo $message;
        }
    }
}