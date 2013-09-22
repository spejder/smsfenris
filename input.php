<?php

ini_set('display_errors', 'on');
error_reporting(E_ALL);

require_once 'init.php';

Logger::debug("Inside input.php");

//----------------------------------

$msg = new Message($_GET);

$msg->logIncomming();

$parser = new MessageParser($msg);
$parser->handleMessage();

