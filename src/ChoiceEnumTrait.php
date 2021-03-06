<?php

/*
 * This file is part of the "elao/enum" package.
 *
 * Copyright (C) Elao
 *
 * @author Elao <contact@elao.com>
 */

namespace Elao\Enum;

use Elao\Enum\Exception\LogicException;

/**
 * Discover readable enumerated values by returning the enumerated values as keys and their labels as values
 * in {@link \Elao\Enum\ChoiceEnumTrait::choices()}, replacing the need to provide both:
 * - {@link \Elao\Enum\ReadableEnumInterface::readables()}
 * - {@link \Elao\Enum\ReadableEnumInterface::values()}
 *
 * Meant to be used within a {@link \Elao\Enum\ReadableEnumInterface} implementation.
 */
trait ChoiceEnumTrait
{
    /**
     * @see EnumInterface::values()
     *
     * @return int[]|string[]
     */
    public static function values(): array
    {
        self::checkForChoiceEnumTraitMisuses();

        $values = array_keys(static::choices());

        if (is_a(static::class, FlaggedEnum::class, true)) {
            $values = array_values(array_filter($values, function ($v): bool {
                return 0 === ($v & $v - 1);
            }));
        }

        return $values;
    }

    /**
     * @see ReadableEnumInterface::readables()
     *
     * @return string[] labels indexed by enumerated value
     */
    public static function readables(): array
    {
        self::checkForChoiceEnumTraitMisuses();

        return static::choices();
    }

    /**
     * @return string[] The enumerated values as keys and their labels as values.
     */
    abstract protected static function choices(): array;

    /**
     * @internal
     */
    private static function checkForChoiceEnumTraitMisuses()
    {
        if (!is_a(static::class, ReadableEnumInterface::class, true)) {
            throw new LogicException(sprintf(
                'The "%s" trait is meant to be used by "%s" implementations, but "%s" does not implement it.',
                ChoiceEnumTrait::class,
                ReadableEnumInterface::class,
                static::class
            ));
        }
    }
}
