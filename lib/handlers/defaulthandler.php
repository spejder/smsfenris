<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 23:30
 */

require_once 'handler.php';

class DefaultHandler implements Handler
{
    const DEFAULT_RESPONSE_BODY = 'Vi forstår desværre ikke hvad du siger. Prøv igen eller kontakt Fenris-teamet på tlf. 28925598';

    /**
     * @var DBConnection
     */
    private $conn;

    public function handle(Message $m) {

        $this->conn = DBConnection::get();

        $parts = explode(" ", $m->body());
        $funktion = $parts[2];
        array_shift($parts); array_shift($parts); array_shift($parts);
        $besked = implode(" ", $parts);

        if ($this->erBetroet(getDanishPhoneNumber($m->from()))) {
            $personer = $this->findFolkMedFunktion($funktion);

            if ($personer == null)
                $this->errorSMS($m);
            else
                $this->groupSMS($funktion, $personer, $besked, $m->from());

        } else {

            Logger::info($m->from(). " er ikke betroet!");
            $this->errorSMS($m);
        }



    }

    private function errorSMS(Message $m) {
        $response = new Message();
        $response->to($m->from());
        $response->body(self::DEFAULT_RESPONSE_BODY);

        $ms = new MessageSender();
        $ms->send($response);
    }

    private function groupSMS($funktion, $personer, $besked, $afsender) {
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


        $status = new Message();
        $status->to($afsender);
        $status->body("Din besked blev sendt til ". count($receivers). " med funktionen ". $funktion);

        $ms->send($status);
    }

    private function erBetroet($from) {
        $this->conn = DBConnection::get();
        $from = $this->conn->real_escape_string($from);

        $res = $this->conn->query("select * from personer where mobilnummer = $from");
        $obj = $res ? $res->fetch_object() : null;

        return isset($obj) ? $obj->betroet : false;
    }

    private function findFolkMedFunktion($funktion) {

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
