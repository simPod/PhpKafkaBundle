<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Consumer;

use RdKafka\KafkaConsumer;
use RdKafka\Message;

interface Consumer
{
    public function consume(Message $kafkaMessage, KafkaConsumer $kafkaConsumer) : void;

    public function getConfiguration() : Configuration;

    public function getGroupId() : string;

    /**
     * @return string[]
     */
    public function getTopics() : array;

    public function idle() : void;
}
