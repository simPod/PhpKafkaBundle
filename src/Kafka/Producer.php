<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka;

use Exception;
use RdKafka\Conf;
use RdKafka\Message;
use RdKafka\ProducerTopic;
use RdKafka\TopicConf;
use SimPod\KafkaBundle\Kafka\Producer\Exception\ProducingFailed;
use function rd_kafka_err2str;
use function sprintf;
use const RD_KAFKA_PARTITION_UA;

class Producer
{
    /** @var Brokers */
    private $brokers;

    /** @var Client */
    private $client;

    /** @var \RdKafka\Producer */
    private $producer;

    /** @var ProducerTopic[] */
    private $topics = [];

    public function __construct(Brokers $brokers, Client $client)
    {
        $this->brokers = $brokers;
        $this->client  = $client;
    }

    public function produce(
        string $messagePayload,
        string $topicName,
        ?string $partitioningKey = null,
        int $partition = RD_KAFKA_PARTITION_UA
    ) : void {
        if ($this->producer === null) {
            $conf = new Conf();

            if ($this->client->getId() !== null) {
                $conf->set('client.id', $this->client->getId());
            }

            $conf->setErrorCb(
                function (\RdKafka\Producer $kafka, $err, $reason) : void {
                    throw new Exception(sprintf('Kafka error: %s (reason: %s)', rd_kafka_err2str($err), $reason));
                }
            );

            $conf->setDrMsgCb(
                function (\RdKafka\Producer $kafka, Message $message) : void {
                    if ($message->err) {
                        throw ProducingFailed::fromMessage($message);
                    }
                }
            );

            $this->producer = new \RdKafka\Producer($conf);
            $this->producer->addBrokers($this->brokers->getList());
        }

        $producer = $this->producer;

        if (! isset($this->topics[$topicName])) {
            $topicConfig = new TopicConf();
//            $topicConfig->set('message.timeout.ms', '1000');
            $this->topics[$topicName] = $producer->newTopic($topicName, $topicConfig);
        }

        $topic = $this->topics[$topicName];

        $topic->produce($partition, 0, $messagePayload, $partitioningKey);

        while ($producer->getOutQLen() > 0) {
            $producer->poll(1);
        }
    }
}
