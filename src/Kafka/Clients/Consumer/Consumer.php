<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Clients\Consumer;

use RdKafka\Exception;

interface Consumer
{
    /** @throws Exception */
    public function run(): void;
}
