<?php

namespace t35\Library\Arrays;

use InvalidArgumentException;
use t35\Library\Exceptions\stdException;
use t35\Library\Strings\StringBase;
use TypeError;

class ListString extends ListTyped {
    public function __construct(ArrayBase|array $value = null) {
        parent::__construct(StringBase::class, $value);
    }

    /**
     * Реализация.
     *
     * @param ArrayBase|array|null $value
     * @return static
     */
    public function similar(ArrayBase|array $value = null): static {
        return new static($value);
    }

    /**
     * @param mixed $offset
     * @param mixed $value
     * @return void
     * @throws stdException
     */
    public function offsetSet(mixed $offset, mixed $value): void {
        if (!($value instanceof StringBase)) {
            try {
                $value = new StringBase($value);
            }
            catch (TypeError $exception) {
                throw new InvalidArgumentException(
                    'Элемент "' . static::class . '" должен быть класса "' . StringBase::class . '", либо приводимым к строке. Передан "' . get_debug_type($value) . '"'
                );
            }
        }

        parent::offsetSet(null, $value);
    }
}
