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

use DiamondStrider1\DiamondDatas\NeoConfig;
use pocketmine\plugin\PluginBase;

/**
 * An example plugin to show off this
 * virions features
 *
 * @category Example
 * @package  DiamondDatas
 * @author   DiamondStrider1 <62265561+Swift-Strider@users.noreply.github.com>
 * @license  The Unlicense
 * @link     https://github.com/Swift-Strider/DiamondVirions
 */
class Plugin extends PluginBase
{
    /** @var NeoConfig<PluginSettings> */
    private NeoConfig $settings;
    public function onEnable(): void
    {
        /*
            IMPORTANT: Your configuration class **must**
            implement IDefaultProvider otherwise if the
            config file `NeoConfig` loads is empty it won't
            be able to load your configuration with default
            values!
        */

        // No side-effects with the `new` operator
        $this->settings = new NeoConfig($this->getDataFolder() . "config.yml", PluginSettings::class);

        // The `true` parameter tells the config to
        // not use any cached object from the last
        // call to getObject(), but instead read from
        // disk.
        /** @var PluginSettings $settings */
        $settings = $this->settings->getObject(true);
        foreach ($settings->manyStrings as $s) {
            // Calling functions of config to consume
            // the users settings
            $settings->print_settings->printMessage($s);
        }
    }
}
