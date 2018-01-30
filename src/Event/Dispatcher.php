<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Dispatcher
{
    /** @var EventDispatcherInterface|null */
    private $eventDispatcher;

    public function __construct(?EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(string $eventName, ?Event $event = null) : void
    {
        if ($this->eventDispatcher === null) {
            return;
        }

        $this->eventDispatcher->dispatch($eventName, $event);
    }
}
