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
 * @package  DiamondDatas
 * @author   DiamondStrider1 <62265561+Swift-Strider@users.noreply.github.com>
 * @license  The Unlicense
 * @link     https://github.com/Swift-Strider/DiamondVirions
 */

declare(strict_types=1);

namespace DiamondStrider1\DiamondDatas;

use DiamondStrider1\DiamondDatas\metadata\IDefaultProvider;
use DiamondStrider1\DiamondDatas\metadata\ISubtypeProvider;
use DiamondStrider1\DiamondDatas\attributes\IValueType;
use DiamondStrider1\DiamondDatas\attributes\ListType;
use ReflectionClass;
use TypeError;

/**
 * @template T of object
 */
class ClassInfo
{
    /** @var self[] */
    private static array $cache = [];

    /**
     * @template V of object
     * @phpstan-param class-string<V> $class
     * @return self<V>
     */
    public static function getInfo(string $class): self
    {
        if (isset(self::$cache[$class])) {
            $classInfo = self::$cache[$class];
        } else {
            $classInfo = self::$cache[$class] = new self($class);
        }
        /** @var self<V> $classInfo */
        return $classInfo;
    }

    /** @phpstan-var class-string<T>[] */
    private array $subtypes;
    /** @var array{\ReflectionProperty, IValueType}[] $props */
    private array $props = [];
    /** @var array<string, mixed> */
    private array $defaults;
    /** @var ReflectionClass<T> */
    private ReflectionClass $reflection;

    /** @phpstan-param class-string<T> $class */
    private function __construct(string $class)
    {
        $this->reflection = new ReflectionClass($class);
        if ($this->reflection->isAbstract()) {
            if (!$this->reflection->implementsInterface(ISubtypeProvider::class)) {
                throw new TypeError("Abstract Class does not implement ISubtypeProvider");
            }
            /** @phpstan-var class-string<T>[] $subtypes */
            $subtypes = $this->reflection->getMethod("getSubtypes")->invoke(null);
            $this->subtypes = $subtypes;
            return;
        }
        foreach ($this->reflection->getProperties() as $rProp) {
            if ($rProp->isStatic()) {
                continue;
            }
            $inject = null;
            foreach ($rProp->getAttributes() as $attr) {
                $rAttr = new ReflectionClass($attr->getName());
                if (
                    $rAttr->isSubclassOf(IValueType::class) &&
                    $rAttr->getName() !== ListType::class
                ) {
                    $listType = $rProp->getAttributes(ListType::class)[0] ?? null;
                    if ($listType) {
                        /** @var IValueType $other */
                        $other = $attr->newInstance();
                        $inject = $listType->newInstance();
                        /** @var ListType $inject */
                        $inject->setType($other);
                    } else {
                        $inject = $attr->newInstance();
                    }
                    break;
                }
            }
            if ($inject === null) {
                continue;
            }
            /** @var IValueType $inject */
            $this->props[] = [$rProp, $inject];
        }
        if ($this->reflection->implementsInterface(IDefaultProvider::class)) {
            /** @var array<string, mixed> $defaults */
            $defaults = $this->reflection->getMethod("getDefaults")->invoke(null);
            $this->defaults = $defaults;
        }
    }

    /** @phpstan-return class-string<T>[]|null */
    public function getSubtypes(): ?array
    {
        return isset($this->subtypes) ? $this->subtypes : null;
    }

    /** @return array{\ReflectionProperty, IValueType}[] $props */
    public function getProps(): array
    {
        return $this->props;
    }

    /** @return array<string, mixed>|null */
    public function getDefaults(): ?array
    {
        return isset($this->defaults) ? $this->defaults : null;
    }

    public function isInstanceOf(mixed $value): bool
    {
        if (!\is_object($value)) {
            return false;
        }
        return $this->reflection->isInstance($value);
    }
}
