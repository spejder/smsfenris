<?php

Logger::debug("Inside input.php");

$msg = new Message($_GET);

$msg->logIncomming();
