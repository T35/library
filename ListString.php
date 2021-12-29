<?php

namespace t35\Library;

class ListString extends ListSimple {
    /**
     * Реализация $box с измененным PHPDoc.
     *
     * @var array<int, string>
     */
    protected array $box = [];

    public function offsetSet(mixed $offset, mixed $value): void {
        if (!is_string($value)) {
            throw new \InvalidArgumentException(
                'Элемент "' . static::class . '" должен быть класса строкой(string). Передан "' . get_debug_type($value) . '"'
            );
        }

        parent::offsetSet(null, $value);
    }
}
