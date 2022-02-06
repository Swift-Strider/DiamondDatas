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

namespace DiamondStrider1\DiamondDatas;

class ConfigContext
{
    private string $nestedKeys = '<root>';
    private int $depth = 0;

    public function __construct(
        private string $file
    ) {
    }

    public function addKey(string|int $key): self
    {
        if (\is_int($key)) {
            $key = "[{$key}]";
        } else {
            $key = ".{$key}";
        }

        $context = new self($this->file);
        $context->nestedKeys = $this->nestedKeys.$key;
        $context->depth = $this->depth + 1;

        return $context;
    }

    public function getNestedKeys(): string
    {
        return $this->nestedKeys;
    }

    public function getDepth(): int
    {
        return $this->depth;
    }

    public function getFile(): string
    {
        return $this->file;
    }
}
