<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 23:43
 */

class MessageSender
{
    const SEND_SMS_API = 'http://sms.dds.dk/rest/sendsms';
    const API_KEY = 'fenris2013';
    const SENDER = 'Fenris';

    public function send(Message $m) {

        $data = array(
            'apikey' => self::API_KEY,
            'from' => self::SENDER,
            'to' => $m->to(),
            'message' => utf8_decode($m->body()),
        );

        Logger::info("Sending sms with data: ". print_r($data, true));
        $this->doPost(self::SEND_SMS_API, $data);
    }

    private function doPost($url, $data) {

        // use key 'http' even if you send the request to https://...
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);

        Logger::info("Got result from GW: ". $result);
    }



}