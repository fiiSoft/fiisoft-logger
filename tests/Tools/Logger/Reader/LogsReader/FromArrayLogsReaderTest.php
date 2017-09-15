<?php

namespace FiiSoft\Test\Tools\Logger\Reader\LogsReader;

use FiiSoft\Tools\Logger\Reader\LogConsumer\InMemoryLogsCollector;
use FiiSoft\Tools\Logger\Reader\LogsReader\FromArrayLogsReader;

class FromArrayLogsReaderTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_can_read_logs_from_prepared_list_of_logs()
    {
        $reader = new FromArrayLogsReader([
            'ala', 'ma', 'kota', 'a', 'kot', 'ma', 'psa',
        ]);
    
        self::assertSame(7, $reader->count());
        
        $logConsumer = new InMemoryLogsCollector();
        $reader->read($logConsumer, 5, 1);
        
        self::assertSame(2, $reader->count());
        self::assertSame(5, $logConsumer->count());
        self::assertSame(['ala', 'ma', 'kota', 'a', 'kot'], $logConsumer->getCollectedMessages());
        
        $logConsumer->clear();
        $reader->read($logConsumer, null, 1);
    
        self::assertSame(0, $reader->count());
        self::assertSame(2, $logConsumer->count());
        self::assertSame(['ma', 'psa'], $logConsumer->getCollectedMessages());
    }
}
