<?php

namespace FiiSoft\Logger\Writer;

use Exception;

final class AutoDelayedWriter implements LogsWriter
{
    /** @var WriterItem */
    private $writer;
    
    /**
     * @param LogsWriter $writer
     * @param int|null $initialDelay in seconds or null to use default
     */
    public function __construct(LogsWriter $writer, $initialDelay = null)
    {
        if ($writer instanceof WriterItem) {
            $this->writer = $writer;
        } elseif ($writer instanceof AutoDelayedWriter) {
            $this->writer = $writer->getWriterItem();
        } else {
            $this->writer = new WriterItem($writer, $initialDelay);
        }
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function write($message, array $context = [])
    {
        try {
            $this->writer->write($message, $context);
        } catch (Exception $e) {
            $this->writer->writerIsDead();
        }
    }
    
    /**
     * @return LogsWriter
     */
    public function getWriter()
    {
        return $this->writer->getWriter();
    }
    
    /**
     * @return WriterItem
     */
    public function getWriterItem()
    {
        return $this->writer;
    }
}