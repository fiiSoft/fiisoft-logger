<?php

namespace FiiSoft\Tools\Logger\Reader;

use BadMethodCallException;
use FiiSoft\Tools\OutputWriter\OutputWriter;
use InvalidArgumentException;

/**
 * Reads logs from some source and push them to OutputWriter.
 * What is the source of logs is up to concrete implementation.
 *
 * LogsMonitor just allows to redirect logs to provided OutputWriter,
 * with optional filtering by log's level and/or context.
 */
interface LogsMonitor
{
    /**
     * Allows to redefine levels (if different from default).
     * The list of Levels must be ordered from the least to the most significant.
     * Instead of call this method, new levels can be set by pass them as second argument to filterByLevels.
     *
     * @param array $levels
     * @return $this fluent interface
     */
    public function setLevels(array $levels);
    
    /**
     * List of levels this LogsMonitor knows and understands.
     *
     * @return array
     */
    public function getLevels();
    
    /**
     * One can set minimum level of logs that will be streamed to OutputWriter by this LogsMonitor.
     * This is optional and if method is not call, all available logs are supposed to be streamed.
     *
     * If second param is not empty then use this set of levels instead of defaults.
     * The list of Levels must be ordered from the least to the most significant.
     *
     * @param string $level
     * @param array $levels
     * @return $this fluent interface
     */
    public function filterByLevel($level, array $levels = []);
    
    /**
     * One can set context that is required to stream logs to OutputWriter by this LogsMonitor.
     * This is optional and if method is not call, all available logs are supposed to be streamed.
     *
     * @param array $context
     * @return $this fluent interface
     */
    public function filterByContext(array $context);
    
    /**
     * Set OutputWriter where logs will be written by this LogsMonitor.
     * It is required to call this method before logs are streamed to OutputWriter by method start().
     *
     * @param OutputWriter $outputWriter
     * @return $this fluent interface
     */
    public function setOutputWriter(OutputWriter $outputWriter);
    
    /**
     * Start to sending logs to OutputWriter, which has to be set before call this method.
     * If param maxNum is provided, then only given number of messages will be transferred to OutputWriter.
     *
     * @param integer|null $maxNum number of logs to read before return; must be >= 1
     * @param integer $timeout maximum time (in seconds) to wait for any log before return; 0 means "no timeout"
     * @throws BadMethodCallException if OutputWriter was not set before this method is call
     * @throws InvalidArgumentException if param maxNum or timeout is invalid
     * @return void
     */
    public function start($maxNum = null, $timeout = 0);
}