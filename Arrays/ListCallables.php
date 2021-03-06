<?php

namespace t35\Library\Arrays;

use InvalidArgumentException;
use t35\Library\Exceptions\stdException;

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

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @throws stdException
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        if (!is_callable($value))
            throw new stdException(
                'Значение должно быть типа callable, передано типа "' . get_debug_type($value) . '"'
            );
        parent::offsetSet($offset, $value);
    }
}
