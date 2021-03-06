<?php

/**
 * This file belongs to DiamondDatas,
 * a library that makes saving self-documenting
 * config files a breeze.
 *
 * This is free and unencumbered software released into the public domain.
 *
 * php version 8.0.13
 *
 * @category Annotations
 *
 * @author   DiamondStrider1 <62265561+Swift-Strider@users.noreply.github.com>
 * @license  The Unlicense
 *
 * @see     https://github.com/Swift-Strider/DiamondVirions
 */

declare(strict_types=1);

namespace DiamondStrider1\DiamondDatas\attributes;

use Attribute;
use DiamondStrider1\DiamondDatas\ClassInfo;
use DiamondStrider1\DiamondDatas\ConfigContext;
use DiamondStrider1\DiamondDatas\ConfigException;
use DiamondStrider1\DiamondDatas\metadata\IValidationProvider;
use ReflectionProperty;
use TypeError;

/**
 * @template T of object
 * @implements IValueType<T>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ObjectType implements IValueType
{
    /** @var ClassInfo<T> */
    private ClassInfo $classInfo;

    /**
     * @phpstan-param class-string<T> $class
     */
    public function __construct(
        string $class,
        private string $config_key = '<root>',
        private string $description = ''
    ) {
        $this->classInfo = ClassInfo::getInfo($class);
    }

    public function getKey(): string
    {
        return $this->config_key;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function shortString(mixed $value): string
    {
        if (!\is_object($value)) {
            return 'NOT SET';
        }
        $class = \get_class($value);

        $slashPos = strrpos($class, '\\', -1);
        $shortClass = substr($class, false !== $slashPos ? $slashPos + 1 : 0);
        $prettyClass = ucfirst($shortClass);

        return "{$prettyClass} {...}";
    }

    public function yamlLines(mixed $value, ConfigContext $context): string
    {
        if (!\is_object($value)) {
            throw new TypeError('$value must be an object.');
        }
        $padding = str_repeat('  ', $context->getDepth());
        $props = ClassInfo::getInfo(\get_class($value))->getProps();

        if (0 === \count($props) && 0 !== $context->getDepth()) {
            $lines = '[]';
        } else {
            $lines = 0 === $context->getDepth() ? '' : "\n";
        }

        $subtypes = $this->classInfo->getSubtypes();
        if (null !== $subtypes) {
            $value_class = \get_class($value);
            $subtype = null;
            foreach ($subtypes as $name => $class) {
                if ($class === $value_class) {
                    $subtype = $name;
                }
            }
            if (!$subtype) {
                throw new TypeError('No $subtype found');
            }
            $lines = "\n{$padding}# valid subtypes are: ".implode(', ', array_keys($subtypes))."\n";
            $lines .= "{$padding}subtype: {$subtype}\n";
            $lines .= "{$padding}options: ".(\count($props) > 0 ? "\n" : '[]');
            $padding .= '  ';
            $context = $context->addKey('options');
        }

        foreach ($props as [$rProp, $inject]) {
            /** @var ReflectionProperty $rProp */
            /** @var IValueType $inject */
            $newContext = $context->addKey($inject->getKey());
            $valueLines = rtrim($inject->yamlLines($rProp->getValue($value), $newContext));

            foreach (explode("\n", $inject->getDescription()) as $descLine) {
                $lines .= "{$padding}# {$descLine}\n";
            }
            $lines .= "{$padding}{$inject->getKey()}: {$valueLines}\n";
        }

        return $lines;
    }

    public function fromRaw(mixed $raw, ConfigContext $context): mixed
    {
        if (!\is_array($raw)) {
            throw new ConfigException('Expected key pair values', $context);
        }
        if (($subs = $this->classInfo->getSubtypes()) !== null) {
            if (!isset($raw['subtype']) || !isset($raw['options'])) {
                throw new ConfigException('Expected keys "subtype" and "options"', $context);
            }
            if (($sub = $subs[$raw['subtype']] ?? null) === null) {
                throw new ConfigException(
                    'Unknown "subtype"; Accepted are: '.implode(', ', array_keys($subs)),
                    $context
                );
            }

            return (new self($sub))->fromRaw($raw['options'], $context->addKey('options'));
        }
        $object = $this->classInfo->createWithoutConstructor();
        foreach ($this->classInfo->getProps() as [$rProp, $inject]) {
            // @var ReflectionProperty $rProp
            /** @var IValueType $inject */
            if (($propValue = $raw[$inject->getKey()] ?? null) === null) {
                if (($defaults = $this->classInfo->getDefaults()) === null) {
                    throw new ConfigException("Property Key \"{$inject->getKey()}\" is missing.", $context);
                }
                $parsed = $defaults[$inject->getKey()];
                if ($parsed instanceof IValidationProvider) {
                    $parsed->validate($context->addKey($inject->getKey()));
                }
            } else {
                $parsed = $inject->fromRaw($propValue, $context->addKey($inject->getKey()));
            }
            $rProp->setValue($object, $parsed);
        }
        if ($object instanceof IValidationProvider) {
            $object->validate($context);
        }
        return $object;
    }
}
