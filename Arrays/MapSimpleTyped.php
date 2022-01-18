<?php

namespace t35\Library\Arrays;

use t35\Library\Arrays\ArrayTyped;

/**
 * Более строгий ArrayOfObject.
 * Подразумевает только строковые ключи.
 *
 * @uses \t35\Library\Arrays\ArrayTyped
 * @template T
 */
class MapSimpleTyped extends ArrayTyped {
    /**
     * Реализация $box с измененным PHPDoc.
     *
     * @var array<string, T>
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
     * Переопределена из ArrayOfObjects. Добавлено ограничение типа на ключ элементов массива. Только строковые(string) ключи.
     *
     * @param string $offset
     * @param T $value
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
