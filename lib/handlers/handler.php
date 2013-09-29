<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 23:17
 */

abstract class Handler
{
    const DEFAULT_RESPONSE_BODY = 'Vi forstår desværre ikke hvad du siger. Prøv igen eller kontakt Fenris-teamet på tlf. 28925598';

    /**
     * @var DBConnection
     */
    protected $conn;
    
    public abstract function handle(Message $m);

    protected function errorSMS(Message $m) {
        $response = new Message();
        $response->to($m->from());
        $response->body(self::DEFAULT_RESPONSE_BODY);

        $ms = new MessageSender();
        $ms->send($response);
    }

    protected function groupSMS($personer, $besked) {
        $response = new Message();

        $receivers = array();
        foreach ($personer as $p) {
            if (getDanishPhoneNumber($p->mobilnummer))
                $receivers[] = $p->mobilnummer;
        }

        $response->to($receivers);
        $response->body($besked);

        $ms = new MessageSender();
        $ms->send($response);
    }

    /**
     * @param $funktion
     * @param $afsender
     * @param $personer
     * @param $ms
     */
    protected function receiptSMS($afsender, $besked)
    {
        $ms = new MessageSender();
        
        $status = new Message();
        $status->to($afsender);
        $status->body($besked);

        $ms->send($status);
    }

    protected function erBetroet($from) {
        $this->conn = DBConnection::get();
        $from = $this->conn->real_escape_string($from);

        $res = $this->conn->query("select * from personer where mobilnummer = $from");
        $obj = $res ? $res->fetch_object() : null;

        return isset($obj) ? $obj->betroet : false;
    }

    protected function findFolkMedFunktion($funktion) {

        $personer = array();

        $funktion = strtolower($this->conn->real_escape_string($funktion));

        Logger::debug("Leder efter folk med funktion: ". $funktion);

        $result = $this->conn->query("select * from personer where LOWER(funktion) = '$funktion'");

        if ($result) {

            /* fetch object array */
            while ($obj = $result->fetch_object()) {
                $personer[] = $obj;
            }

            /* free result set */
            $result->close();
        }

        Logger::debug("Antal personer fundet: ". count($personer));

        Logger::debug("Fandt: ". print_r($personer, true));

        return $personer;
    }
}