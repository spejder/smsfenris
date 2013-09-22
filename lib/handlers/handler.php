<?php
/**
 * User: jot
 * Date: 21-09-13
 * Time: 23:17
 */

interface Handler
{
    public function handle(Message $m);
}