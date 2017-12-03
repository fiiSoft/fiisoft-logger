<?php

namespace FiiSoft\Logger\SmartLogger;

use FiiSoft\Logger\SmartLogger;
use Psr\Log\NullLogger as PsrNullLogger;

final class NullLogger extends PsrNullLogger implements SmartLogger
{
    public function setPrefix($prefix)
    {
        return $this;
    }
    
    public function setContext(array $context)
    {
        return $this;
    }
    
    public function setMinLevel($minLevel)
    {
        return $this;
    }
    
    public function setOrderOfLevels(array $levels)
    {
        return $this;
    }
}