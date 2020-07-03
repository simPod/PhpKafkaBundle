# PHP Kafka Symfony bundle for php-rdkafka

[![Build Status](https://github.com/simPod/PhpKafkaBundle/workflows/CI/badge.svg?branch=master)](https://github.com/simPod/PhpKafkaBundle/actions)
[![Coverage Status](https://coveralls.io/repos/github/simPod/PhpKafkaBundle/badge.svg?branch=master)](https://coveralls.io/github/simPod/PhpKafkaBundle?branch=master)
[![Downloads](https://poser.pugx.org/simpod/kafka-bundle/d/total.svg)](https://packagist.org/packages/simpod/kafka-bundle)
[![Packagist](https://poser.pugx.org/simpod/kafka-bundle/v/stable.svg)](https://packagist.org/packages/simpod/kafka-bundle)
[![GitHub Issues](https://img.shields.io/github/issues/simPod/PhpKafkaBundle.svg?style=flat-square)](https://github.com/simPod/PhpKafkaBundle/issues)
[![Type Coverage](https://shepherd.dev/github/simPod/PhpKafkaBundle/coverage.svg)](https://shepherd.dev/github/simPod/PhpKafkaBundle)
[![Infection MSI](https://badge.stryker-mutator.io/github.com/simPod/PhpKafkaBundle/master)](https://infection.github.io)

## Installation

Add as [Composer](https://getcomposer.org/) dependency:

```sh
composer require simpod/kafka-bundle
```

Then add `KafkaBundle` to Symfony's `bundles.php`:

```php
use SimPod\KafkaBundle\SimPodKafkaBundle;

return [
    ...
    new SimPodKafkaBundle()
    ...
];
```

## Usage

This package simply makes it easier to integrate https://github.com/arnaud-lb/php-rdkafka with Symfony. For more details how to work with Kafka in PHP, refer to its documentation.

### Available console commands:

- `bin/console debug:kafka:consumers` to list all available consumer groups
- `bin/console kafka:consumer:run <consumer name>` to run consumer instance

### Config:

You can create eg. `kafka.yaml` file in your config directory with following content:

```yaml
kafka:
    authentication: '%env(KAFKA_AUTHENTICATION)%'
    bootstrap_servers: '%env(KAFKA_BOOTSTRAP_SERVERS)%'
    client:
        id: 'your-application-name'
```

- `authentication` reads env var `KAFKA_AUTHENTICATIOn` that contains authentication uri (`sasl-plain://user:password`, or it might be just empty indicating no authentication).
- `bootstrap_servers` reads env var `KAFKA_BOOTSTRAP_SERVERS` that contains comma-separated list of bootstrap servers (`broker-1.kafka:9092,broker-2.kafka:9092`).

If `bootstrap_servers` isn't set, it defaults to `127.0.0.1:9092`

### Services

Following services are registered in container and can be DI injected.

#### Configuration
class: `\SimPod\KafkaBundle\Kafka\Configuration`

Configuration service allows easy access to all the configuration properties.

```php
$config->set(ConsumerConfig::CLIENT_ID_CONFIG, $this->configuration->getIdWithHostname());
```

### Consuming

There's interface `NamedConsumer` available. When your consumer implements it, this bundle autoregisters it.

This is example of simple consumer, it can be then run via `bin/console kafka:consumer:run consumer1`
```php
<?php

declare(strict_types=1);

namespace Your\AppNamespace;

use SimPod\Kafka\Clients\Consumer\ConsumerConfig;
use SimPod\Kafka\Clients\Consumer\KafkaBatchConsumer;
use SimPod\KafkaBundle\Kafka\Configuration;
use SimPod\KafkaBundle\Kafka\Clients\Consumer\NamedConsumer;

final class ExampleKafkaConsumer implements NamedConsumer
{
    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function run() : void
    {
        $kafkaConsumer = new KafkaBatchConsumer($this->getConfig());

        $kafkaConsumer->subscribe(['topic1']);

        while (true) {
            ...
        }
    }
    
    public function getName() : string {
        return 'consumer1';    
    }

    private function getConfig() : ConsumerConfig
    {
        $config = new ConsumerConfig();

        $config->set(ConsumerConfig::BOOTSTRAP_SERVERS_CONFIG, $this->configuration->getBootstrapServers());
        $config->set(ConsumerConfig::ENABLE_AUTO_COMMIT_CONFIG, false);
        $config->set(ConsumerConfig::CLIENT_ID_CONFIG, $this->configuration->getClientIdWithHostname());
        $config->set(ConsumerConfig::AUTO_OFFSET_RESET_CONFIG, 'earliest');
        $config->set(ConsumerConfig::GROUP_ID_CONFIG, 'consumer_group');

        return $config;
    }
}

```

### Development

There is `kwn/php-rdkafka-stubs` listed as a dev dependency so it properly integrates php-rdkafka extension with IDE.
