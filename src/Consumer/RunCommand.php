<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle\Consumer;

use RdKafka\KafkaConsumer;
use SimPod\Bundle\KafkaBundle\Brokers;
use SimPod\Bundle\KafkaBundle\DependencyInjection\KafkaExtension;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use const RD_KAFKA_RESP_ERR__PARTITION_EOF;
use const RD_KAFKA_RESP_ERR__TIMED_OUT;
use const RD_KAFKA_RESP_ERR_NO_ERROR;
use function strtolower;

final class RunCommand extends Command
{
    private const ARGUMENT_DESCRIPTION = 'Consumer name';

    private const ARGUMENT_NAME = 'consumerName';

    private const DESCRIPTION = 'Start consuming';

    private const NAME = KafkaExtension::ALIAS . ':consumer:run';

    /** @var Bag */
    private $bag;

    /** @var Brokers */
    private $brokers;

    public function __construct(Bag $bag, Brokers $brokers)
    {
        $this->bag     = $bag;
        $this->brokers = $brokers;

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
        $consumers    = $this->bag->getConsumers();

        if (! isset($consumers[$consumerName])) {
            throw new \InvalidArgumentException('Consumer ' . $consumerName . ' does not exists.');
        }

        $consumer = $consumers[$consumerName];

        $config = $consumer->getConfig()->get();
        $config->set(ConfigConstants::METADATA_BROKER_LIST, $this->brokers->getList());

        $kafkaConsumer = new KafkaConsumer($config);
        $kafkaConsumer->subscribe($consumer->getTopics());

        $this->startConsuming($kafkaConsumer, $consumer);
    }

    private function startConsuming(KafkaConsumer $kafkaConsumer, Consumer $consumer) : void
    {
        while (true) {
            $message = $kafkaConsumer->consume(120 * 1000);
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $consumer->consume($message, $kafkaConsumer);
                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw ConsumerException::fromMessage($message);
            }
        }
    }
}
