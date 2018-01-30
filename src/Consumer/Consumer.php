<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle\Consumer;

use RdKafka\KafkaConsumer;
use RdKafka\Message;

interface Consumer
{
    public function consume(Message $kafkaMessage, KafkaConsumer $kafkaConsumer) : void;

    public function getConfig() : ConsumerConfig;

    public function getGroupId() : string;

    /**
     * @return string[]
     */
    public function getTopics() : array;
}
