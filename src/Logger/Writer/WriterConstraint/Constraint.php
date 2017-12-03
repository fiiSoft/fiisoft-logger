<?php

namespace FiiSoft\Logger\Writer\WriterConstraint;

interface Constraint
{
    /**
     * @param string $message
     * @param array $context
     * @return bool
     */
    public function allowsToWrite($message, array $context);
}