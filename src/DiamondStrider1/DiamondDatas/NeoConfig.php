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

namespace DiamondStrider1\DiamondDatas;

use DiamondStrider1\DiamondDatas\attributes\ObjectType;

/**
 * @template T of object
 */
class NeoConfig
{
    /** @var T|null */
    private ?object $lastObject = null;

    /** @psalm-param class-string<T> $class */
    public function __construct(
        private string $filename,
        private string $class,
    ) {
    }

    /**
     * @return T
     */
    public function getObject(bool $reload = false): object
    {
        if ($this->lastObject && !$reload) {
            return $this->lastObject;
        }

        $context = new ConfigContext($this->filename);
        $object = (new ObjectType($this->class))->fromRaw($this->fetchData(), $context);
        $this->setObject($object);
        return $object;
    }

    /**
     * @param T $object
     */
    public function setObject(object $object): void
    {
        $this->lastObject = $object;
        $context = new ConfigContext($this->filename);
        $contents = (new ObjectType($this->class))->yamlLines($object, $context);
        file_put_contents($this->filename, $contents);
    }

    public function deleteFile(): void
    {
        if (!file_exists($this->filename)) return;
        unlink($this->filename);
    }

    /** @return array<string, mixed> */
    private function fetchData(): array
    {
        $context = new ConfigContext($this->filename);

        if (is_dir($this->filename)) {
            throw new ConfigException("YAML file is a directory: §c\"§7{$this->filename}§c\"", $context);
        }
        if (!file_exists($this->filename)) {
            file_put_contents($this->filename, yaml_emit([]));
        }

        $contents = file_get_contents($this->filename);
        if ($contents === false) {
            throw new ConfigException("Could not load settings", $context);
        }

        $saveData = yaml_parse($contents) ?? []; // Empty file defaults to empty array
        if (!is_array($saveData)) {
            throw new ConfigException("Settings must be in key-value pairs", $context);
        }

        return $saveData;
    }
}
