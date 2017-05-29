<?php

namespace FiiSoft\Tools\Logger\Writer\Adapter;

use FiiSoft\Tools\Configuration\AbstractConfiguration;

final class DefaultSmartLoggerConfig extends AbstractConfiguration
{
    /**
     * @var array log levels in order from the most to the least important
     */
    public $levels = [];
    
    /**
     * @var string min level of logged messages
     */
    public $minLevel;
    
    /**
     * @var string path to file where messages about errors will be stored
     */
    public $errorLogFile;
    
    /**
     * @var string path to file where logs will be stored if write to LogWriter failes
     */
    public $fallbackLogFile;
}