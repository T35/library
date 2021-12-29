<?php

namespace t35\Library;

use Exception;
use JetBrains\PhpStorm\Internal\TentativeType;
use JetBrains\PhpStorm\Pure;
use Traversable;

/**
 * Класс для создания массивов объектов одного класса. Аналог List<type>, с учетом того, что массив может быть ассоциативным.
 * Для того чтобы конструкция работала, необходимо задать объекту PHPDoc array<тип_ключей, type>, либо type[], что эквивалентно первому варианту, где "type" это тип объектов типизированного массива.
 * @link https://blog.jetbrains.com/ru/phpstorm/2021/08/phpstorm-2021-2-release/
 * Чтобы PHPStorm не ругался на отличающиеся типы в объявлении и в PHPDoc, при объявлении указывает дополнительный тип array.
 * Например, чтобы описать свойство $value в некоем классе как список объектов класса Foo, в PHPDoc для него указываем: "@var array<mixed, Foo>", либо "@var Foo[]".
 * Объявляем так: "ArrayOfObject|array $value;". Т.е. добавляем еще и array как возможный тип $value.
 * При инициализации передаем имя класса Foo в качестве аргумента в конструктор: "$this->value = new ArrayOfObjects(Foo::class);".
 * Функции array_key_exists, in_array, array_keys принимают только тип array, поэтому следует применять соответствующие функции из t35\Library.
 *
 * @see array_key_exists(), in_array(), array_keys()
 * @template T
 */
class ArrayTyped extends ArrayBase {
    /**
     * Строка с полным именем класса содержащихся в массиве объектов.
     *
     * @param class-string<T> $classOfObjects
     */
    public function __construct(
        public readonly string $classOfObjects,
        array|ArrayBase $value = null
    ) {
        parent::__construct($value);
    }

    public function similar(ArrayBase|array $value = null): static {
        return new static($this->classOfObjects, $value);
    }

    /**
     * @return class-string<T>
     */
    public function classOfObjects(): string {
        return $this->classOfObjects;
    }

    /**
     * Реализация ArrayAccess.
     *
     * @param mixed $offset
     * @param T $value
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        if (!($value instanceof $this->classOfObjects)) {
            throw new \InvalidArgumentException(
                'Элемент "' . static::class . '" должен быть класса "' . $this->classOfObjects . '". Передан "' . get_debug_type($value) . '"'
            );
        }

        parent::offsetSet($offset, $value);
    }
}
