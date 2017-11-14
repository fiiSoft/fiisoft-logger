<?php

namespace FiiSoft\Tools\Logger\Reader\LogConsumer;

use FiiSoft\Tools\OutputWriter\OutputWriter;
use FiiSoft\Tools\Logger\Reader\LogConsumer;

final class LogToOutputSender implements LogConsumer
{
    /** @var OutputWriter */
    private $output;
    
    /**
     * @param OutputWriter $output
     */
    public function __construct(OutputWriter $output)
    {
        $this->output = $output;
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function consumeLog($message, array $context = [])
    {
        $this->output->normal($message, !isset($context['newLine']) || $context['newLine'] !== false);
    }
}