<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Event;

use Symfony\Component\EventDispatcher\Event;

final class ReachedEndOfPartition extends Event
{
    public const NAME = 'kafka__reached_end_of_partition';
}
