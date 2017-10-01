<?php

namespace FiiSoft\Test\Tools\Logger\Writer\Adapter;

use FiiSoft\Tools\Logger\Writer\Adapter\ConsoleLogger;

class ConsoleLoggerTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_logs_message_directly_to_console()
    {
        $logger = new ConsoleLogger();
        
        ob_start();
        $logger->error('This is error');
        self::assertSame('This is error'.PHP_EOL, ob_get_clean());
        
        $logger->surroundMessageWithLevelMarkers(true);
    
        ob_start();
        $logger->info('This is info');
        self::assertSame('<info>This is info</info>'.PHP_EOL, ob_get_clean());
    }
    
    public function test_it_does_not_add_level_as_marker_if_log_is_empty()
    {
        $logger = new ConsoleLogger(true);
    
        ob_start();
        $logger->log('', 'Some log message');
        self::assertSame('Some log message'.PHP_EOL, ob_get_clean());
    }
}
