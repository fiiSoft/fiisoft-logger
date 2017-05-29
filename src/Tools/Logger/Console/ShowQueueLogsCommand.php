<?php

namespace FiiSoft\Tools\Logger\Console;

use Exception;
use FiiSoft\Tools\Console\AbstractCommand;
use FiiSoft\Tools\OutputWriter\Adapter\SymfonyConsoleOutputWriter;
use FiiSoft\Tools\Logger\Reader\LogsMonitor;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ShowQueueLogsCommand extends AbstractCommand
{
    /** @var LogsMonitor */
    private $logsMonitor;
    
    /**
     * @param LogsMonitor $logsMonitor
     * @param string $name name of command used in CLI, example: show:logs
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    public function __construct(LogsMonitor $logsMonitor, $name)
    {
        parent::__construct($name);
        $this->logsMonitor = $logsMonitor;
    }
    
    protected function configure()
    {
        $this->setDescription('Display logs messages on the console')
            ->setHelp(
                'Send logs from some source (LogsMonitor) to Symfony\'s OutputInterface (console).'.PHP_EOL
                .'Optionally can show only logs of level equal or greater then given in --level=minLevel'.PHP_EOL
                .'Command class: '.get_class($this)
            );
        
        $this->addOption('level', 'l', InputOption::VALUE_REQUIRED, 'Min level of logs to show');
    }
    
    protected function handleInput(InputInterface $input, OutputInterface $output)
    {
        if (!$input->isInteractive()) {
            $output->writeln('This command cannot operate when no-interaction mode is enabled!');
            exit(1);
        }
    
        if ($this->isQuiet($output)) {
            $output->writeln('This command cannot operate when quiet mode is enabled!');
            exit(2);
        }
        
        $output->writeln('Waiting for logs messages. To exit press CTRL+C');
        
        $minLevel = $input->getOption('level');
        if ($minLevel) {
            $this->writelnV('Only logs with level ' . $minLevel . ' or greater will be shown');
            $this->logsMonitor->filterByLevel($minLevel);
        }
    
        $this->logsMonitor->setOutputWriter(new SymfonyConsoleOutputWriter($output));
    
        try {
            $this->logsMonitor->start();
        } catch (Exception $e) {
            $output->writeln('Reading of logs interrupted: ['.$e->getCode().'] '.$e->getMessage());
        }
    }
}