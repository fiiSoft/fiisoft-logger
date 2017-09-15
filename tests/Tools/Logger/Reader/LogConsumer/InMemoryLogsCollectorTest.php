<?php

namespace FiiSoft\Test\Tools\Logger\Reader\LogConsumer;

use FiiSoft\Tools\Logger\Reader\LogConsumer\InMemoryLogsCollector;

class InMemoryLogsCollectorTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_can_collect_consumed_logs()
    {
        $messages = [
            'ala', 'ma', 'kota', 'a', 'kot', 'ma', 'psa',
        ];
        
        $consumer = new InMemoryLogsCollector();
        foreach ($messages as $message) {
            $consumer->consumeLog($message);
        }
        
        self::assertSame(count($messages), $consumer->count());
        
        self::assertSame($messages, array_map(function ($log) {
            return $log['message'];
        }, $consumer->getCollectedLogs()));
    
        self::assertSame($messages, $consumer->getCollectedMessages());
        
        $consumer->clear();
        
        self::assertSame(0, $consumer->count());
        self::assertSame([], $consumer->getCollectedLogs());
        self::assertSame([], $consumer->getCollectedMessages());
    }
}
