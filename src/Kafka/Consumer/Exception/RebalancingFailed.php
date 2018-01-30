<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Consumer\Exception;

use Exception;
use function rd_kafka_err2str;
use function sprintf;

final class RebalancingFailed extends Exception
{
    public static function new(int $err) : self
    {
        return new self(sprintf('Rebalancing failed: %s (%d)', rd_kafka_err2str($err), $err));
    }
}
