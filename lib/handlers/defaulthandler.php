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

    public function handle(Message $m) {
        $response = new Message();
        $response->to($m->from());
        $response->body(self::DEFAULT_RESPONSE_BODY);

        $ms = new MessageSender();
        $ms->send($response);
    }
}
