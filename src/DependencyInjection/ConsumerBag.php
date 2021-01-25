<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\DependencyInjection;

use SimPod\KafkaBundle\Kafka\Clients\Consumer\NamedConsumer;

final class ConsumerBag
{
    /** @var array<string, NamedConsumer> */
    private $consumers = [];

    /** @return array<string, NamedConsumer> $consumers */
    public function getConsumers(): array
    {
        return $this->consumers;
    }

    public function addConsumer(NamedConsumer $consumer): void
    {
        $this->consumers[$consumer->getName()] = $consumer;
    }
}
