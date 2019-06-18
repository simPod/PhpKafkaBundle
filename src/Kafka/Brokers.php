<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka;

class Brokers
{
    /** @var string */
    private $bootstrapServers;

    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
        $this->bootstrapServers = $config['bootstrap_servers'];
    }

    public function getBootstrapServers() : string
    {
        return $this->bootstrapServers;
    }
}
