<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Console;

use RdKafka\KafkaConsumer;
use SimPod\KafkaBundle\DependencyInjection\ConsumerBag;
use SimPod\KafkaBundle\DependencyInjection\KafkaExtension;
use SimPod\KafkaBundle\Kafka\Brokers;
use SimPod\KafkaBundle\Kafka\Consumer\ConfigConstants;
use SimPod\KafkaBundle\Kafka\Consumer\Configuration;
use SimPod\KafkaBundle\Kafka\Consumer\Consumer;
use SimPod\KafkaBundle\Kafka\Consumer\IncompatibleConsumerStatus;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function strtolower;
use const RD_KAFKA_RESP_ERR__PARTITION_EOF;
use const RD_KAFKA_RESP_ERR__TIMED_OUT;
use const RD_KAFKA_RESP_ERR_NO_ERROR;

final class ConsumeCommand extends Command
{
    private const ARGUMENT_DESCRIPTION = 'Consumer name';
    private const ARGUMENT_NAME        = 'consumerName';
    private const DESCRIPTION          = 'Start consuming';
    private const NAME                 = KafkaExtension::ALIAS . ':consumer:run';

    /** @var ConsumerBag */
    private $consumerBag;

    /** @var Brokers */
    private $brokers;

    public function __construct(ConsumerBag $consumerBag, Brokers $brokers)
    {
        $this->consumerBag = $consumerBag;
        $this->brokers     = $brokers;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this->setDescription(self::DESCRIPTION);
        $this->setName(self::NAME);
        $this->addArgument(
            self::ARGUMENT_NAME,
            InputArgument::REQUIRED,
            self::ARGUMENT_DESCRIPTION
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        $consumerName = strtolower($input->getArgument(self::ARGUMENT_NAME));
        $consumers    = $this->consumerBag->getConsumers();

        if (! isset($consumers[$consumerName])) {
            throw new \InvalidArgumentException('Consumer ' . $consumerName . ' does not exists.');
        }

        $consumer = $consumers[$consumerName];

        $configuration = $consumer->getConfiguration();

        $config = $configuration->get();
        $config->set(ConfigConstants::METADATA_BROKER_LIST, $this->brokers->getList());

        $kafkaConsumer = new KafkaConsumer($config);
        $kafkaConsumer->subscribe($consumer->getTopics());

        $this->startConsuming($configuration, $kafkaConsumer, $consumer);
    }

    private function startConsuming(
        Configuration $configuration,
        KafkaConsumer $kafkaConsumer,
        Consumer $consumer
    ) : void {
        $consumerIdleThresholdMs = $configuration->getConsumerIdleThresholdMs();

        while (true) {
            $message = $kafkaConsumer->consume($consumerIdleThresholdMs);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $consumer->consume($message, $kafkaConsumer);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    $consumer->idle();
                    break;
                default:
                    throw IncompatibleConsumerStatus::fromMessage($message);
            }
        }
    }
}
