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
use pocketmine\math\Vector3;
use TypeError;

/**
 * @implements IValueType<Vector3>
 */
#[Attribute(Attribute::TARGET_PROPERTY)]
class VectorType implements IValueType
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
        if (!($value instanceof Vector3)) {
            return "NOT SET";
        }
        return sprintf("(x: %.2f, y: %.2f, z: %.2f)", $value->x, $value->y, $value->z);
    }

    public function yamlLines(mixed $value, ConfigContext $context): string
    {
        if (!($value instanceof Vector3)) {
            throw new TypeError("\$value is not a Vector3");
        }
        return sprintf("[%.6f, %.6f, %.6f]", $value->x, $value->y, $value->z);
    }

    public function fromRaw(mixed $raw, ConfigContext $context): mixed
    {
        if (!\is_array($raw) || \count($raw) < 3) {
            throw new ConfigException("Expected Vector3 (list of 3 numbers)", $context);
        }
        return new Vector3((float) $raw[0], (float) $raw[1], (float) $raw[2]);
    }
}
