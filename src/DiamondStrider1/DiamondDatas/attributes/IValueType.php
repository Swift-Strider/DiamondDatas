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
 * @category Internal
 *
 * @author   DiamondStrider1 <62265561+Swift-Strider@users.noreply.github.com>
 * @license  The Unlicense
 *
 * @see     https://github.com/Swift-Strider/DiamondVirions
 */

declare(strict_types=1);

namespace DiamondStrider1\DiamondDatas\attributes;

use DiamondStrider1\DiamondDatas\ConfigContext;

/**
 * @template T
 * An attribute that gives info about a property
 */
interface IValueType
{
    public function getKey(): string;

    public function getDescription(): string;

    public function shortString(mixed $value): string;

    /**
     * @param T $value
     */
    public function yamlLines(mixed $value, ConfigContext $context): string;

    /**
     * @return T
     */
    public function fromRaw(mixed $raw, ConfigContext $context): mixed;
}
