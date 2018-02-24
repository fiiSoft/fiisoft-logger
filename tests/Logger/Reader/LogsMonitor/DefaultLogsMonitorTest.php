<?php

namespace FiiSoft\Test\Logger\Reader\LogsMonitor;

use FiiSoft\Logger\Reader\LogsMonitor\DefaultLogsMonitor;
use FiiSoft\Logger\Reader\LogsReader\FromArrayLogsReader;
use FiiSoft\Tools\OutputWriter\Adapter\BufferedOutputWriter;

class DefaultLogsMonitorTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_can_push_messages_to_output_writer()
    {
        $logsReader = new FromArrayLogsReader([
            'ala', 'ma', 'kota', 'a', 'kot', 'ma', 'psa',
        ]);
        
        $outputWriter = new BufferedOutputWriter();
        
        $monitor = new DefaultLogsMonitor($logsReader);
        $monitor->setOutputWriter($outputWriter);
        $monitor->start(3);
        
        self::assertSame(3, $outputWriter->count());
        self::assertSame(['ala', 'ma', 'kota'], $outputWriter->getBufferedMessages());
    }
    
    public function test_it_can_forward_messages_with_custom_levels()
    {
        $logsLevels = ['fifth', 'fourth', 'third', 'second', 'first'];
    
        $logs = [
            'ala',
            [
                'message' => 'ma',
                'level' => 'third',
            ],
            'kota',
            'a',
            [
                'message' => 'kot',
                'level' => 'fourth',
            ],
            'ma',
            [
                'message' => 'psa',
                'level' => 'fifth',
            ]
        ];
        
        $logsReader = new FromArrayLogsReader($logs, 'first');
    
        $outputWriter = new BufferedOutputWriter();
    
        $monitor = new DefaultLogsMonitor($logsReader, $logsLevels);
        $monitor->setOutputWriter($outputWriter);
        $monitor->filterByLevel('third');
        $monitor->start(count($logs));
    
        self::assertSame(3, $outputWriter->count());
        self::assertSame(['ma', 'kot', 'psa'], $outputWriter->getBufferedMessages());
    }
    
    public function test_it_can_forward_messages_with_particular_level()
    {
        $logsLevels = ['fifth', 'fourth', 'third', 'second', 'first'];
    
        $logs = [
            'ala',
            [
                'message' => 'ma',
                'level' => 'third',
            ],
            'kota',
            'a',
            [
                'message' => 'kot',
                'level' => 'fourth',
            ],
            'ma',
            [
                'message' => 'psa',
                'level' => 'fifth',
            ]
        ];
    
        $logsReader = new FromArrayLogsReader($logs, 'first');
    
        $outputWriter = new BufferedOutputWriter();
    
        $monitor = new DefaultLogsMonitor($logsReader, $logsLevels);
        $monitor->setOutputWriter($outputWriter);
        $monitor->filterByLevel('fourth', ['fourth']);
        $monitor->start(count($logs));
    
        self::assertSame(1, $outputWriter->count());
        self::assertSame(['kot'], $outputWriter->getBufferedMessages());
    }
}
