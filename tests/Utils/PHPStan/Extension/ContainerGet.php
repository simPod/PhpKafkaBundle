<?php

declare(strict_types=1);

namespace SimPod\KafkaBundle\Tests\Utils\PHPStan\Extension;

use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Name\FullyQualified;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\DynamicMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use Symfony\Component\DependencyInjection\ContainerInterface;
use function assert;

final class ContainerGet implements DynamicMethodReturnTypeExtension
{
    public function getClass() : string
    {
        return ContainerInterface::class;
    }

    public function isMethodSupported(MethodReflection $methodReflection) : bool
    {
        return $methodReflection->getName() === 'get';
    }

    public function getTypeFromMethodCall(
        MethodReflection $methodReflection,
        MethodCall $methodCall,
        Scope $scope
    ) : Type {
        $classConstFetch = $methodCall->args[0]->value;
        if ($classConstFetch instanceof ClassConstFetch) {
            $fullyQualified = $classConstFetch->class;
            assert($fullyQualified instanceof FullyQualified);

            return new ObjectType($fullyQualified->toCodeString());
        }

        return new ObjectType('object');
    }
}
