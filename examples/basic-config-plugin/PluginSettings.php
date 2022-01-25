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

use DiamondStrider1\DiamondDatas\attributes\BoolType;
use DiamondStrider1\DiamondDatas\attributes\FloatType;
use DiamondStrider1\DiamondDatas\attributes\IntType;
use DiamondStrider1\DiamondDatas\attributes\ListType;
use DiamondStrider1\DiamondDatas\attributes\ObjectType;
use DiamondStrider1\DiamondDatas\attributes\StringType;
use DiamondStrider1\DiamondDatas\attributes\VectorType;
use DiamondStrider1\DiamondDatas\metadata\IDefaultProvider;
use pocketmine\math\Vector3;

/**
 * A class that holds the example plugin's
 * configuration
 *
 * @category Example
 * @package  DiamondDatas
 * @author   DiamondStrider1 <62265561+Swift-Strider@users.noreply.github.com>
 * @license  The Unlicense
 * @link     https://github.com/Swift-Strider/DiamondVirions
 */
class PluginSettings implements IDefaultProvider
{
    /* All fields with `IntType` or other attributes to be
       saved **must be marked as public**. Failing to do so will
       crash your plugin! */
    #[IntType("message-repeat-count", "How many times to print")]
    public int $msgRepeatCount;

    /* First Parameter: The config key in the yaml file
       Second Parameter: Comments to write before the key
                         in the yaml file */
    #[FloatType("float-key", "A float to print")]
    public float $aFloat;

    #[StringType("string-key", "A string to print")]
    public string $aString;

    #[BoolType("bool-key", "A bool to print")]
    public bool $aBool;

    #[VectorType("vector-key", "A position to print")]
    public bool $aVector;

    // You can leave out the arguments of IntType here
    /**
     * @var int[]
     */
    #[IntType()]
    #[ListType("multiple-numbers", "an array of numbers")]
    public array $multipleNumbers;

    /**
     * @var string[]
     */
    #[StringType()]
    // New Lines (\n) can be used too.
    #[ListType("many-strings", "a random assortment of strings\nfeel free to change these strings!")]
    public array $manyStrings;

    // Sub-objects can be saved to config as well. They must
    // have attributes annotating them to save and load properly
    #[ObjectType(PrintSettings::class, "print-settings", "Settings for how to\nprint the strings in `many-strings`\nto the console")]
    public PrintSettings $print_settings;

    public static function getDefaults(): array
    {
        // Notice that the key is the same as the config-key
        // on the attributes
        return [
            'message-repeat-count' => 2,
            'float-key' => 5.2,
            'string-key' => "Hello, config!",
            'bool-key' => true,
            // The values of this array must be fully initialized
            // In practice that means you must use the `new` operator
            // for both the VectorType and also ObjectType.
            'vector-key' => new Vector3(5, 2, 3),
            'multiple-numbers' => [2, 3],
            'many-strings' => ["Many", "Strings", "To", "Print"],
            'print-settings' => new PrintSettings(0),
        ];
    }
}
