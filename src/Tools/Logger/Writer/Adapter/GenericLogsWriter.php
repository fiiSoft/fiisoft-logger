<?php

namespace FiiSoft\Tools\Logger\Writer\Adapter;

use Closure;
use FiiSoft\Tools\Logger\Writer\LogsWriter;

final class GenericLogsWriter implements LogsWriter
{
    /** @var Closure */
    private $func;
    
    /**
     * @param Closure $func this function will get two parameters: message (string) and context (array)
     */
    public function __construct(Closure $func)
    {
        $this->func = $func;
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function write($message, array $context = [])
    {
        $func = $this->func;
        $func($message, $context);
    }
}