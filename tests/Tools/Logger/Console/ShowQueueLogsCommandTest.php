<?php

namespace FiiSoft\Test\Tools\Logger\Console;

use FiiSoft\Tools\Logger\Console\ShowQueueLogsCommand;
use FiiSoft\Tools\Logger\Reader\LogsMonitor\NullLogsMonitor;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ShowQueueLogsCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_can_show_available_levels_of_logs()
    {
        $output = new BufferedOutput();
    
        $logsMonitor = new NullLogsMonitor();
        $logsMonitor->setLevels(['level1', 'level2', 'level3', 'level4']);
        
        $cmd = new ShowQueueLogsCommand($logsMonitor);
        $cmd->run(new StringInput('--show-levels'), $output);
        
        self::assertContains('Available levels of logs: level1,level2,level3,level4', $output->fetch());
    }
}
