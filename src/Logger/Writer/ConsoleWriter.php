<?php

namespace FiiSoft\Logger\Writer;

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