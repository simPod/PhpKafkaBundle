<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Clients\Consumer;

interface NamedConsumer extends Consumer
{
    public function getName() : string;
}
