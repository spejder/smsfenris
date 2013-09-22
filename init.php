<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 22:17
 */

require_once 'libloader.php';
require_once 'config.php';

LibLoader::load('lib/handlers');
LibLoader::load('lib');

Logger::addSubscriber(new FileSubscriber('fenris.log'));

Logger::info("Initialized Fenris SMS application");
