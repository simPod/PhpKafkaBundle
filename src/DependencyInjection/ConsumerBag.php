<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\DependencyInjection;

use SimPod\KafkaBundle\Kafka\Consumer\Consumer;

class ConsumerBag
{
    /** @var Consumer[] */
    private $consumers = [];

    /** @return Consumer[] $consumers */
    public function getConsumers() : array
    {
        return $this->consumers;
    }

    public function addConsumer(Consumer $consumer) : void
    {
        $this->consumers[$consumer->getGroupId()] = $consumer;
    }
}
