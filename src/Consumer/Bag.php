<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle\Consumer;

final class Bag
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
