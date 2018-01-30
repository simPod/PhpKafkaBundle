<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests\FullRun;

use SimPod\KafkaBundle\Kafka\Producer;

final class TestProducer
{
    /** @var Producer */
    private $producer;

    public function __construct(Producer $producer)
    {
        $this->producer = $producer;
    }

    public function produce(string $payload, string $topicName) : void
    {
        $this->producer->produce($payload, $topicName);
    }
}
