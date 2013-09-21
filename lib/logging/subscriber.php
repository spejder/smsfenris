<?php

abstract class LoggingSubscriber
{
	protected $loglevel;
	protected $logexact;
    protected $lineTerminator = PHP_EOL;
	
	public function LoggingSubscriber($loglevel = null, $logexact = null) {
		
		$this->loglevel = isset($loglevel) ? $loglevel : DEFAULT_LOG_LEVEL;
		$this->logexact = $logexact;
	}
	
	public function getLevel() {
		return $this->loglevel;
	}
	
	public function getExact() {
		return $this->logexact;
	}

    public function setLineTerminator($chars) {
        $this->lineTerminator = $chars;
    }
	
	
	abstract function write($message);
	
	abstract function __toString();
	
	public function finalize() {  }
	
	public function __destruct() {
		$this->finalize();	
	}
}