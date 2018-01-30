<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Consumer\Exception;

use Exception;
use RdKafka\Message;
use function sprintf;

final class IncompatibleStatus extends Exception
{
    public static function fromMessage(Message $message) : self
    {
        return new self(
            sprintf(
                'Consumer status "%d" is not handled: %s',
                $message->err,
                $message->errstr()
            )
        );
    }
}
