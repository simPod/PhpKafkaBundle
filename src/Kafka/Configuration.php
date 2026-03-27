<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka;

use function gethostname;
use function sprintf;

final class Configuration
{
    /** @param array{authentication: string|null, bootstrap_servers: string, client?: array{id?: string}} $config */
    public function __construct(private array $config)
    {
    }

    public function getAuthentication(): string|null
    {
        return $this->config['authentication'];
    }

    public function getBootstrapServers(): string
    {
        return $this->config['bootstrap_servers'];
    }

    public function getClientId(): string|null
    {
        return $this->config['client']['id'] ?? null;
    }

    public function getClientIdWithHostname(): string
    {
        $hostname = gethostname();
        if ($hostname === false) {
            $hostname = 'unknown';
        }

        $clientId = $this->config['client']['id'] ?? null;
        if ($clientId === null) {
            return $hostname;
        }

        return sprintf('%s-%s', $clientId, $hostname);
    }
}
