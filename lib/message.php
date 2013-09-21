<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 22:04
 */

class Message
{
    protected $data = array();

    public function __construct($data) {

    }

    public function logIncomming() {
        $p = &$this->data;
        $conn = DBConnection::get();
        $conn->query("INSERT INTO beskeder (afsender, message, command, appnr, lac) VALUES ('". $p['from']. "', '". $p['message']. "', '". $p['command']. "', '". intval($p['appnr']). "', '". $p['lac']. "')");

    }



}