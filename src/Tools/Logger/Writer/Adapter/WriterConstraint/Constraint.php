<?php

namespace FiiSoft\Tools\Logger\Writer\Adapter\WriterConstraint;

interface Constraint
{
    /**
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function allowsToWrite($message, array $context);
}