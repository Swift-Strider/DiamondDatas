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
 * @category Example
 * @package  DiamondDatas
 * @author   DiamondStrider1 <62265561+Swift-Strider@users.noreply.github.com>
 * @license  The Unlicense
 * @link     https://github.com/Swift-Strider/DiamondVirions
 */

declare(strict_types=1);

namespace Example;

use DiamondStrider1\DiamondDatas\attributes\IntType;

/**
 * An inner structure for the PluginSettings class
 *
 * @category Example
 * @package  DiamondDatas
 * @author   DiamondStrider1 <62265561+Swift-Strider@users.noreply.github.com>
 * @license  The Unlicense
 * @link     https://github.com/Swift-Strider/DiamondVirions
 */
class PrintSettings
{
    /* This object is a member of PluginSettings.
       After the plugin loads the PluginSettings object
       it gets a hold of this PrintSettings and runs the print
       method */
    #[IntType("indent-amount", "The amount of spaces to put before each message printed")]
    public int $indentSpaces;

    public function __construct(int $indentSpaces)
    {
        $this->indentSpaces = $indentSpaces;
    }

    public function printMessage(string $message)
    {
        // Using echo for simplicity, note how $indentSpaces
        // is used indirectly by the plugin by calling this method.
        echo str_repeat(" ", $this->indentSpaces) . $message . "\n";
    }
}
