<?php

namespace FiiSoft\Tools\Logger\Writer\Adapter;

use FiiSoft\Tools\Logger\Writer\AbstractSmartLogger;

final class ConsoleLogger extends AbstractSmartLogger
{
    /** @var bool */
    private $addLeveLMarkersToMessage;
    
    /**
     * @param bool $surroundMessageWithLevelMarkers
     */
    public function __construct($surroundMessageWithLevelMarkers = false)
    {
        $this->surroundMessageWithLevelMarkers($surroundMessageWithLevelMarkers);
    }
    
    /**
     * @param bool $bool
     * @return void
     */
    public function surroundMessageWithLevelMarkers($bool)
    {
        $this->addLeveLMarkersToMessage = (bool) $bool;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, array $context = array())
    {
        if ($this->addLeveLMarkersToMessage && !empty($level)) {
            echo '<',$level,'>',$message,'</',$level,'>',PHP_EOL;
        } else {
            echo $message,PHP_EOL;
        }
    }
}