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
$ composer require simpod/kafka-bundle
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

This bundle registers these commands:

- `bin/console debug:kafka:consumers` to list all available consumer groups
- `bin/console kafka:consumer:run <consumer group name>` to run consumer instance to join specific consumer group

### Setup

Create eg. `kafka.yaml` file in your config directory with following content:

```yaml
kafka:
    broker_list: '%env(KAFKA_BROKER_LIST)%' # required
    client:
        id: 'your-application-name'
```
It reads env var `KAFKA_BROKER_LIST` that contains comma-separated list of brokers (`broker-1.kafka.com:9092,broker-2.kafka.com:9092`).

If not set, it defaults to `127.0.0.1:9092`

### Producing

To create producer, you will need only `Brokers` from this bundle, there's no need for anything else.

Simple example:

```php
<?php

declare(strict_types=1);

use RdKafka\Producer;
use SimPod\KafkaBundle\Kafka\Brokers;
use const RD_KAFKA_PARTITION_UA;
use function json_encode;

class SimpleProducer
{
    private const TOPIC_NAME = 'topic1';

    /** @var Brokers */
    private $brokers;

    public function __construct(Brokers $brokers)
    {
        $this->brokers = $brokers;
    }

    public function produce(MessageObject $message) : void
    {
        $producer = new Producer();
        $producer->addBrokers($this->brokers->getList());

        $topic = $producer->newTopic(self::TOPIC_NAME);

        // 4th argument can be optional key
        $topic->produce(
            RD_KAFKA_PARTITION_UA,
            0,
            json_encode($message)
        );
    }
}
```

### Consuming

This is example of simple consumer that belongs into `simple_consumer_group` and consuming `topic1`
```php
<?php

declare(strict_types=1);

use RdKafka\KafkaConsumer;
use RdKafka\Message;
use SimPod\KafkaBundle\Kafka\Consumer\Consumer;
use SimPod\KafkaBundle\Kafka\Consumer\Config;

final class SimpleConsumer implements Consumer
{
    private const GROUP_ID = 'simple_consumer_group';

    public function consume(Message $kafkaMessage, KafkaConsumer $kafkaConsumer) : void
    {
        // Execute your consumer logic here
    }

    public function getGroupId() : string
    {
        return self::GROUP_ID;
    }

    /**
     * @return string[]
     */
    public function getTopics() : array
    {
        return ['topic1'];
    }

    public function getConfig() : Config
    {
        return new Config($this->getGroupId());
    }
}
```

 It is automatically registered to container for it `implements Consumer` 

### Development

There is `kwn/php-rdkafka-stubs` listed as a dev dependency so it properly integrates php-rdkafka extension with IDE.
