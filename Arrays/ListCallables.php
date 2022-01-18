<?php

namespace t35\Library\Arrays;

use InvalidArgumentException;
use t35\Library\Arrays\ArrayBase;
use t35\Library\Arrays\ListSimple;

/**
 * Список callback-функций.
 * При создании объекта, можно передать одну callable-функцию.
 */
class ListCallables extends ListSimple {
    public function __construct(ArrayBase|array $value = null) {
        if (is_callable($value))
            $value = new ArrayBase([$value]);
        parent::__construct($value);
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        if (!is_callable($value))
            throw new InvalidArgumentException(
                'Значение должно быть типа callable, передано типа "' . get_debug_type($value) . '"'
            );
        parent::offsetSet($offset, $value);
    }
}
