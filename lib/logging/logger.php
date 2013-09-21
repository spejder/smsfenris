<?php

class Logger
{	
	protected static $subscribers = array();
	
	protected function Logger() { }
	
	public static function addSubscriber(LoggingSubscriber $s) {
		self::$subscribers[$s->__toString()] = $s;
		self::daemon($s->__toString(). " subscribed for logging");
	}

    public static function clearSubscribers() {
        self::$subscribers = array();
        self::daemon("Cleared loggging subscribers");
    }

	protected static function write($level, $message) {
	
		$timestamp = date("Y-m-d H:i:s");

		foreach (self::$subscribers as $s) {
			
			if ($s->getLevel() >= $level || $s->getExact() == $level)
				$s->write($timestamp. ' ('. self::getCaller(). ') '. $message);
        }
	}
	
	
	public static function error($message) {
		self::write(LOG_ERR, "[error]: $message");		
	}
	
	public static function warning($message) {
		self::write(LOG_WARNING, "[warning]: $message");		
	}
	
	public static function notice($message) {
		self::write(LOG_NOTICE, "[notice]: $message");		
	}
	
	public static function info($message) {
		self::write(LOG_INFO, "[info]: $message");		
	}
	
	public static function debug($message) {
		self::write(LOG_DEBUG, "[debug]: $message");
	}
	
	public static function daemon($message) {
		self::write(LOG_DAEMON, "[daemon]: $message");
	}
	
	
	
	public static function finalize() {
		foreach (self::$subscribers as $s) {
			$s->finalize();
		}
	}

    private static function getCaller() {

        $trace = debug_backtrace();
        $file = $trace[2]['file'];

        //If the AppPath constant is defined, we can shorten the file path in the log files
        return defined('AppPath') ? str_replace(AppPath, '', $file) : $file;
    }

}