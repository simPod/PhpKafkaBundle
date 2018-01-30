<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Event;

use Symfony\Component\EventDispatcher\Event;

final class StoppedConsuming extends Event
{
    public const NAME = 'kafka__stopped_consuming';
}
