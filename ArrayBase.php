<?php

namespace t35\Library;

use ArrayAccess;
use Countable;
use Iterator;
use JetBrains\PhpStorm\Pure;
use OutOfBoundsException;
use t35\Library\Callback;
use TypeError;

/**
 * Контейнер для массива
 */
class ArrayBase implements Iterator, ArrayAccess, Countable, IJSONSerializable {
    /**
     * Собственно, контейнер для данных массива.
     *
     * @var array
     */
    protected array $box = [];

    public function __construct(array|ArrayBase $value = null) {
        if ($value !== null)
            $this->putAll($value);
    }

    /**
     * Преобразование значения в объект ArrayBase static класса.
     * Например, параметры типа array вначале всех методов, для удобства, нужно преобразовать в объект ArrayBase static класса.
     *
     * @param mixed $value
     * @return void
     */
    public static function Converse(mixed &$value): void {
        if (!($value instanceof ArrayBase)) {
            $value = new static($value);
        }
    }

    /**
     * Возвращает такой же массив, но с новыми данными внутреннего массива(box) или пустой.
     * Подразумевается, что какие-то дополнительные настройки должны быть сохранены. Например, класс типизированного массива.
     *
     * @param array|ArrayBase|null $value
     * @return $this
     * @see ArrayTyped
     */
    public function similar(array|ArrayBase $value = null): static {
        return new static($value);
    }

    /**
     * Реализация Iterator.
     *
     * @return mixed
     */
    public function current(): mixed {
        return current($this->box);
    }

    /**
     * Реализация Iterator.
     */
    public function next(): void {
        next($this->box);
    }

    /**
     * Реализация Iterator.
     *
     * @return int|string|null
     */
    public function key(): int|string|null {
        return key($this->box);
    }

    /**
     * Реализация Iterator.
     *
     * @return bool
     */
    #[Pure] public function valid(): bool {
        return key($this->box) !== null;
    }

    /**
     * Реализация Iterator.
     */
    public function rewind(): void {
        reset($this->box);
    }

    /**
     * Реализация ArrayAccess.
     *
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool {
        return \array_key_exists($offset, $this->box);
    }

    /**
     * Реализация ArrayAccess.
     *
     * @param mixed $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed {
        return $this->box[$offset] ?? throw new OutOfBoundsException('Ключ "' . $offset . '" отсутствует в массиве');
    }

    /**
     * Реализация ArrayAccess.
     *
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        if ($offset === null) {
            $this->box[] = $value;
        }
        else {
            $this->box[$offset] = $value;
        }
    }

    /**
     * Реализация ArrayAccess.
     *
     * @param mixed $offset
     */
    public function offsetUnset(mixed $offset): void {
        unset($this->box[$offset]);
    }

    /**
     * Реализация Countable.
     *
     * @return int
     */
    #[Pure] public function count(): int {
        return count($this->box);
    }

    /**
     * Добавление элементов из другого массива или объекта ArrayBase.
     *
     * @param array|ArrayBase $value
     * @param bool $use_keys
     * @return void
     */
    public function putAll(array|ArrayBase $value, bool $use_keys = true): void {
        foreach ($value as $key => $item) {
            $this->offsetSet($use_keys ? $key : null, $item);
        }
    }

    /**
     * Замена функции in_array.
     *
     * @param mixed $value
     * @return bool
     * @see \t35\Library\in_array()
     */
    public function in_array(mixed $value): bool {
        return \in_array($value, $this->box);
    }

    /**
     * Замена функции array_keys.
     * @return array
     * @see \t35\Library\array_keys().
     */
    public function array_keys(): array {
        return \array_keys($this->box);
    }

    /**
     * Замена функции array_values.
     * @return array
     */
    public function array_values(): array {
        return array_values($this->box);
    }

    /**
     * Замена функции array_key_exists.
     *
     * @param mixed $key
     * @return bool
     */
    #[Pure] public function array_key_exists(mixed $key): bool {
        return $this->offsetExists($key);
    }

    /**
     * Возвращает новый массив, отобранный по callback-функции.
     * Если указан mode ARRAY_FILTER_USE_BOTH(1), то в callback-функции параметры идут в следующем порядке: значение, ключ.
     *
     * @param callable $callback
     * @param int $mode
     * @return static
     */
    public function filter(callable $callback, int $mode = 0): static {
        return $this->similar(array_filter($this->box, $callback, $mode));
    }

    /**
     * Возвращает новый массив, отобранный по списку ключей.
     *
     * @param ListSimple $list Список ключей.
     * @param bool $strict Если строго, то вернет пустой массив, если есть не все элементы из списка.
     * @return static
     */
    public function filterByList(ListSimple $list, bool $strict = false): static {
        $callbackInList = new Callback\CallbackValueInList($list);
        $new = $this->filter($callbackInList, ARRAY_FILTER_USE_KEY);
        if ($strict)
            return $list->count() == $new->count() ? $new : $this->similar();

        return $new;
    }

    /**
     * Применяет callback-функцию ко всем элементам массива.
     *
     * @param callable $callback Должен принимать два параметра: 1 - ключ, 2 - значение.
     * @return static
     */
    public function apply(callable $callback): static {
        foreach ($this->box as $key => $value) {
            $this->box[$key] = $callback($key, $value);
        }

        return $this;
    }

    /**
     * Возвращает копию массива с примененной ко всем значениям callback-функцией.
     *
     * @param callable $callback
     * @return $this
     */
    public function map(callable $callback): static {
        return $this->similar($this->box)->apply($callback);
    }

    /**
     * Возвращает первый элемент массива.
     *
     * @return mixed
     */
    public function first(): mixed {
        return $this->getSafe(array_key_first($this->box));
    }

    /**
     * Возвращает последний элемент массива.
     *
     * @return mixed
     */
    public function last(): mixed {
        return $this->getSafe(array_key_last($this->box));
    }

    /**
     * Возвращает результат добавления всех значений переданного массива в исходный массив(box).
     * Сливание массивов происходит по принципу функции array_merge.
     *
     * @param array|ArrayBase $array
     * @return static
     * @see array_merge()
     */
    public function merge(array|ArrayBase $array): static {
        ArrayBase::Converse($array);

        return $this->similar(array_merge($this->toArray(), $array->toArray()));
    }

    /**
     * Возвращает массив типа array.
     *
     * @return array
     */
    public function toArray(): array {
        return $this->box;
    }

    /**
     * Возвращает массив в виде var_dump-строки в нужном формате.
     *
     * @param EStringFormat $format
     * @return string
     * @throws stdException
     */
    public function toVarDump(EStringFormat $format = EStringFormat::None): string {
        return SimpleLibrary::GetVarDump($this->box, $format);
    }

    /**
     * Реализация IJSONSerializable
     *
     * @return array
     * @see IJSONSerializable
     */
    public function JSONSerialize(): array {
        return $this->map(
            function ($key, $value) {
                if ($value instanceof IJSONSerializable) {
                    return $value->JSONSerialize();
                }
                return $value;
            }
        )->toArray();
    }

    /**
     * Возвращает реализацию массива в виде JSON-строки.
     *
     * @return string
     */
    public function toJSON(): string {
        return json_encode($this->JSONSerialize(), JSON_UNESCAPED_UNICODE);
    }

    /**
     * Реализация __toString().
     *
     * @return string
     * @see IJSONSerializable
     */
    public function __toString(): string {
        return $this->toJSON();
    }

    /**
     * Проверят значение массива на наличие и соответствие условиям в callback-функции.
     * Если проверка не пройдена, выбрасывает исключение или возвращает null.
     *
     * @param ArrayBase $array |ArrayBase $array $array Входной массив, который предположительно содержит искомое значение.
     * @param mixed $key Ключ искомого значения во входном массиве.
     * @param false|callable|ArrayBase $callback Callback-функция(или массив таких функций), которая принимает один аргумент: значение массива, и возвращает ответ в виде bool-значения.
     * @param bool $throw_exception Если true - выбрасывает исключение. Если false - возвращает null.
     * @return mixed
     * @throws stdException
     */
    public static function ValueInArray(
        ArrayBase                $array,
        mixed                    $key,
        false|callable|ArrayBase $callback = false,
        bool                     $throw_exception = true
    ): mixed {
        try {
            $value = $array[$key];
        }
        catch (OutOfBoundsException $exception) {
            if ($throw_exception) {
                throw new stdException(
                    $exception->getMessage(),
                    $array,
                    null,
                    $exception->getCode()
                );
            }

            return null;
        }

        try {
            if ($callback !== false)
                return ValidatingMethods::Validated($value, $callback, $throw_exception);
            else
                return $value;
        }
        catch (stdException $exception) {
            throw new stdException('Значение "' . $key . '" массива не прошло проверку callback-функции', null, $exception);
        }
    }

    /**
     * Возвращает проверенное с помощью callback-функции значение массива.
     * Если проверка не пройдена, выбрасывает исключение или возвращает null.
     *
     * @param mixed $key
     * @param false|callable|ArrayBase $callback Callback-функция или ArrayBase-массив таких функций(по логике "И"). Можно использовать константы VM_ в качестве callback-функции.
     * @param bool $throw_exception Если true - выбрасывает исключение. Если false - возвращает null.
     * @return mixed
     * @throws stdException
     * @see ArrayBase::ValueInArray(), SimpleLibrary
     */
    public function getValid(
        mixed                    $key,
        false|callable|ArrayBase $callback = false,
        bool                     $throw_exception = true
    ): mixed {
        try {
            return self::ValueInArray(
                $this,
                $key,
                $callback,
                $throw_exception
            );
        }
        catch (stdException $exception) {
            throw new stdException(
                'Значение массива не прошло проверку',
                [
                    'key' => $key,
                    'array' => $this->box,
                    'callback' => $callback ?? 'without callback'
                ],
                $exception
            );
        }
    }

    /**
     * Проверяет значения массива по набору проверок.
     *
     * @param ArrayBase $valid_scheme Массив-набор проверок. Ключ - ключ массива, значение - callback-функция проверки или VM_-константа, или массив таких по логике "И".
     * @param bool $strict Строгость для набора ключей. Если true, положительный результат будет только в том случае, когда все ключи найдены.
     * @param bool $throw_exception Если true, в случае отрицательного результата выбросится исключение. В противном случае будет возвращен массив с удачными проверками.
     * @return static
     * @throws stdException
     */
    public function getValidByScheme(
        ArrayBase $valid_scheme,
        bool      $strict = true,
        bool      $throw_exception = true
    ): static {
        $new = $this->similar();
        foreach ($this->filterByList(new ListSimple($valid_scheme->array_keys()), $strict) as $key => $value) {
            if (($new_value = $this->getValid($key, $valid_scheme[$key], $throw_exception)) !== null)
                $new[$key] = $new_value;
        }

        if ($throw_exception && !$new->count())
            throw new stdException(
                'Массив не прошел список проверок',
                [
                    'strict' => $strict,
                    'valid_scheme' => $valid_scheme,
                    'box' => $this->box
                ]
            );

        return $new;
    }

    /**
     * Безопасный offsetGet. Если переданного ключа массива не существует, вернет $failed_value.
     *
     * @param mixed $offset
     * @param mixed|null $failed_value Возвращает это значение в случае неудачи.
     * @return mixed
     */
    public function getSafe(mixed $offset, mixed $failed_value = null): mixed {
        try {
            return $this->offsetGet($offset);
        }
        catch (OutOfBoundsException $exception) {
            return $failed_value;
        }
    }

    /**
     * Безопасный getValid. Если проверка не пройдена, вернет $failed_value.
     *
     * @see getValid()
     * @param mixed $key
     * @param false|callable|ArrayBase $callback
     * @param bool $failed_value Возвращает это значение в случае неудачи.
     * @return mixed
     * @throws stdException
     */
    public function getValidSafe(
        mixed $key,
        false|callable|ArrayBase $callback = false,
        bool $failed_value = null
    ): mixed {
        return $this->getValid($key, $callback, false) ?? $failed_value;
    }
}
