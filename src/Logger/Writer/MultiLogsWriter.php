<?php

namespace FiiSoft\Logger\Writer;

use Exception;

final class MultiLogsWriter implements LogsWriter
{
    /** @var LogsWriter[] */
    private $writers = [];
    
    /**
     * @param LogsWriter[] $writers
     */
    public function __construct(array $writers = [])
    {
        $this->setWriters($writers);
    }
    
    /**
     * @param LogsWriter[] $writers
     * @return $this fluent interface
     */
    public function setWriters(array $writers)
    {
        $this->writers = [];
        $this->addWriters($writers);
        
        return $this;
    }
    
    /**
     * @param LogsWriter[] $writers
     * @return $this fluent interface
     */
    public function addWriters(array $writers)
    {
        foreach ($writers as $writer) {
            $this->addWriter($writer);
        }
        
        return $this;
    }
    
    /**
     * @param LogsWriter $writer
     * @return $this fluent interface
     */
    public function addWriter(LogsWriter $writer)
    {
        $this->writers[] = $writer;
        return $this;
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function write($message, array $context = [])
    {
        $errors = [];
        $workingWriters = [];
        
        foreach ($this->writers as $key => $writer) {
            try {
                $writer->write($message, $context);
                $workingWriters[] = $writer;
            } catch (Exception $e) {
                $errors[] = 'Write message error! Message: "'.$message.'". Writer: '.get_class($writer)
                    .'. Exception: ['.$e->getCode().'] '.$e->getMessage()
                    .'. Stacktrace: '.$e->getTraceAsString();
            }
        }
    
        foreach ($errors as $error) {
            foreach ($workingWriters as $writer) {
                try {
                    $writer->write($error, ['source' => 'MultiWriter', 'level' => 'alert']);
                    break;
                } catch (Exception $e) {
                    //noop
                }
            }
        }
    }
}