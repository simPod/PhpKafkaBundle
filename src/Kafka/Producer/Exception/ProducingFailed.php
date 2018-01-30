<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Producer\Exception;

use Exception;
use RdKafka\Message;
use function sprintf;

final class ProducingFailed extends Exception
{
    public static function fromMessage(Message $message) : self
    {
        return new self(
            sprintf(
                'Message producing failed with code "%d" and message: %s',
                $message->err,
                $message->errstr()
            )
        );
    }
}
