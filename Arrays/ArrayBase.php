<?php

namespace t35\Library\Arrays;

use ArrayAccess;
use Countable;
use Exception;
use Iterator;
use JetBrains\PhpStorm\Pure;
use OutOfBoundsException;
use t35\Library\BaseClass;
use t35\Library\Callback;
use t35\Library\EFailedValueType;
use t35\Library\EInclusionStatus;
use t35\Library\FailedValue;
use t35\Library\Strings\EStringFormat;
use t35\Library\IJSONSerializable;
use t35\Library\SimpleLibrary;
use t35\Library\Exceptions\stdException;
use t35\Library\ValidatingMethods;
use function array_key_exists;
use function array_keys;
use function in_array;

/**
 * Контейнер для массива
 */
class ArrayBase extends BaseClass implements Iterator, ArrayAccess, Countable, IJSONSerializable {
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
        return array_key_exists($offset, $this->box);
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
        return \count($this->box);
    }

    /**
     * Считается ли массив пустым.
     *
     * @return bool
     */
    #[Pure] public function isEmpty(): bool {
        return !$this->count();
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
        return in_array($value, $this->box);
    }

    /**
     * Замена функции array_keys.
     * @return array
     * @see \t35\Library\array_keys().
     */
    public function array_keys(): array {
        return array_keys($this->box);
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
     *
     * @param callable $callback
     * @param int $mode По-умолчанию(0) - в callback-функцию передается только значение;
     * ARRAY_FILTER_USE_BOTH(1) - значение и ключ(и именно в таком порядке!);
     * ARRAY_FILTER_USE_KEY(2) - Только ключ.
     * @return static
     * @see \array_filter()
     */
    public function filter(callable $callback, int $mode = 0): static {
        return $this->similar(\array_filter($this->box, $callback, $mode));
    }

    /**
     * Возвращает новый массив, отобранный по списку ключей.
     * Если список обязательный, вернет пустой массив, если не все поля найдены.
     *
     * @param ListWithInclusionStatus $list Список ключей с информацией об обязательности. Список может быть "белым" или "черным".
     * @return static
     * @see EInclusionStatus
     * @see ListWithInclusionStatus
     */
    public function filterByList(
        ListWithInclusionStatus $list
    ): static {
        $callbackInList = new Callback\CallbackValueInList($list);
        $new = $this->filter($callbackInList, ARRAY_FILTER_USE_KEY);
        if ($list->requireStatus() == EInclusionStatus::Require) {
            return $list->count() == $new->count() ? $new : $this->similar();
        }

        return $new;
    }

    /**
     * Возвращает новый массив, отобранный по набору проверок, либо выполняет FailedValue.
     *
     * @param ArrayValidScheme $validScheme Массив-набор проверок. Ключ - ключ массива, значение - callback-функция проверки или VM_-константа, или массив таких по логике "И".
     * @param FailedValue $failedValue
     * @return static
     * @throws stdException|\t35\Library\Exceptions\FailedValue
     * @see FailedValue
     * @see ArrayValidScheme
     */
    public function filterByValidScheme(
        ArrayValidScheme $validScheme,
        FailedValue      $failedValue = new FailedValue(null, EFailedValueType::Exception)
    ): mixed {
        $new = $this->similar();

        foreach ($filtered = $this->filterByList(new ListWithInclusionStatus($validScheme->array_keys(), $validScheme->inclusionStatus())) as $key => $value) {
            if ($validScheme->inclusionStatus() == EInclusionStatus::BlackList) {
                $new->putAll($filtered);
                break;
            }
            else {
                if (($new_value = $this->getValid($key, $validScheme[$key], new FailedValue(null))) !== null)
                    $new[$key] = $new_value;
                elseif ($validScheme->inclusionStatus() == EInclusionStatus::Require)
                    return $failedValue->Get(new \t35\Library\Exceptions\FailedValue(
                        'Массив не прошел проверку callback-функцией(-ями)',
                        [
                            'key' => $key,
                            'callbacks' => $validScheme[$key],
                            'box' => $this->box
                        ]
                    ));
            }
        }

        if (!$new->count())
            return $failedValue->Get(new \t35\Library\Exceptions\FailedValue(
                'Массив не прошел список проверок',
                [
                    'valid_scheme' => $validScheme,
                    'box' => $this->box
                ]
            ));

        return $new;
    }

    /**
     * Применяет callback-функцию ко всем элементам массива. Возвращает себя.
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
     * Возвращает копию массива с применением apply.
     *
     * @param callable $callback Зависит от apply.
     * @return static
     * @see ArrayBase::apply()
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
     * @param ListCallables $callbackList Callback-функция(или массив таких функций), которая принимает один аргумент: значение массива, и возвращает ответ в виде bool-значения.
     * @param FailedValue $failedValue Возвращаемое значение(или выбрасываемое исключение).
     * @return mixed
     * @throws \t35\Library\Exceptions\FailedValue
     * @throws stdException
     */
    public static function ValueInArray(
        ArrayBase     $array,
        mixed         $key,
        ListCallables $callbackList = new ListCallables(),
        FailedValue   $failedValue = new FailedValue(null)
    ): mixed {
        try {
            $value = $array[$key];
        }
        catch (OutOfBoundsException $exception) {
            return $failedValue->Get(new \t35\Library\Exceptions\FailedValue(
                $exception->getMessage(),
                $array,
                null,
                $exception->getCode()
            ));
        }

        try {
            if ($callbackList->count())
                return ValidatingMethods::Validated($value, $callbackList, $failedValue);
            else
                return $value;
        }
        catch (Exception $exception) {
            throw new stdException('Значение "' . $key . '" массива не прошло проверку callback-функции', null, stdException::Conversed($exception));
        }
    }

    /**
     * Возвращает проверенное с помощью callback-функции значение массива.
     * Если проверка не пройдена, выполняется FailedValue.
     *
     * @param mixed $key
     * @param ListCallables $callbackList Callback-функция или ArrayBase-массив таких функций(по логике "И"). Можно использовать константы VM_ в качестве callback-функции.
     * @param FailedValue $failedValue Возвращаемое значение(или выбрасываемое исключение).
     * @return mixed
     * @throws stdException
     * @see ArrayBase::ValueInArray(), SimpleLibrary
     * @see FailedValue
     */
    public function getValid(
        mixed         $key,
        ListCallables $callbackList = new ListCallables(),
        FailedValue   $failedValue = new FailedValue(null, EFailedValueType::Exception)
    ): mixed {
        try {
            return self::ValueInArray(
                $this,
                $key,
                $callbackList,
                $failedValue
            );
        }
        catch (Exception $exception) {
            throw new stdException(
                'Значение массива не прошло проверку',
                [
                    'key' => $key,
                    'array' => $this->box,
                    'callback' => $callbackList ?? 'without callback'
                ],
                stdException::Conversed($exception)
            );
        }
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
     * Определяет, можно ли считать массив ассоциативным.
     *
     * @param array|ArrayBase $arr
     * @return bool
     */
    #[Pure] public static function array_is_assoc(array|ArrayAccess $arr): bool {
        if ($arr instanceof ArrayBase) {
            if (\count($arr) == 0) return false;
            return \t35\Library\array_keys($arr) !== range(0, \count($arr) - 1);
        }

        if ($arr instanceof ArrayAccess) {
            if (\t35\Library\count($arr) == 0) return false;
            return \t35\Library\array_keys($arr) !== range(0, \t35\Library\count($arr) - 1);
        }

        if ($arr === array()) return false;
        return \array_keys($arr) !== range(0, \count($arr) - 1);
    }

    /**
     * Является ли массив ассоциативным.
     *
     * @return bool
     * @see ArrayBase::array_is_assoc()
     */
    #[Pure] public function isAssoc(): bool {
        return self::array_is_assoc($this->box);
    }

    /**
     * Возвращает случайный индекс.
     *
     * @return string|int|null
     */
    public function randIndex(): string|int|null {
        $index_num = rand(0, $this->count() - 1);

        $offset = null;
        for ($i = 0; $i <= $index_num; $i++) {
            $offset = $this->key();
            $this->next();
            if (!$this->valid())
                $this->rewind();
        }
        $this->rewind();

        return $offset;
    }

    /**
     * Возвращает случайное значение массива.
     *
     * @return mixed
     */
    public function randValue(): mixed {
        return $this->box[$this->randIndex()];
    }
}
