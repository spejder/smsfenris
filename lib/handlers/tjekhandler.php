<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 23:17
 */

require_once 'handler.php';

class TjekHandler extends Handler
{

    public function handle(Message $m)
    {
        try
        {
            $number = getDanishPhoneNumber($m->from());
            if ($number == null)
                throw new InvalidArgumentException();

            $personer = $this->findPersoner($number);

            $this->markAsCheckedIn($personer);

            $patruljeNavn = $personer[0]->patruljenavn;
            $this->respond($m->from(), "Nu er $patruljeNavn tjekket ind.");

        } catch (InvalidArgumentException $e) {
            $this->respond($m->from(), "Beklager! Vi kunne ikke finde din patrulje. Du skal være PL, PA eller PM for at tjekke ind.");
        }
    }

    private function respond($receiver, $body) {
        $response = new Message();
        $response->to($receiver);
        $response->body($body);

        $ms = new MessageSender();
        $ms->send($response);
    }

    private function findPersoner($from) {

        $conn = DBConnection::get();
        $from = $conn->real_escape_string($from);

        Logger::info("Leder efter person med mobilnummer '". $from. "'");

        $result = $conn->query("select * from personer where patruljenummer in (select patruljenummer from personer where mobilnummer = $from and (LOWER(funktion) = 'pm' or LOWER(funktion) = 'pl' or LOWER(funktion) = 'pa'))");

        $personer = array();

        if ($result) {

            /* fetch object array */
            while ($obj = $result->fetch_object()) {
                $personer[] = $obj;
            }

            /* free result set */
            $result->close();
        }

        Logger::debug("Antal personer fundet: ". count($personer));

        if (count($personer) < 1) {
            throw new InvalidArgumentException();
        }

        Logger::debug("Fandt: ". print_r($personer, true));

        return $personer;
    }

    private function markAsCheckedIn($personer) {
        $conn = DBConnection::get();

        $clause = '1 != 1';
        foreach ($personer as $p) {
            $clause .= ' or personid = '. $p->personid;
        }

        Logger::debug("Konstrueret where clause: ". $clause);

        $conn->query("update personer set tjekket = '". date('c'). "' where $clause");
        $conn->commit();
    }


}
