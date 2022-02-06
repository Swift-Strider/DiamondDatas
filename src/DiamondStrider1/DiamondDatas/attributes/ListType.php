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
use DiamondStrider1\DiamondDatas\ConfigContext;
use DiamondStrider1\DiamondDatas\ConfigException;
use TypeError;

/**
 * @template T
 * @implements IValueType<array<int, T>>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class ListType implements IValueType
{
    /** @var IValueType<T> */
    private IValueType $type;

    public function __construct(
        private string $config_key = '<root>',
        private string $description = ''
    ) {
    }

    public function setType(IValueType $type): void
    {
        $this->type = $type;
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
        if (!\is_array($value)) {
            return 'NOT SET';
        }

        return 'List [...]';
    }

    public function yamlLines(mixed $value, ConfigContext $context): string
    {
        if (!(\is_array($value) && array_values($value) === $value)) {
            throw new TypeError('$value must be an array-list');
        }
        $lines = "\n";
        foreach ($value as $i => $v) {
            $newContext = $context->addKey($i);
            $valueLines = rtrim($this->type->yamlLines($v, $newContext));
            $padding = str_repeat('  ', $context->getDepth());
            $lines .= "{$padding} - {$valueLines}\n";
        }
        if ("\n" === $lines) {
            return '[]';
        }

        return $lines;
    }

    public function fromRaw(mixed $raw, ConfigContext $context): mixed
    {
        if (!\is_array($raw)) {
            throw new ConfigException('Expected key pair values', $context);
        }

        /** @var array<int, T> */
        $array = [];
        foreach ($raw as $i => $value) {
            $array[] = $this->type->fromRaw($value, $context->addKey($i));
        }

        return $array;
    }
}
