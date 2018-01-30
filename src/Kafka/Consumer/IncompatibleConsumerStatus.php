<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Consumer;

use Exception;
use RdKafka\Message;
use function sprintf;

final class IncompatibleConsumerStatus extends Exception
{
    public static function fromMessage(Message $message) : self
    {
        return new self(sprintf('%d: %s', $message->err, $message->errstr()), $message->err);
    }
}
