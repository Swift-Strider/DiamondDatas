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
 * @category API
 *
 * @author   DiamondStrider1 <62265561+Swift-Strider@users.noreply.github.com>
 * @license  The Unlicense
 *
 * @see     https://github.com/Swift-Strider/DiamondVirions
 */

declare(strict_types=1);

namespace DiamondStrider1\DiamondDatas\metadata;

use DiamondStrider1\DiamondDatas\ConfigContext;
use DiamondStrider1\DiamondDatas\ConfigException;

interface IValidationProvider
{
    /**
     * @throws ConfigException when validation fails
     */
    public function validate(ConfigContext $context): void;
}
