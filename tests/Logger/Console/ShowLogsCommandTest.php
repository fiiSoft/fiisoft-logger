<?php

namespace FiiSoft\Test\Logger\Console;

use FiiSoft\Logger\Console\ShowLogsCommand;
use FiiSoft\Logger\Reader\LogsMonitor\NullLogsMonitor;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

class ShowLogsCommandTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_can_show_available_levels_of_logs()
    {
        $output = new BufferedOutput();
    
        $logsMonitor = new NullLogsMonitor();
        $logsMonitor->setLevels(['level1', 'level2', 'level3', 'level4']);
        
        $cmd = new ShowLogsCommand($logsMonitor);
        $cmd->run(new StringInput('--show-levels'), $output);
        
        self::assertContains('Available levels of logs: level1,level2,level3,level4', $output->fetch());
    }
}
