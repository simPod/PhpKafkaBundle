<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Clients\Consumer;

interface Consumer
{
    public function run(): void;
}
