<?php

namespace FiiSoft\Tools\Logger\Writer\Adapter;

use BadMethodCallException;
use FiiSoft\Tools\Logger\Reader\LogsMonitor\InstantLogsMonitor;
use FiiSoft\Tools\Logger\Writer\LogsWriter;
use RuntimeException;

final class InstantSmartLogger extends DefaultSmartLogger implements LogsWriter
{
    /** @var InstantLogsMonitor */
    private $logsMonitor;
    
    /** @var LogsWriter */
    private $logsWriter;
    
    /**
     * @param LogsWriter $logsWriter
     * @param DefaultSmartLoggerConfig $config
     * @param InstantLogsMonitor $logsMonitor
     * @throws RuntimeException
     */
    public function __construct(
        LogsWriter $logsWriter,
        DefaultSmartLoggerConfig $config,
        InstantLogsMonitor $logsMonitor
    ) {
        parent::__construct($this, $config);
    
        $this->logsWriter = $logsWriter;
        $this->logsMonitor = $logsMonitor;
    }
    
    /**
     * @param string $message
     * @param array $context
     * @throws BadMethodCallException
     * @return void
     */
    public function write($message, array $context = [])
    {
        $this->logsWriter->write($message, $context);
        $this->logsMonitor->consumeLog($message, $context);
    }
}