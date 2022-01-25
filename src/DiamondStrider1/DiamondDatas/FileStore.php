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

class FileStore
{
    /**
     * @param string $foldername Full path to folder without trailing slash
     */
    public function __construct(
        private string $foldername,
    ) {
        $this->foldername = rtrim($foldername, '\\/');
    }

    public function getFolderName(): string
    {
        return $this->foldername;
    }

    /**
     * @return string[]
     */
    public function getAll(): array
    {
        if (!file_exists($this->foldername)) {
            @mkdir($this->foldername);
        }
        if (!is_dir($this->foldername)) {
            throw new ConfigException("There is a file instead of a folder at `" . $this->foldername . "`");
        }
        $dir = dir($this->foldername);
        if ($dir === false) {
            throw new ConfigException("Cannot read directory `" . $this->foldername . "`");
        }

        $entries = [];
        while (($e = $dir->read()) !== false) {
            if ($e === "." || $e === "..") continue;
            $entries[] = $e;
        }
        return $entries;
    }

    public function saveFile(string $fileToCopy, string $newEntryName): void
    {
        if (is_dir($fileToCopy)) {
            self::recursiveCopy($fileToCopy, $this->foldername . '/' . $newEntryName);
        } else {
            copy($fileToCopy, $this->foldername . '/' . $newEntryName);
        }
    }

    public function loadFile(string $entryName, string $pathToCopyTo): void
    {
        $entryFile = $this->foldername . '/' . $entryName;
        if (is_dir($entryFile)) {
            self::recursiveCopy($entryFile, $pathToCopyTo);
        } else {
            copy($entryFile, $pathToCopyTo);
        }
    }

    private static function recursiveCopy(string $src, string $dst): void
    {
        $dir = dir($src);
        if ($dir === false) return;
        if (!file_exists($dst)) {
            mkdir($dst);
        }
        while (($e = $dir->read()) !== false) {
            if ($e === "." || $e === "..") continue;
            $file = $src . "/" . $e;
            if (is_dir($file) && !is_link($file)) {
                self::recursiveCopy($file, $dst . "/" . $e);
            } else {
                copy($file, $dst . '/' . $e);
            }
        }
    }

    public function remove(string $entryName): void
    {
        $file = $this->foldername . '/' . $entryName;
        if (!file_exists($file)) {
            return;
        }

        if (is_dir($file)) {
            self::recursiveDelete($file);
        } else {
            unlink($file);
        }
    }

    private static function recursiveDelete(string $folder): void
    {
        $files = scandir($folder);
        if ($files === false) return;
        foreach ($files as $e) {
            if ($e === "." || $e === "..") continue;
            $file = $folder . "/" . $e;
            if (is_dir($file) && !is_link($file)) {
                self::recursiveDelete($file);
            } else {
                unlink($file);
            }
        }
        rmdir($folder);
    }
}
