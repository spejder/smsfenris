<?php

include_once('subscriber.php');

class FileSubscriber extends LoggingSubscriber 
{
	protected $filehandle;
	protected $logfilePath;
	
	public function FileSubscriber($logfilePath, $level = null, $exact = null) {
		parent::LoggingSubscriber($level, $exact);

        $this->logfilePath = $logfilePath;
        $this->filehandle = fopen($logfilePath, 'a');
	}

	public function write($message) {
		$this->filehandle = (isset($this->filehandle)) ? $this->filehandle : fopen($this->logfilePath, 'a');
		fwrite($this->filehandle, $message. $this->lineTerminator);
	}

	public function __toString() {
		return "File ($this->logfilePath)";
	}

	public function finalize() {
		if (is_resource($this->filehandle)) {
			fwrite($this->filehandle, $this->lineTerminator);
			fclose($this->filehandle);	
		}
	}
	
}