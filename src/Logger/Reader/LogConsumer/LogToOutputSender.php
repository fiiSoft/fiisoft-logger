<?php

namespace FiiSoft\Logger\Reader\LogConsumer;

use FiiSoft\Logger\Reader\LogConsumer;
use FiiSoft\Tools\OutputWriter\OutputWriter;

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