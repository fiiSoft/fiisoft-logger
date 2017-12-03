<?php

namespace FiiSoft\Logger\Reader\LogConsumer;

use FiiSoft\Logger\Reader\LogConsumer;
use FiiSoft\Tools\OutputWriter\Adapter\NullOutputWriter;
use FiiSoft\Tools\OutputWriter\OutputWriter;
use Psr\Log\LogLevel;
use RuntimeException;

final class ConsumerFilteredByLevel implements LogConsumer
{
    /** @var array */
    private $levels = [
        LogLevel::EMERGENCY,
        LogLevel::ALERT,
        LogLevel::CRITICAL,
        LogLevel::ERROR,
        LogLevel::WARNING,
        LogLevel::NOTICE,
        LogLevel::INFO,
        LogLevel::DEBUG,
    ];
    
    /** @var bool used in xor to decide if message should be passed or filtered out */
    private $disallow;
    
    /** @var bool */
    private $filterEnabled = false;
    
    /** @var LogConsumer */
    private $consumer;
    
    /** @var OutputWriter */
    private $output;
    
    /**
     * @param LogConsumer $consumer
     * @param string $minLevel minimal level of messages consumed by Consumer
     * @param array $levels optional, allows to change set of recognized levels
     * @param OutputWriter $output optional, used for debug messages only
     */
    public function __construct(LogConsumer $consumer, $minLevel, array $levels = [], OutputWriter $output = null)
    {
        $this->consumer = $consumer;
        $this->output = $output ?: new NullOutputWriter();
    
        if (!empty($levels)) {
            $this->levels = $levels;
        }
        
        $this->setUp($minLevel);
    }
    
    /**
     * @param string $minLevel
     * @return void
     */
    private function setUp($minLevel)
    {
        $this->debug('minLevel: '.$minLevel);
        
        $found = array_search($minLevel, $this->levels, true);
    
        if ($found !== false && $found < (count($this->levels) - 1)) {
            $this->debug('found level: '.$found);
        
            $allow = $found + 1 < (count($this->levels) / 2);
            if ($allow) {
                $this->levels = array_slice($this->levels, 0, $found + 1);
            } else {
                $this->levels = array_slice($this->levels, $found + 1);
            }
        
            $this->filterEnabled = true;
            $this->disallow = !$allow;
            
            $this->debug('mode: ' . ($allow ? 'allow' : 'deny'));
            $this->debug('filtering enabled');
        } else {
            $this->debug('filtering disabled');
        }
    }
    
    /**
     * @param string $message
     * @param array $context
     * @throws RuntimeException
     * @return void
     */
    public function consumeLog($message, array $context = [])
    {
        if ($this->filterEnabled) {
            if (isset($context['level'])) {
                if (in_array($context['level'], $this->levels, true) XOR $this->disallow) {
                    $this->consumer->consumeLog($message, $context);
                }
            } else {
                throw new RuntimeException('There is no key "level" in context');
            }
        } else {
            $this->consumer->consumeLog($message, $context);
        }
    }
    
    /**
     * @param string $message
     * @return void
     */
    private function debug($message)
    {
        $this->output->debug('ConsumerFilteredByLevel: '.$message);
    }
}