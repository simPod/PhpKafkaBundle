<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Event;

use Symfony\Contracts\EventDispatcher\Event;

final class StoppedConsuming extends Event
{
}
