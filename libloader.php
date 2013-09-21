<?php
class LibLoader
{
	protected $recursive;
		
	private function LibLoader($recursive) {
		$this->recursive = $recursive;
	}
	
	
	/**
	 * Includes all PHP-files in a library
	 *
	 * @param string Path to scan for PHP-files
	 * @param bool[optional] if true, the scan with recurse subdirectories
	 */
	public static function load($libraryPath, $runRecursive = true) {
		$loader = new LibLoader($runRecursive);
		$loader->run(new DirectoryIterator($libraryPath));
	}
	
	private function run(DirectoryIterator $dir) {
		foreach ($dir as $entry) {

			if ($entry->isDot()) continue;
			if ($this->recursive && $entry->isDir()) $this->run(new DirectoryIterator($entry->getPathName()));
			if ($entry->isFile() && end(explode(".", $entry->getBasename())) == 'php' && $entry->isReadable()) {
                include_once($entry->getPathName());
            }
		}
	}
	
}