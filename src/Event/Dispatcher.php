<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Event;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event;

class Dispatcher
{
    /** @var EventDispatcherInterface|null */
    private $eventDispatcher;

    public function __construct(?EventDispatcherInterface $eventDispatcher = null)
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    public function dispatch(Event $event) : void
    {
        if ($this->eventDispatcher === null) {
            return;
        }

        $this->eventDispatcher->dispatch($event);
    }
}
