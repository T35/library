<?php

namespace t35\Library;

use ArrayAccess;
use Countable;
use JetBrains\PhpStorm\Pure;
use t35\Library\Arrays\ArrayBase;

/**
 * Расширение array_key_exists. Стандартная функция работает только с array.
 *
 * @param string|int $key
 * @param array|ArrayAccess $array
 * @return bool
 * @see ArrayBase
 */
function array_key_exists(string|int $key, array|ArrayAccess $array): bool {
    if ($array instanceof ArrayAccess)
        return $array->offsetExists($key);
    return \array_key_exists($key, $array);
}

/**
 * Расширение in_array. Стандартная функция работает только с array.
 *
 * @param mixed $needle
 * @param array|ArrayAccess $array
 * @return bool
 * @see ArrayBase
 */
#[Pure] function in_array(mixed $needle, array|ArrayAccess $array): bool {
    if ($array instanceof ArrayBase)
        return $array->in_array($needle);
    if ($array instanceof ArrayAccess) {
        foreach ($array as $item) {
            if ($item === $needle)
                return true;
        }
        return false;
    }
    return \in_array($needle, $array);
}

/**
 * Расширение array_keys. Стандартная функция работает только с array.
 *
 * @param array|ArrayAccess $array |ArrayAccess $array
 * @return array
 * @see ArrayBase
 */
#[Pure] function array_keys(array|ArrayAccess $array): array {
    if ($array instanceof ArrayBase) {
        return $array->array_keys();
    }
    if ($array instanceof ArrayAccess) {
        $keys = [];
        foreach ($array as $key => $value) {
            $keys[] = $key;
        }
        return $keys;
    }
    return \array_keys($array);
}

/**
 * Расширение count. Стандартная функция работает только с array|Countable.
 *
 * @param array|Countable|ArrayAccess $value |ArrayAccess $array
 * @return int
 * @see ArrayBase
 */
function count(array|Countable|ArrayAccess $value): int {
    if ($value instanceof Countable)
        return \count($value);

    if ($value instanceof ArrayAccess) {
        return \count(array_keys($value));
    }

    return \count($value);
}

/**
 * Расширение is_array. ArrayBase также считается массивом.
 *
 * @param mixed $value
 * @return bool
 */
function is_array(mixed $value): bool {
    if ($value instanceof ArrayBase)
        return true;
    return \is_array($value);
}

/**
 * Расширение array_filter.
 *
 * @param array|ArrayBase $array
 * @param callable $callback
 * @param int $mode
 * @return array|ArrayBase
 */
function array_filter(array|ArrayBase $array, callable $callback, int $mode = 0): array|ArrayBase {
    if ($array instanceof ArrayBase) {
        return $array->filter($callback, $mode);
    }
    return \array_filter($array, $callback, $mode);
}
