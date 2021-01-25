<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Console;

use InvalidArgumentException;
use SimPod\KafkaBundle\DependencyInjection\ConsumerBag;
use SimPod\KafkaBundle\DependencyInjection\KafkaExtension;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

use function assert;
use function is_string;
use function strtolower;

final class ConsumeCommand extends Command
{
    private const ARGUMENT_DESCRIPTION = 'Consumer name';
    private const ARGUMENT_NAME        = 'consumerName';
    private const DESCRIPTION          = 'Start consuming';
    private const NAME                 = KafkaExtension::ALIAS . ':consumer:run';

    private ConsumerBag $consumerBag;

    public function __construct(ConsumerBag $consumerBag)
    {
        $this->consumerBag = $consumerBag;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription(self::DESCRIPTION);
        $this->setName(self::NAME);
        $this->addArgument(
            self::ARGUMENT_NAME,
            InputArgument::REQUIRED,
            self::ARGUMENT_DESCRIPTION
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $name = $input->getArgument(self::ARGUMENT_NAME);
        assert(is_string($name));

        $consumerName = strtolower($name);
        $consumers    = $this->consumerBag->getConsumers();

        if (! isset($consumers[$consumerName])) {
            throw new InvalidArgumentException('Consumer "' . $consumerName . '"" does not exists.');
        }

        $consumer = $consumers[$consumerName];

        $consumer->run();

        return 0;
    }
}
