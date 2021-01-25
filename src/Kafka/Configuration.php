<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka;

use function Safe\gethostname;
use function Safe\sprintf;

final class Configuration
{
    /** @var array{authentication: string|null, bootstrap_servers: string, client?: array{id?: string}} */
    private array $config;

    /** @param array{authentication: string|null, bootstrap_servers: string, client?: array{id?: string}} $config */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getAuthentication(): ?string
    {
        return $this->config['authentication'];
    }

    public function getBootstrapServers(): string
    {
        return $this->config['bootstrap_servers'];
    }

    public function getClientId(): ?string
    {
        return $this->config['client']['id'] ?? null;
    }

    public function getClientIdWithHostname(): string
    {
        $clientId = $this->config['client']['id'] ?? null;
        if ($clientId === null) {
            return gethostname();
        }

        return sprintf('%s-%s', $clientId, gethostname());
    }
}
