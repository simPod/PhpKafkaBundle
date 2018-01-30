<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka;

class Brokers
{
    /** @var string */
    private $brokerList;

    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
        $this->brokerList = $config['broker_list'];
    }

    public function getList() : string
    {
        return $this->brokerList;
    }
}
