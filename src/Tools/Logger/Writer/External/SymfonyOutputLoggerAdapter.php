<?php

namespace FiiSoft\Tools\Logger\Writer\External;

use FiiSoft\Tools\Logger\Writer\SmartLogger;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class SymfonyOutputLoggerAdapter implements OutputInterface
{
    /** @var SmartLogger */
    private $logger;
    
    /** @var OutputFormatter */
    private $formatter;
    
    /** @var int */
    private $verbosity;
    
    /** @var int */
    private $types;
    
    /** @var int */
    private $verbosities;
    
    /**
     * @param SmartLogger $logger
     * @param int $verbosity
     * @param bool $decorated
     * @param OutputFormatterInterface|null $formatter
     */
    public function __construct(
        SmartLogger $logger,
        $verbosity = self::VERBOSITY_DEBUG,
        $decorated = false,
        OutputFormatterInterface $formatter = null
    ){
        $this->logger = $logger;
    
        $this->verbosity = $verbosity ?: self::VERBOSITY_DEBUG;
        
        $this->formatter = $formatter ?: new OutputFormatter();
        $this->formatter->setDecorated($decorated);
        
        $this->types = self::OUTPUT_NORMAL | self::OUTPUT_RAW | self::OUTPUT_PLAIN;
        $this->verbosities = self::VERBOSITY_QUIET | self::VERBOSITY_NORMAL | self::VERBOSITY_VERBOSE
                            | self::VERBOSITY_VERY_VERBOSE | self::VERBOSITY_DEBUG;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setFormatter(OutputFormatterInterface $formatter)
    {
        $this->formatter = $formatter;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getFormatter()
    {
        return $this->formatter;
    }
    
    /**
     * {@inheritdoc}
     */
    public function setDecorated($decorated)
    {
        $this->formatter->setDecorated($decorated);
    }
    
    /**
     * {@inheritdoc}
     */
    public function isDecorated()
    {
        return $this->formatter->isDecorated();
    }
    
    /**
     * {@inheritdoc}
     */
    public function setVerbosity($level)
    {
        $this->verbosity = (int) $level;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getVerbosity()
    {
        return $this->verbosity;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isQuiet()
    {
        return self::VERBOSITY_QUIET === $this->verbosity;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isVerbose()
    {
        return self::VERBOSITY_VERBOSE <= $this->verbosity;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isVeryVerbose()
    {
        return self::VERBOSITY_VERY_VERBOSE <= $this->verbosity;
    }
    
    /**
     * {@inheritdoc}
     */
    public function isDebug()
    {
        return self::VERBOSITY_DEBUG <= $this->verbosity;
    }
    
    /**
     * {@inheritdoc}
     */
    public function writeln($messages, $options = self::OUTPUT_NORMAL)
    {
        $this->write($messages, true, $options);
    }
    
    /**
     * Writes a message to the output.
     *
     * @param string|array $messages The message as an array of lines or a single string
     * @param bool         $newline  Whether to add a newline
     * @param int          $options  A bitmask of options (one of the OUTPUT or VERBOSITY constants), 0 is considered the same as self::OUTPUT_NORMAL | self::VERBOSITY_NORMAL
     */
    public function write($messages, $newline = false, $options = self::OUTPUT_NORMAL)
    {
        $verbosity = $this->verbosities & $options ?: self::VERBOSITY_NORMAL;
        if ($verbosity > $this->verbosity) {
            return;
        }
        
        $isVerbosityNormal = $verbosity & self::VERBOSITY_NORMAL;
        $type = $this->types & $options ?: self::OUTPUT_NORMAL;
        
        foreach ((array) $messages as $message) {
            switch ($type) {
                case OutputInterface::OUTPUT_NORMAL:
                    $message = $this->formatter->format($message);
                break;
                case OutputInterface::OUTPUT_RAW:
                break;
                case OutputInterface::OUTPUT_PLAIN:
                    $message = strip_tags($this->formatter->format($message));
                break;
            }
    
            if ($isVerbosityNormal) {
                $this->logger->info($message, ['newLine' => $newline]);
            } else {
                $this->logger->debug($message, ['newLine' => $newline]);
            }
        }
    }
}