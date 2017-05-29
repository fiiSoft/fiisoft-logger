<?php

namespace FiiSoft\Tools\Logger\Reader;

interface LogConsumer
{
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function consumeLog($message, array $context = []);
}