<?php

namespace FiiSoft\Logger\SmartLogger;

use Exception;
use FiiSoft\Logger\Writer\LogsWriter;
use RuntimeException;

class DefaultSmartLogger extends AbstractSmartLogger
{
    /** @var DefaultSmartLoggerConfig */
    protected $config;
    
    /** @var LogsWriter */
    private $writer;
    
    /**
     * @param LogsWriter $logsWriter
     * @param DefaultSmartLoggerConfig $config
     */
    public function __construct(LogsWriter $logsWriter, DefaultSmartLoggerConfig $config)
    {
        $this->config = clone $config;
        $this->writer = $logsWriter;
        
        if ($config->levels) {
            $this->setOrderOfLevels($config->levels);
        }
        
        if ($config->minLevel) {
            $this->setMinLevel($config->minLevel);
        }
    }
    
    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @throws RuntimeException
     * @return void
     */
    final public function log($level, $message, array $context = array())
    {
        if (isset($this->levels[$level]) && $this->levels[$level] < $this->minLevel) {
            return;
        }
    
        if (empty($this->context) && empty($context)) {
            $context = [];
        } elseif ($this->context !== $context) {
            $context = array_merge($this->context, $context);
        }
        
        $context['level'] = $level;
        $context['datetime'] = date('Y-m-d H:i:s');
        
        try {
            $this->writer->write($context['datetime'].' ['.$level.'] '.$this->prefix.$message, $context);
        } catch (Exception $e) {
            $this->handleWriteError($e, $message);
        }
    }
    
    /**
     * @param Exception $error
     * @param string $message
     * @throws RuntimeException
     * @return void
     */
    protected function handleWriteError(Exception $error, $message)
    {
        $errorMsg = 'Error during write to LogWriter: ['.$error->getCode().'] '.$error->getMessage()."\n"
            .'Stacktrace:'."\n".$error->getTraceAsString()."\n";
        
        $errorFile = $this->config->errorLogFile;
        if (false === file_put_contents($errorFile, $errorMsg, FILE_APPEND)) {
            throw new RuntimeException(
                'Unable to write info about error to logfile '.$errorFile."\n".'Logged error is: '.$errorMsg
            );
        }
        
        $logFile = $this->config->fallbackLogFile;
        if (false === file_put_contents($logFile, $message."\n", FILE_APPEND)) {
            throw new RuntimeException('Unable to write log to fallback logfile '.$logFile);
        }
    }
}