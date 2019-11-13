<?php

namespace Boomdraw\AutoFinder\Test\Integration\Currencies;

abstract class Driver
{
    /**
     * Returns supported currencies
     *
     * @return array
     */
    abstract public static function supported(): array;

    /**
     * Check is currency supported by driver
     *
     * @param string|array ...$currencies
     * @return bool
     */
    public static function isSupported(...$currencies): bool
    {
        if (is_array($currencies[0])) {
            $currencies = $currencies[0];
        }

        return !array_diff($currencies, static::supported());
    }
}
