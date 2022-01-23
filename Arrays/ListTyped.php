<?php

namespace t35\Library\Arrays;

use JetBrains\PhpStorm\Pure;
use t35\Library\Arrays\ArrayTyped;

/**
 * @template T
 */
class ListTyped extends ArrayTyped {
    public function offsetSet(mixed $offset, mixed $value): void {
        if (!($value instanceof $this->classOfObjects)) {
            throw new \InvalidArgumentException(
                'Элемент "' . static::class . '" должен быть класса "' . $this->classOfObjects . '". Передан "' . get_debug_type($value) . '"'
            );
        }

        parent::offsetSet(null, $value);
    }

    /**
     * Реализация.
     *
     * @return string|int|null
     * @see ArrayBase::randIndex()
     */
    public function randIndex(): string|int|null {
        return rand(0, $this->count() - 1);
    }
}
