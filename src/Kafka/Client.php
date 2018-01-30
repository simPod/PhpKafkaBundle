<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka;

class Client
{
    /** @var string */
    private $id;

    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
        $this->id = $config['client']['id'] ?? null;
    }

    public function getId() : ?string
    {
        return $this->id;
    }
}
