<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 23:30
 */

require_once 'handler.php';

class DefaultHandler extends Handler
{

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
            else {
                $this->groupSMS($personer, $besked);
                $this->receiptSMS($m->from(), "Din besked blev sendt til ". count($personer). " med funktionen ". $funktion);
            }

        } else {

            Logger::info($m->from(). " er ikke betroet!");
            $this->errorSMS($m);
        }



    }

}
