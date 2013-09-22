<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 22:04
 */

class Message
{
    protected $data;

    public function Message($data = array()) {
        $this->data = $data;

        $this->data['message'] = utf8_encode($this->data['message']);
    }

    public function logIncomming() {
        $p = &$this->data;

        Logger::debug(print_r($p, true));

        $conn = DBConnection::get();
        $p['message'] = $conn->real_escape_string($p['message']);
        $conn->query("INSERT INTO beskeder (afsender, message, command, appnr, lac) VALUES ('". $p['from']. "', '". $p['message']. "', '". $p['command']. "', '". intval($p['appnr']). "', '". $p['lac']. "')");
        $conn->commit();
    }

    public function ___get($propName) {
        return $this->data[$propName];
    }

    public function from($from = null) {
        if (isset($from))
            $this->data['from'] = $from;

        return $this->data['from'];
    }

    public function to($to = null) {
        if (isset($to))
            $this->data['to'] = $to;

        return $this->data['to'];
    }

    public function body($message = null) {
        if (isset($message))
            $this->data['message'] = $message;

        return $this->data['message'];
    }


}