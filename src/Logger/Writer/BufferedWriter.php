<?php

namespace FiiSoft\Logger\Writer;

use InvalidArgumentException;

final class BufferedWriter implements LogsWriter
{
    /** @var array */
    private $buffer = [];
    
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function write($message, array $context = [])
    {
        $this->buffer[] = [
            'message' => $message,
            'context' => $context,
        ];
    }
    
    /**
     * @param int|null $limit
     * @throws InvalidArgumentException
     * @return array
     */
    public function fetchMessages($limit = null)
    {
        return array_column($this->fetch($limit), 'message');
    }
    
    /**
     * @param int|null $limit
     * @throws InvalidArgumentException
     * @return array
     */
    public function fetch($limit = null)
    {
        if ($limit === null) {
            return array_splice($this->buffer, 0);
        }
        
        if (is_int($limit)) {
            if ($limit >= 0) {
                return array_splice($this->buffer, 0, $limit);
            }
            
            return array_splice($this->buffer, $limit);
        }
        
        throw new InvalidArgumentException('Invalid param limit');
    }
    
    /**
     * @param int|null $limit
     * @throws InvalidArgumentException
     * @return array
     */
    public function getMessages($limit = null)
    {
        return array_column($this->get($limit), 'message');
    }
    
    /**
     * @param int|null $limit
     * @throws InvalidArgumentException
     * @return array
     */
    public function get($limit = null)
    {
        if ($limit === null) {
            return $this->buffer;
        }
    
        if (is_int($limit)) {
            if ($limit >= 0) {
                return array_slice($this->buffer, 0, $limit);
            }
            
            return array_slice($this->buffer, $limit);
        }
        
        throw new InvalidArgumentException('Invalid param limit');
    }
    
    /**
     * @return void
     */
    public function purge()
    {
        $this->buffer = [];
    }
    
    /**
     * @param LogsWriter $writer
     * @return void
     */
    public function flushTo(LogsWriter $writer)
    {
        while (!empty($this->buffer)) {
            $content = $this->buffer[0];
            $writer->write($content['message'], $content['context']);
            array_shift($this->buffer);
        }
    }
}