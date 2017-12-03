<?php

namespace FiiSoft\Test\Logger\Writer;

use FiiSoft\Logger\Writer\BufferedWriter;
use FiiSoft\Logger\Writer\ConsoleWriter;

class BufferedWriterTest extends \PHPUnit_Framework_TestCase
{
    public function test_it_collects_messages_and_allows_to_get_all_of_some_of_them()
    {
        $writer = new BufferedWriter();
        $messages = ['a', 'b', 'c', 'd', 'e'];
    
        foreach ($messages as $message) {
            $writer->write($message);
        }
        
        self::assertSame([['message' => 'a', 'context' => []]], $writer->get(1));
        
        self::assertSame(['a','b','c','d','e'], array_column($writer->get(), 'message'));
        self::assertSame(['a'], array_column($writer->get(1), 'message'));
        self::assertSame(['a', 'b'], array_column($writer->get(2), 'message'));
        self::assertSame(['e'], array_column($writer->get(-1), 'message'));
        self::assertSame(['d', 'e'], array_column($writer->get(-2), 'message'));
        self::assertSame([], $writer->get(0));
    }
    
    public function test_it_returns_all_messages_and_removes_them_immediately_on_fetch()
    {
        $writer = new BufferedWriter();
        $writer->write('a');
        
        self::assertSame(['a'], array_column($writer->fetch(), 'message'));
        
        self::assertSame([], $writer->get());
    }
    
    public function test_it_removes_all_messages_on_purge()
    {
        $writer = new BufferedWriter();
        $writer->write('a');
    
        $writer->purge();
        
        self::assertSame([], $writer->get());
    }
    
    public function test_it_allows_to_fetch_only_limited_number_of_messages()
    {
        $writer = new BufferedWriter();
        $messages = ['a', 'b', 'c', 'd', 'e'];
    
        foreach ($messages as $message) {
            $writer->write($message);
        }

        self::assertSame([], $writer->fetch(0));
        self::assertSame(['a'], array_column($writer->fetch(1), 'message'));
        self::assertSame(['e'], array_column($writer->fetch(-1), 'message'));
        self::assertSame(['b', 'c'], array_column($writer->fetch(2), 'message'));
        self::assertSame(['d'], array_column($writer->fetch(-2), 'message'));
    
        self::assertSame([], $writer->fetch());
    }
    
    public function test_it_returns_messages_directly_without_context_by_dedicated_methods()
    {
        $writer = new BufferedWriter();
        $messages = ['a', 'b', 'c', 'd', 'e'];
        
        foreach ($messages as $message) {
            $writer->write($message);
        }
        
        self::assertSame($messages, $writer->getMessages());
        self::assertSame($messages, $writer->fetchMessages());
     
        self::assertEmpty($writer->get());
    }
    
    public function test_it_can_flush_all_buffered_messages_to_another_writer()
    {
        $messages = ['a', 'b', 'c', 'd', 'e'];
        
        $writer = new BufferedWriter();
        foreach ($messages as $message) {
            $writer->write($message);
        }
        
        ob_start();
        $writer->flushTo(new ConsoleWriter());
        $expected = implode(PHP_EOL, $messages) . PHP_EOL;
        self::assertSame($expected, ob_get_clean());
    }
}
