<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests;

use PHPUnit\Framework\TestCase;
use SimPod\KafkaBundle\DependencyInjection\KafkaExtension;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\Yaml\Yaml;
use function Safe\file_get_contents;
use function sys_get_temp_dir;

abstract class KafkaTestCase extends TestCase
{
    protected function createYamlBundleTestContainer() : Container
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.name'        => 'app',
            'kernel.debug'       => false,
            'kernel.cache_dir'   => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir'    => __DIR__ . '/../',
        ]));

        $extension = new KafkaExtension();
        $container->registerExtension($extension);

        $fileContents = file_get_contents(__DIR__ . '/test-config.yaml');

        $config = Yaml::parse($fileContents);

        $extension->load($config, $container);

        $container->getCompilerPassConfig()->addPass(new TestCaseAllPublicCompilerPass());
        $container->compile();

        return $container;
    }
}

