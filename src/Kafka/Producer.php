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

    /** @var callable|null */
    private $deliveryCallback;

    /** @var string[] */
    private $configuration = [];

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

            foreach ($this->configuration as $key => $value) {
                $conf->set($key, $value);
            }

            $conf->setErrorCb(
                static function (\RdKafka\Producer $kafka, int $err, $reason) : void {
                    throw new Exception(sprintf('Kafka error: %s (reason: %s)', rd_kafka_err2str($err), $reason));
                }
            );

            $conf->setDrMsgCb($this->getDeliveryCallback());

            $this->producer = new \RdKafka\Producer($conf);
            $this->producer->addBrokers($this->brokers->getList());
        }

        $producer = $this->producer;

        if (! isset($this->topics[$topicName])) {
            $topicConfig              = new TopicConf();
            $this->topics[$topicName] = $producer->newTopic($topicName, $topicConfig);
        }

        $topic = $this->topics[$topicName];

        $topic->produce($partition, 0, $messagePayload, $partitioningKey);
        $producer->poll(0);
    }

    private function getDeliveryCallback() : ?callable
    {
        if ($this->deliveryCallback === null) {
            return static function (\RdKafka\Producer $kafka, Message $message) : void {
                if ($message->err) {
                    throw ProducingFailed::fromMessage($message);
                }
            };
        }

        return $this->deliveryCallback;
    }

    public function setDeliveryCallback(callable $deliveryCallback) : void
    {
        $this->deliveryCallback = $deliveryCallback;
    }

    public function addConfiguration(string $key, string $value) : void
    {
        $this->configuration[$key] = $value;
    }
}
