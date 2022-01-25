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
 * @package  DiamondDatas
 * @author   DiamondStrider1 <62265561+Swift-Strider@users.noreply.github.com>
 * @license  The Unlicense
 * @link     https://github.com/Swift-Strider/DiamondVirions
 */

declare(strict_types=1);

namespace DiamondStrider1\DiamondDatas\metadata;

/**
 * Provides a defaults array where config-key => value
 */
interface IDefaultProvider
{
    /**
     * @return array<string, mixed>
     * config-key => value
     * 
     * The values this function returns must be
     * fully parsed.
     * 
     * Ex: The defaults in yaml
     * ```yaml
     * position: [0, 2, 4]
     * ```
     * would lead to `getDefaults()` returning
     * `["position" => new Vector3(0, 2, 4)]`
     */
    public static function getDefaults(): array;
}
