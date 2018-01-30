<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Consumer;

use RdKafka\KafkaConsumer;
use RdKafka\Message;

abstract class Consumer
{
    /** @var Message|null */
    protected $mostRecentMessage;

    /** @var KafkaConsumer */
    protected $kafkaConsumer;

    abstract public function consume(Message $kafkaMessage) : void;

    abstract public function getConfiguration() : Configuration;

    abstract public function getGroupId() : string;

    /**
     * @return string[]
     */
    abstract public function getTopics() : array;

    public function setMostRecentMessage(Message $mostRecentMessage) : void
    {
        $this->mostRecentMessage = $mostRecentMessage;
    }

    public function setKafkaConsumer(KafkaConsumer $kafkaConsumer) : void
    {
        $this->kafkaConsumer = $kafkaConsumer;
    }
}
