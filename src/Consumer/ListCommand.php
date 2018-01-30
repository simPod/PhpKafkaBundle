<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle\Consumer;

use SimPod\Bundle\KafkaBundle\DependencyInjection\KafkaExtension;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ListCommand extends Command
{
    private const DESCRIPTION = 'List available consumers.';
    private const NAME        = KafkaExtension::ALIAS . ':consumer:list';

    /** @var Bag */
    private $bag;

    public function __construct(Bag $bag)
    {
        $this->bag = $bag;

        parent::__construct();
    }

    protected function configure() : void
    {
        $this
            ->setDescription(self::DESCRIPTION)
            ->setName(self::NAME);
    }

    protected function execute(InputInterface $input, OutputInterface $output) : void
    {
        foreach ($this->bag->getConsumers() as $consumerName => $consumer) {
            $output->writeln($consumerName);
        }
    }
}
