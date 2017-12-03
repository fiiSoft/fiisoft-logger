<?php

namespace FiiSoft\Logger\Reader;

interface LogConsumer
{
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function consumeLog($message, array $context = []);
}