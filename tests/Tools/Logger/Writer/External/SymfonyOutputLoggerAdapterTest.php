<?php

namespace FiiSoft\Test\Tools\Logger\Writer\External;

use FiiSoft\Tools\Logger\Writer\Adapter\BufferedWriter;
use FiiSoft\Tools\Logger\Writer\Adapter\ConsoleLogger;
use FiiSoft\Tools\Logger\Writer\Adapter\DefaultSmartLogger;
use FiiSoft\Tools\Logger\Writer\Adapter\DefaultSmartLoggerConfig;
use FiiSoft\Tools\Logger\Writer\External\SymfonyOutputLoggerAdapter;
use Symfony\Component\Console\Output\OutputInterface;

class SymfonyOutputLoggerAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_sends_messages_to_logger()
    {
        $output = new SymfonyOutputLoggerAdapter(new ConsoleLogger());
        
        ob_start();
        $output->write('without new line');
        self::assertSame('without new line', ob_get_clean());
        
        ob_start();
        $output->writeln('with new line');
        self::assertSame('with new line'.PHP_EOL, ob_get_clean());
    }
    
    public function test_it_should_not_send_anything_to_logger_when_is_quiet()
    {
        $output = new SymfonyOutputLoggerAdapter(new ConsoleLogger(), OutputInterface::VERBOSITY_QUIET);
        
        ob_start();
        $output->write('this should not appear');
        self::assertSame('', ob_get_clean());
    }
    
    public function test_it_sends_messages_as_info_or_debug_depends_on_verbosity_level()
    {
        //given
        $config = new DefaultSmartLoggerConfig();
        $config->levels = ['error', 'info', 'debug'];
        $config->minLevel = 'info';
    
        $writer = new BufferedWriter();
        $logger = new DefaultSmartLogger($writer, $config);
        $output = new SymfonyOutputLoggerAdapter($logger);
        
        //when
        $output->write('this should be send to writer');
        
        //then
        $messages = $writer->fetchMessages();
        self::assertCount(1, $messages);
        self::assertContains('this should be send to writer', $messages[0]);
        
        //when
        $output->write('this should not be send to writer', false, OutputInterface::VERBOSITY_VERBOSE);
        
        //then
        $messages = $writer->fetchMessages();
        self::assertEmpty($messages);
        
        //when
        $logger->setMinLevel('debug');
        $output->write('but now it should be send to writer', false, OutputInterface::VERBOSITY_VERBOSE);
        
        //then
        $messages = $writer->fetchMessages();
        self::assertCount(1, $messages);
        self::assertContains('but now it should be send to writer', $messages[0]);
    }
}
