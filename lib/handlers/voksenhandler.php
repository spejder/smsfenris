<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 23:30
 */

require_once 'handler.php';

class VoksenHandler extends Handler
{

    public function handle(Message $m) {

        $this->conn = DBConnection::get();

        $parts = explode(" ", $m->body());
        $funktion = $parts[2];
        array_shift($parts); array_shift($parts); array_shift($parts);
        $besked = implode(" ", $parts);

        if ($this->erBetroet(getDanishPhoneNumber($m->from()))) {
            $personer = $this->findVoksne();

            if ($personer == null)
                $this->errorSMS($m);
            else
                $this->groupSMS($personer, $besked);
                $this->receiptSMS($m->from(), "Din besked blev sendt til ". count($personer). " voksne.");

        } else {

            Logger::info($m->from(). " er ikke betroet!");
            $this->errorSMS($m);
        }
    }

    private function findVoksne() {

        $personer = array();

        Logger::debug("Leder efter folk uden patruljenummer");

        $result = $this->conn->query("select * from personer where patruljenummer IS NULL OR patruljenummer < 1");

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
