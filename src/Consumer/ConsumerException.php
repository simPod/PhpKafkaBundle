<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle\Consumer;

use Exception;
use RdKafka\Message;

final class ConsumerException extends Exception
{
    public static function fromMessage(Message $message) : self
    {
        return new self($message->errstr(), $message->err);
    }
}
