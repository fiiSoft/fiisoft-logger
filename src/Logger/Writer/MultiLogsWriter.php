<?php

namespace FiiSoft\Logger\Writer;

use Exception;

final class MultiLogsWriter implements LogsWriter
{
    /** @var WriterItem[] */
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
        if ($writer instanceof WriterItem) {
            $this->writers[] = $writer;
        } elseif ($writer instanceof AutoDelayedWriter) {
            $this->writers[] = $writer->getWriterItem();
        } else {
            $this->writers[] = new WriterItem($writer);
        }
        
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
        $time = null;
        
        foreach ($this->writers as $item) {
            try {
                $item->write($message, $context);
            } catch (Exception $e) {
                $item->writerIsDead();
    
                $errors[] = 'Write message error! Message: "'.$message.'"
                    . Writer: '.get_class($item->getWriter())
                    .'. Exception: ['.$e->getCode().'] '.$e->getMessage()
                    .'. Stacktrace: '.$e->getTraceAsString();
            }
        }
    
        foreach ($errors as $error) {
            foreach ($this->writers as $item) {
                if ($item->writeIfAlive($error, ['source' => 'MultiWriter', 'level' => 'alert'])) {
                    break;
                }
            }
        }
    }
}