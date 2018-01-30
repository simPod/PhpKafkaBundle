<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Kafka\Consumer;

use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\TopicConf;

class Configuration
{
    /** @var Conf */
    private $config;

    /** @var int|null */
    private $maxMessages;

    /** @var float|null */
    private $maxSeconds;

    public function __construct(string $groupId, ?int $maxMessages = null, ?float $maxSeconds = null)
    {
        $this->config = new Conf();

        $this->set(ConfigConstants::GROUP_ID, $groupId);

        $this->maxMessages = $maxMessages;
        $this->maxSeconds  = $maxSeconds;
    }

    public function set(string $name, string $value) : void
    {
        $this->config->set($name, $value);
    }

    public function setDefaultTopicConf(TopicConf $topicConf) : void
    {
        $this->config->setDefaultTopicConf($topicConf);
    }

    public function commitIfAutoCommitDisabled(Message $message, KafkaConsumer $kafkaConsumer) : void
    {
        if ($this->isAutoCommitEnabled()) {
            return;
        }
        $kafkaConsumer->commit($message);
    }

    public function isAutoCommitEnabled() : bool
    {
        return $this->config->dump()[ConfigConstants::ENABLE_AUTO_COMMIT] === 'true';
    }

    public function get() : Conf
    {
        return $this->config;
    }

    public function getMaxMessages() : ?int
    {
        return $this->maxMessages;
    }

    public function getMaxSeconds() : ?float
    {
        return $this->maxSeconds;
    }
}
