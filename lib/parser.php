<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 23:04
 */

class MessageParser
{
    /**
     * @var Message
     */
    protected $message;

    protected $parts;

    protected $handlers;


    public function MessageParser(Message $message) {
        $this->message = $message;

        $this->initHandlers();
        $this->parts = explode(' ', $message->body());
    }


    public function handleMessage() {

        $handler = $this->findBestHandler();

        if (!$handler instanceof Handler) {
            $handler = new DefaultHandler();
        }

        $handler->handle($this->message);
    }

    /**
     * @return Handler
     */
    private function findBestHandler() {

        $bestmatch = new stdClass();
        $bestmatch->count = 0;
        $bestmatch->handler = null;

        foreach ($this->handlers as $params=>$handler)
        {
            $ruleParts = explode(" ", $params);
            $messageParts = $this->parts;

            $a = 0;
            while((isset($messageParts[$a]) && isset($ruleParts[$a])) && (strtolower($ruleParts[$a]) == strtolower($messageParts[$a])))
            {
                if ($a > $bestmatch->count)
                {
                    if (count($ruleParts) -1 <= $a)
                    {
                        $bestmatch->count = $a;
                        $bestmatch->handler = $handler;
                    }
                }

                $a++;
            }
        }

        return $bestmatch->handler;
    }

    private function initHandlers() {
        $this->handlers = array(
            'spejder fenris tjek' => new TjekHandler(),
            'spejder fenris voksen' => new VoksenHandler(),
            'spejder fenris voksne' => new VoksenHandler(),
            'spejder fenris slet afstemning' => new SletAfstemningHandler(),
        );
    }


}
