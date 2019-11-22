<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Console;

use SimPod\KafkaBundle\DependencyInjection\ConsumerBag;
use SimPod\KafkaBundle\DependencyInjection\KafkaExtension;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ConsumerListCommand extends Command
{
    private const DESCRIPTION = 'List available consumers.';
    private const NAME        = 'debug:' . KafkaExtension::ALIAS . ':consumers';

    /** @var ConsumerBag */
    private $consumerBag;

    public function __construct(ConsumerBag $consumerBag)
    {
        $this->consumerBag = $consumerBag;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setDescription(self::DESCRIPTION)
            ->setName(self::NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        foreach ($this->consumerBag->getConsumers() as $consumerName => $consumer) {
            $output->writeln($consumerName);
        }

        return 0;
    }
}
