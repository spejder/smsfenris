<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 23:17
 */

require_once 'handler.php';

class AfstemningHandler extends Handler
{

    public function handle(Message $m)
    {
        try
        {
            $number = getDanishPhoneNumber($m->from());

            if ($number == null)
                throw new Exception();

            $parts = explode(" ", $m->body());
            $vote = $parts[2];

            $vote = $this->parseVote($vote);
            $poll = $this->findActivePoll();

            $this->doVote($poll, $number, $vote, $m);

        } catch (InvalidArgumentException $e) {
            $this->respond($m->from(), "Beklager! Vi kan ikke tage imod din stemme. Skriv spejder fenris [1-10]");
        } catch (Exception $e) {
            $this->respond($m->from(), "Beklager! Der opstod en fejl i forbindelse med behandling af din stemme");
        }
    }


    private function respond($receiver, $body)
    {
        $response = new Message();
        $response->to($receiver);
        $response->body($body);

        $ms = new MessageSender();
        $ms->send($response);
    }

    private function findActivePoll()
    {
        $conn = DBConnection::get();

        Logger::info("Leder efter aktiv afstemning");

        $result = $conn->query("select id from afstemning where afsluttet IS NULL order by id desc limit 1");

        $res = null;

        if ($result) {
            $res = $result->fetch_object();
        }

        if (!$res) {
            $conn->query("insert into afstemning (id) VALUES (null)");
            $id = $conn->getLastInsertId();
            $conn->commit();

            Logger::info("Created new poll with ID: ". $id);
        } else {
            $id = $res->id;
        }

        return $id;
    }

    private function doVote($poll, $from, $vote, Message $m)
    {
        $conn = DBConnection::get();

        Logger::info("Foretager logning af stemme på ". $vote. " - fra: ". $from);

        try
        {
            $conn->query("insert into stemmer (afstemningid, afsender, stemme) VALUES ($poll, '$from', '$vote')");
            $conn->commit();

            $this->respond($m->from(), "Tak for din stemme!");
        } catch (Exception $e) {
            $this->respond($m->from(), "Beklager! Vi kan ikke tage imod din stemme. Er du sikker på du ikke allerede har stemt?");
        }

    }

    private function parseVote($vote)
    {
        if (is_numeric($vote)) {
            $iVote = intval($vote);

            if ($iVote && $iVote >= 1 && $iVote <= 10) {
                return $iVote;
            }
        }

        throw new InvalidArgumentException();
    }

}
