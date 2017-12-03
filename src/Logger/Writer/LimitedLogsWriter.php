<?php

namespace FiiSoft\Logger\Writer;

use Exception;
use FiiSoft\Logger\Writer\WriterConstraint\Constraint;

final class LimitedLogsWriter implements LogsWriter
{
    /** @var LogsWriter */
    private $logsWriter;
    
    /** @var bool */
    private $suppressErrors;
    
    /** @var Constraint[] */
    private $constraints;
    
    /**
     * @param LogsWriter $logsWriter
     * @param Constraint[] $constraints
     * @param bool $supressErrors
     */
    public function __construct(LogsWriter $logsWriter, array $constraints = [], $supressErrors = false)
    {
        $this->logsWriter = $logsWriter;
        $this->suppressErrors = (bool) $supressErrors;
        
        $this->setConstraints($constraints);
    }
    
    /**
     * @param Constraint[] $constraints
     * @return $this fluent interface
     */
    public function setConstraints(array $constraints)
    {
        $this->constraints = [];
        $this->addConstraints($constraints);
        
        return $this;
    }
    
    /**
     * @param Constraint[] $constraints
     * @return $this fluent interface
     */
    public function addConstraints(array $constraints)
    {
        foreach ($constraints as $constraint) {
            $this->addConstraint($constraint);
        }
        
        return $this;
    }
    
    /**
     * @param Constraint $constraint
     * @return $this fluent interface
     */
    public function addConstraint(Constraint $constraint)
    {
        $this->constraints[] = $constraint;
        return $this;
    }
    
    /**
     * @param bool $bool
     * @return $this fluent interface
     */
    public function suppressErrors($bool)
    {
        $this->suppressErrors = (bool) $bool;
        return $this;
    }
    
    /**
     * @param string $message
     * @param array $context
     * @return void
     */
    public function write($message, array $context = [])
    {
        $isWriteAllowed = true;
        foreach ($this->constraints as $constraint) {
            $isWriteAllowed = $constraint->allowsToWrite($message, $context) && $isWriteAllowed;
        }
    
        if ($isWriteAllowed) {
            if ($this->suppressErrors) {
                try {
                    @ $this->logsWriter->write($message, $context);
                } catch (Exception $e) {
                    //noop
                }
            } else {
                $this->logsWriter->write($message, $context);
            }
        }
    }
}