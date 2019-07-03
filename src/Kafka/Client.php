<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka;

use function Safe\gethostname;
use function Safe\sprintf;

class Client
{
    /** @var string */
    private $id;

    /**
     * @param mixed[] $config
     */
    public function __construct(array $config)
    {
        $this->id = $config['client']['id'] ?? null;
    }

    public function getId() : ?string
    {
        return $this->id;
    }

    public function getIdWithHostname() : string
    {
        if ($this->id === null) {
            return gethostname();
        }

        return sprintf('%s-%s', $this->id, gethostname());
    }
}
