<?php

namespace Jawabkom\Backend\Module\Spam\Detection\Contract\Queue;

interface IQueuePusher
{
    public function push(mixed $message):void;
}
