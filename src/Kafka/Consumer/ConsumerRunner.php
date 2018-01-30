<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Consumer;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use RdKafka\KafkaConsumer;
use SimPod\KafkaBundle\Kafka\Brokers;
use SimPod\KafkaBundle\Kafka\Client;
use SimPod\KafkaBundle\Kafka\Consumer\Exception\IncompatibleStatus;
use SimPod\KafkaBundle\Kafka\Consumer\Exception\RebalancingFailed;
use SimPod\KafkaBundle\Kafka\Event\ReachedEndOfPartition;
use SimPod\KafkaBundle\Kafka\Event\TimedOut;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use function microtime;
use const RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS;
use const RD_KAFKA_RESP_ERR__PARTITION_EOF;
use const RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS;
use const RD_KAFKA_RESP_ERR__TIMED_OUT;
use const RD_KAFKA_RESP_ERR_NO_ERROR;

final class ConsumerRunner
{
    /** @var Brokers */
    private $brokers;

    /** @var Client */
    private $client;

    /** @var int */
    private $processedMessageCount = 0;

    /** @var Configuration */
    private $configuration;

    /** @var LoggerInterface */
    private $logger;

    /** @var EventDispatcherInterface|null */
    private $eventDispatcher;

    public function __construct(
        Brokers $brokers,
        Client $client,
        ?LoggerInterface $logger = null,
        ?EventDispatcherInterface $eventDispatcher = null
    ) {
        $this->brokers         = $brokers;
        $this->client          = $client;
        $this->logger          = $logger ?? new NullLogger();
        $this->eventDispatcher = $eventDispatcher;
    }

    public function run(Consumer $consumer) : void
    {
        $this->configuration = $consumer->getConfiguration();

        $kafkaConfiguration = $this->configuration->get();
        $kafkaConfiguration->set(ConfigConstants::METADATA_BROKER_LIST, $this->brokers->getList());
        if ($this->client->getId() !== null) {
            $kafkaConfiguration->set('client.id', $this->client->getId());
        }

        $kafkaConfiguration->setRebalanceCb(
            function (KafkaConsumer $kafka, int $err, ?array $partitions = null) : void {
                switch ($err) {
                    case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                        $this->logger->info('Assigning partitions', $partitions ?? []);
                        $kafka->assign($partitions);
                        break;

                    case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                        $this->logger->info('Revoking partitions', $partitions ?? []);
                        $kafka->assign(null);
                        break;

                    default:
                        throw RebalancingFailed::new($err);
                }
            }
        );

        $kafkaConsumer = new KafkaConsumer($kafkaConfiguration);
        $kafkaConsumer->subscribe($consumer->getTopics());

        $this->startConsuming($kafkaConsumer, $consumer);
    }

    private function tryDispatch(string $eventName, ?Event $event = null) : void
    {
        if ($this->eventDispatcher === null) {
            return;
        }

        $this->eventDispatcher->dispatch($eventName, $event);
    }

    private function startConsuming(KafkaConsumer $kafkaConsumer, Consumer $consumer) : void
    {
        $startTime = microtime(true);
        $consumer->setKafkaConsumer($kafkaConsumer);

        while (true) {
            if (! $this->shouldContinue($startTime)) {
                $this->tryDispatch(ReachedEndOfPartition::NAME);
                break;
            }

            $message = $kafkaConsumer->consume(120 * 1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $consumer->setMostRecentMessage($message);
                    $consumer->consume($message);
                    $this->processedMessageCount++;
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    $this->logger->info('No more messages. Will wait for more');
                    $this->tryDispatch(ReachedEndOfPartition::NAME);
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $this->logger->info('Timed out');
                    $this->tryDispatch(TimedOut::NAME);
                    break;
                default:
                    throw IncompatibleStatus::fromMessage($message);
            }
        }
    }

    private function shouldContinue(float $startTime) : bool
    {
        return $this->hasAnyMessagesLeft() && $this->hasAnyTimeLeft($startTime);
    }

    private function hasAnyMessagesLeft() : bool
    {
        $maxMessages = $this->configuration->getMaxMessages();

        return $maxMessages === null || $maxMessages > $this->processedMessageCount;
    }

    private function hasAnyTimeLeft(float $startTime) : bool
    {
        $maxSeconds = $this->configuration->getMaxSeconds();

        return $maxSeconds === null || microtime(true) < $startTime + $maxSeconds;
    }
}
