<?php

namespace FiiSoft\Logger\Console;

use Exception;
use FiiSoft\Logger\Reader\LogsMonitor;
use FiiSoft\Tools\Console\AbstractCommand;
use FiiSoft\Tools\OutputWriter\Adapter\SymfonyConsoleOutputWriter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

final class ShowLogsCommand extends AbstractCommand
{
    /** @var LogsMonitor */
    private $logsMonitor;
    
    /**
     * @param LogsMonitor $logsMonitor
     * @param string|null $name optional name of command used in CLI, by default is: show:logs
     */
    public function __construct(LogsMonitor $logsMonitor, $name = null)
    {
        parent::__construct($name ?: 'show:logs');
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
        $this->addOption('show-levels', 's', InputOption::VALUE_NONE, 'Show list of available levels of logs');
        $this->addOption('only', 'o', InputOption::VALUE_NONE, 'Show only logs with level determined by option --level');
    }
    
    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function handleInput(InputInterface $input, OutputInterface $output)
    {
        if ($this->isQuiet($output)) {
            $output->writeln('This command cannot operate when quiet mode is enabled!');
            return 2;
        }
    
        if ($input->getOption('show-levels')) {
            $this->writeln('Available levels of logs: '.implode(',', $this->logsMonitor->getLevels()));
            return 0;
        }
        
        $minLevel = $input->getOption('level');
        $onlyLevel = $input->getOption('only');
        
        $output->writeln('Waiting for logs messages. To exit press CTRL+C');
        
        if ($minLevel) {
            if ($onlyLevel) {
                $this->logsMonitor->setLevels([$minLevel]);
                $this->writelnV('Only logs with level ' . $minLevel . ' will be shown');
            } else {
                $this->writelnV('Only logs with level ' . $minLevel . ' or greater will be shown');
            }
            
            $this->logsMonitor->filterByLevel($minLevel);
        } elseif ($onlyLevel) {
            $this->writeln('Option --only without --level has no sense');
        }
    
        $this->logsMonitor->setOutputWriter(new SymfonyConsoleOutputWriter($output));
    
        try {
            $this->logsMonitor->start();
        } catch (Exception $e) {
            $output->writeln('Reading of logs interrupted: ['.$e->getCode().'] '.$e->getMessage());
        }
        
        return 0;
    }
}