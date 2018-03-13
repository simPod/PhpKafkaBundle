<?php

declare(strict_types=1);

namespace SimPod\Bundle\KafkaBundle\Consumer;

final class ConfigConstants
{
    public const ENABLE_AUTO_COMMIT   = 'enable.auto.commit';
    public const GROUP_ID             = 'group.id';
    public const METADATA_BROKER_LIST = 'metadata.broker.list';
    public const SESSION_TIMEOUT_MS   = 'session.timeout.ms';
}
