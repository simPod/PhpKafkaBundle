# PHP Kafka Symfony bundle for php-rdkafka

[![Build Status](https://travis-ci.org/simPod/KafkaBundle.svg)](https://travis-ci.org/simPod/KafkaBundle)
[![Downloads](https://poser.pugx.org/simpod/kafka-bundle/d/total.svg)](https://packagist.org/packages/simpod/kafka-bundle)
[![Packagist](https://poser.pugx.org/simpod/kafka-bundle/v/stable.svg)](https://packagist.org/packages/simpod/kafka-bundle)
[![Licence](https://poser.pugx.org/simpod/kafka-bundle/license.svg)](https://packagist.org/packages/simpod/kafka-bundle)
[![Quality Score](https://scrutinizer-ci.com/g/simPod/KafkaBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/simPod/KafkaBundle)
[![Code Coverage](https://scrutinizer-ci.com/g/simPod/KafkaBundle/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/simPod/KafkaBundle)

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
    bootstrap_servers: '%env(KAFKA_BOOTSTRAP_SERVERS)%'
    client:
        id: 'your-application-name'
```

- `bootstrap_servers` reads env var `KAFKA_BOOTSTRAP_SERVERS` that contains comma-separated list of bootstrap servers (`broker-1.kafka:9092,broker-2.kafka:9092`).

If not set, it defaults to `127.0.0.1:9092`

### Services

Following services are registered in container and can be DI injected.

#### Brokers
class: `\SimPod\KafkaBundle\Kafka\Brokers`

Brokers service gives you `bootstrap_servers` config value through `->getBootstrapServers()`

#### Clients
class: `\SimPod\KafkaBundle\Kafka\Clients`

Clients can help you generate `client.id`

```php
$config->set(ConsumerConfig::CLIENT_ID_CONFIG, $this->client->getIdWithHostname());
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
use SimPod\KafkaBundle\Kafka\Brokers;
use SimPod\KafkaBundle\Kafka\Client;
use SimPod\KafkaBundle\Kafka\Clients\Consumer\NamedConsumer;

final class ExampleKafkaConsumer implements NamedConsumer
{
    /** @var Brokers */
    private $brokers;

    /** @var Client */
    private $client;

    public function __construct(Client $client, Brokers $brokers)
    {
        $this->brokers = $brokers;
        $this->client  = $client;
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

        $config->set(ConsumerConfig::BOOTSTRAP_SERVERS_CONFIG, $this->brokers->getList());
        $config->set(ConsumerConfig::ENABLE_AUTO_COMMIT_CONFIG, false);
        $config->set(ConsumerConfig::CLIENT_ID_CONFIG, $this->client->getIdWithHostname());
        $config->set(ConsumerConfig::AUTO_OFFSET_RESET_CONFIG, 'earliest');
        $config->set(ConsumerConfig::GROUP_ID_CONFIG, 'consumer_group');

        return $config;
    }
}

```

### Development

There is `kwn/php-rdkafka-stubs` listed as a dev dependency so it properly integrates php-rdkafka extension with IDE.
