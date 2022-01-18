<?php

namespace t35\Library\Arrays;

use t35\Library\Arrays\ArrayBase;

/**
 * Коллекция пар ключ-значение. В качестве ключа допускаются ТОЛЬКО строковые значения(string).
 */
class MapSimple extends ArrayBase {
    /**
     * Реализация $box с измененным PHPDoc.
     *
     * @var array<string, mixed>
     */
    protected array $box = [];

    /**
     * Реализация Iterator.
     * Переопределен из ArrayBase с измененным типом возвращаемого значения.
     *
     * @return string|null
     */
    public function key(): string|null {
        return key($this->box);
    }

    /**
     * Реализация ArrayAccess.
     * Переопределена из ArrayBase с добавлением проверки ключа на тип string.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @uses \t35\Library\Arrays\ArrayTyped::offsetSet()
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        if (!is_string($offset))
            throw new \InvalidArgumentException(
                'Ключ массива для элементов "' . self::class . '" должен быть строкой(string). Передан типа "' . get_debug_type($offset) . '"'
            );

        parent::offsetSet($offset, $value);
    }
}
