<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;
use Traversable;

/**
 * Обертка для класса Ds\Map.
 * Служит для того, чтобы можно было определить тип значений(value) в парах(Ds\Pair)
 */
abstract class MapOfObject implements \Iterator, \ArrayAccess {
    /**
     * @var string Имя класса, только объекты которого допускаются к добавлению в коллекцию пар(Ds\Map)
     */
    public readonly string $classOfObjects;

    protected readonly Map $map;

    public function __construct(
        string $classOfObjects,
        mixed  $initMapValue = null
    ) {
        $this->map = new Map();

        $this->classOfObjects = $classOfObjects;

        if ($initMapValue !== null) {
            $this->putAll($initMapValue);
        }
    }

    protected int $position = 0;

    //Блок собственно Map-методов

    /**
     * Имплементация Ds\Map.
     * @param int $capacity
     */
    public function allocate(int $capacity): void {
        $this->map->allocate($capacity);
    }

    /**
     * Имплементация Ds\Map.
     * @param callable $callback
     */
    public function apply(callable $callback): void {
        $this->map->apply($callback);
    }

    /**
     * Имплементация Ds\Map.
     * @return int
     */
    #[Pure] public function capacity(): int {
        return $this->map->capacity();
    }

    /**
     * Затычка. Нужно добавить метод.
     * _CREATE_METHOD: public function diff(class__MapOfObject $object): class__MapOfObject {return $this->p_diff($object);}
     * _TODO: return $this;
     * @param static $object
     * @return static
     */
    abstract protected function _todo_diff(MapOfObject $object): MapOfObject;

    /**
     * Имплементация \Ds\Map.
     * @param MapOfObject $object
     * @return MapOfObject
     */
    protected function p_diff(MapOfObject $object): MapOfObject {
        return new static($this->classOfObjects, $this->map->diff($object->map));
    }

    /**
     * Должен возвращать MapOfObject.
     * _RETURN_TYPE: class_MapOfObject.
     * _TODO: return $this->p_filter($callback);
     * @param callable $callback
     * @return static
     */
    abstract public function filter(callable $callback): MapOfObject;

    /**
     * Имплементация Ds\Map.
     * @param callable $callback
     * @return static
     */
    protected function p_filter(callable $callback): MapOfObject {
        return new static($this->classOfObjects, $this->map->filter($callback));
    }

    /**
     * Имплементация Ds\Map.
     * @return Pair
     */
    // TODO: Надо придумать, как возвращать Pair с нужным типом value
    public function first(): Pair {
        return $this->map->first();
    }

    /**
     * Должен возвращать элемент MapOfObject по ключу.
     * _RETURN_TYPE: class__Object.
     * _TODO: return $this->p_get($key, $default);
     */
    abstract public function get(mixed $key, $default = null);

    /**
     * Имплементация Ds\Map.
     * @param mixed $key
     * @param null $default
     * @return mixed
     */
    protected function p_get(mixed $key, $default = null): mixed {
        return $this->map->get($key, $default);
    }

    /**
     * Имплементация Ds\Map.
     * @param mixed $key
     * @return bool
     */
    public function hasKey(mixed $key): bool {
        return $this->map->hasKey($key);
    }

    /**
     * Имплементация Ds\Map.
     * @param mixed $value
     * @return bool
     */
    public function hasValue(mixed $value): bool {
        return $this->map->hasValue($value);
    }

    /**
     * Затычка. Нужно добавить метод.
     * _CREATE_METHOD: public function intersect(class__MapOfObject $object): class__MapOfObject {return $this->p_intersect($object);}
     * _TODO: return $this;
     * @param MapOfObject $object
     * @return static
     */
    abstract public function _todo_intersect(MapOfObject $object): MapOfObject;

    /**
     * Имплементация Ds\Map.
     * @param MapOfObject $object
     * @return static
     */
    protected function p_intersect(MapOfObject $object): MapOfObject {
        return new static($this->classOfObjects, $this->map->intersect($object->map));
    }

    /**
     * Имплементация Ds\Map.
     * @return Set
     */
    public function keys(): Set {
        return $this->map->keys();
    }

    /**
     * Имплементация Ds\Map.
     * @param callable|null $comparator
     */
    public function ksort(callable $comparator = null): void {
        $this->map->ksort($comparator);
    }

    /**
     * Должен возвращать MapOfObject.
     * _RETURN_TYPE: class__MapOfObject.
     * _TODO: return $this->p_ksorted($comparator);
     * @param callable|null $comparator
     * @return static
     */
    abstract public function ksorted(callable $comparator = null): MapOfObject;

    /**
     * Имплементация Ds\Map.
     * @param callable|null $comparator
     * @return $this
     */
    protected function p_ksorted(callable $comparator = null): MapOfObject {
        return new static($this->classOfObjects, $this->map->ksorted($comparator));
    }

    /**
     * Имплементация Ds\Map.
     * @return Pair
     */
    // TODO: Надо придумать, как возвращать Pair с нужным типом value
    public function last(): Pair {
        return $this->map->last();
    }

    /**
     * Должен возвращать MapOfObject.
     * _RETURN_TYPE: class__MapOfObject.
     * _TODO: return $this->p_map($callback);
     * @param callable $callback
     * @return static
     */
    abstract public function map(callable $callback): MapOfObject;

    /**
     * Имплементация Ds\Map.
     * @param callable $callback
     * @return $this
     */
    protected function p_map(callable $callback): MapOfObject {
        return new static($this->classOfObjects, $this->map->map($callback));
    }

    /**
     * _RETURN_TYPE: class__MapOfObject.
     * _TODO: return $this->p_merge($value);
     * @return static
     */
    abstract public function merge(Traversable|array $value): MapOfObject;

    /**
     * Имплементация Ds\Map.
     * @param Traversable|array $value
     * @return static
     */
    protected function p_merge(Traversable|array $value): MapOfObject {
        foreach ($value as $item) {
            if (!($item instanceof $this->classOfObjects)) {
                throw new \InvalidArgumentException('Коллекция пар(Ds\Map) типа "' . static::class . '" может содержать только объекты класса "' . $this->classOfObjects . '"');
            }
        }

        return new static($this->classOfObjects, $this->map->merge($value));
    }

    /**
     * Имплементация Ds\Map.
     * @return Sequence
     */
    // TODO: Было бы неплохо возвращать последовательность пар, значения(value) которых были бы нужного класса
    public function pairs(): Sequence {
        return $this->map->pairs();
    }

    /**
     * Затычка. Нужно добавить метод.
     * _CREATE_METHOD: public function put(mixed $key, class__Object $value): void {return $this->p_put($key, $value);}
     * _TODO: EMPTY
     * @param mixed $key
     * @param $value
     */
    abstract protected function _todo_put(mixed $key, $value): void;

    /**
     * Имплементация Ds\Map.
     * @param mixed $key
     * @param mixed $value
     */
    public function p_put(mixed $key, mixed $value): void {
        $this->map->put($key, $value);
    }

    /**
     * Имплементация Ds\Map.
     * @param Traversable|array $value
     */
    public function putAll(Traversable|array $value): void {
        foreach ($value as $key => $item) {
            if (!($item instanceof $this->classOfObjects)) {
                throw new \InvalidArgumentException('Коллекция пар(Ds\Map) типа "' . static::class . '" может содержать только объекты класса "' . $this->classOfObjects . '"');
            }

            $this->map->put($key, $item);
        }
    }

    public function reduce(callable $callback, mixed $initial = null): mixed {
        return $this->map->reduce($callback, $initial);
    }

    abstract public function remove(mixed $key, mixed $default = null);

    public function reverse(): void {
        $this->map->reverse();
    }

    abstract public function reversed();

    public function skip(int $position): Pair {
        return $this->map->skip($position);
    }

    abstract public function slice(int $offset, int $length = null);

    public function sort(callable $comparator = null): void {
        $this->map->sort($comparator);
    }

    abstract public function sorted(callable $comparator = null);

    public function sum(): int|float {
        return $this->map->sum();
    }

    abstract public function union(MapOfObject $object);

    public function values(): Sequence {
        return $this->map->values();
    }

    abstract public function xor(MapOfObject $object);

    //Блок методов интерфейса Collection
    function clear(): void {
        $this->map->clear();
    }

    /**
     * Затычка. Нужно добавить метод.
     * _CREATE_METHOD: public function copy(): _class__MapOfObject {return $this->p_copy();}
     * _TODO: return $this;
     * @return MapOfObject
     */
    abstract protected function _todo_copy(): MapOfObject;

    /**
     * Имплементация \Ds\Map.
     * @return static
     */
    protected function p_copy(): MapOfObject {
        return new static($this->classOfObjects, $this->map->copy());
    }

    /**
     * Имплементация Ds\Map.
     * @return bool
     */
    function isEmpty(): bool {
        return $this->map->isEmpty();
    }

    /**
     * Имплементация Ds\Map.
     * @return array
     */
    #[Pure] function toArray(): array {
        return $this->map->toArray();
    }

    /**
     * Имплементация Ds\Map.
     * @return int
     */
    #[Pure] function count(): int {
        return $this->map->count();
    }

    /**
     * Имплементация Ds\Map.
     * @return mixed
     */
    public function jsonSerialize(): mixed {
        return $this->map->jsonSerialize();
    }

    //Блок методов интерфейса ArrayAccess

    /**
     * Имплементация ArrayAccess.
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool {
        return $this->map->offsetExists($offset);
    }

    /**
     * Имплементация ArrayAccess.
     * @param mixed $offset
     */
    public function offsetUnset(mixed $offset): void {
        $this->map->offsetUnset($offset);
    }

    /**
     * Должен возвращать элемент MapOfObject.
     * _RETURN_TYPE: class__Object.
     * _TODO: return $this->p_offsetGet($offset);
     * @param mixed $offset
     */
    abstract function offsetGet(mixed $offset);

    /**
     * Имплементация \ArrayAccess. Возвращает элемент Map по ключу $offset.
     * @param mixed $offset
     * @return mixed
     */
    protected function p_offsetGet(mixed $offset): mixed {
        return $this->map->offsetGet($offset);
    }

    /**
     * _TODO: return $this->p_offsetSet($offset, $value);
     * @param mixed $offset
     * @param mixed $value
     * @return void
     */
    abstract public function offsetSet(mixed $offset, mixed $value): void;

    /**
     * Имплементация \ArrayAccess. Добавляет элемент в Map с ключом $offset.
     * @param mixed $offset
     * @param mixed $value
     */
    protected function p_offsetSet(mixed $offset, mixed $value): void {
        if (!($value instanceof $this->classOfObjects))
            throw new \InvalidArgumentException('Элемент "' . static::class . '" должен быть класса "' . $this->classOfObjects . '". Передан "' . get_debug_type($value) . '"');
        $this->map->offsetSet($offset, $value);
    }

    //Блок методов интерфейса Iterator

    /**
     * Имплементация Iterator.
     */
    public function next(): void {
        ++$this->position;
    }

    /**
     * Имплементация Iterator.
     * @return int
     */
    public function key(): int {
        return $this->position;
    }

    /**
     * Имплементация Iterator.
     * @return bool
     */
    #[Pure] public function valid(): bool {
        return $this->position >= 0 && $this->position < $this->map->count();
    }

    /**
     * Имплементация Iterator.
     */
    public function rewind(): void {
        $this->position = 0;
    }

    /**
     * Должен возвращать элемент MapOfObject.
     * _RETURN_TYPE: class__Object.
     * _TODO: return $this->p_current();
     */
    abstract public function current();

    /**
     * Имплементация \Iterator. Возвращает текущий элемент Map.
     * @return mixed
     */
    protected function p_current(): mixed {
        return $this->map->skip($this->position)->value;
    }
}
