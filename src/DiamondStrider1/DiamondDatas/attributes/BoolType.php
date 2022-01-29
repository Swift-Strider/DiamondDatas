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
 * @package  DiamondDatas
 * @author   DiamondStrider1 <62265561+Swift-Strider@users.noreply.github.com>
 * @license  The Unlicense
 * @link     https://github.com/Swift-Strider/DiamondVirions
 */

declare(strict_types=1);

namespace DiamondStrider1\DiamondDatas\attributes;

use Attribute;
use DiamondStrider1\DiamondDatas\ConfigContext;
use DiamondStrider1\DiamondDatas\ConfigException;

/**
 * @implements IValueType<bool>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class BoolType implements IValueType
{
    public function __construct(
        private string $config_key = "",
        private string $description = ""
    ) {
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
        if (!\is_bool($value)) {
            return "NOT SET";
        }
        return $value ? "ENABLED" : "DISABLED";
    }

    public function yamlLines(mixed $value, ConfigContext $context): string
    {
        return $value ? "true" : "false";
    }

    public function fromRaw(mixed $raw, ConfigContext $context): mixed
    {
        if (!\is_bool($raw)) {
            throw new ConfigException("Expected boolean", $context);
        }
        return $raw;
    }
}
