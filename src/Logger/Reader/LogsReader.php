<?php

namespace FiiSoft\Logger\Reader;

interface LogsReader
{
    /**
     * @param LogConsumer $logConsumer
     * @param integer|null $maxReads number of logs to consume before return
     * @param integer $timeout maximum time (in seconds) to wait for any log before return; 0 means "no timeout"
     * @return mixed
     */
    public function read(LogConsumer $logConsumer, $maxReads = null, $timeout = 0);
}