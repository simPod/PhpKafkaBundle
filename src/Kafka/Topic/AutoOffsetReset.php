<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Topic;

final class AutoOffsetReset
{
    public const EARLIEST = 'earliest';
    public const LATEST   = 'latest';
}
