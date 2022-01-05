<?php

namespace t35\Library;

class ListString extends ListSimple {
    public function offsetSet(mixed $offset, mixed $value): void {
        if (!($value instanceof StringBase)) {
            throw new \InvalidArgumentException(
                'Элемент "' . static::class . '" должен быть класса строкой "' . StringBase::class . '". Передан "' . get_debug_type($value) . '"'
            );
        }

        parent::offsetSet(null, $value);
    }
}
