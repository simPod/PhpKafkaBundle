<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka;

use RuntimeException;
use function gethostname;
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
            $hostname = gethostname();
            if ($hostname === false) {
                throw new RuntimeException('Could not get hostname');
            }

            return $hostname;
        }

        return sprintf('%s-%s', $this->id, gethostname());
    }
}
