<?php

namespace FiiSoft\Logger\SmartLogger;

use FiiSoft\Logger\Reader\LogsMonitor\InstantLogsMonitor;
use FiiSoft\Logger\Writer\LogsWriter;

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
     * @return void
     */
    public function write($message, array $context = [])
    {
        $this->logsWriter->write($message, $context);
        $this->logsMonitor->consumeLog($message, $context);
    }
}