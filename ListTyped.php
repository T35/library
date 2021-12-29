<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;

/**
 * @template T
 */
class ListTyped extends ArrayTyped {
    /**
     * Реализация $box с измененным PHPDoc.
     *
     * @var array<int, T>
     */
    protected array $box = [];

    public function offsetSet(mixed $offset, mixed $value): void {
        if (!($value instanceof $this->classOfObjects)) {
            throw new \InvalidArgumentException(
                'Элемент "' . static::class . '" должен быть класса "' . $this->classOfObjects . '". Передан "' . get_debug_type($value) . '"'
            );
        }

        parent::offsetSet(null, $value);
    }

    /**
     * Возвращает последний элемент списка.
     *
     * @return T
     */
    #[Pure] public function last(): mixed {
        return $this->box[$this->count() - 1];
    }
}
