<?php

namespace t35\Library;

class ValidatingMethods {
    /**
     * Константы VM_(Validating Method) это callback-функции для проверки значения.
     * @see ArrayBase::getValid(), SimpleLibrary::Validated()
     * В качестве callback-функций также можно передать объекты:
     * @see CallbackValueInList, CallbackRegExp
     */
    public const VM_SYSTEM_NAME = [ValidatingMethods::class, 'isSystemName'];
    public const VM_NOT_EMPTY_ARRAY = [ValidatingMethods::class, 'isNotEmptyArray'];
    public const VM_IS_STRING_LIST = [ValidatingMethods::class, 'isStringList'];
    public const VM_IS_NOT_NULL = [ValidatingMethods::class, 'isNotNull'];

    public static function isSystemName(mixed $value): bool {
        return is_string($value)
            ? preg_match(SimpleLibrary::REG_EXP_SYSTEM_NAME, $value)
            : false;
    }

    public static function isNotEmptyArray(mixed $value): bool {
        return is_array($value) && count($value);
    }

    public static function isStringList(mixed $value): bool {
        return
            is_array($value)
            && count(array_filter($value, function ($item) {
                return is_string($item);
            })) == count($value);
    }

    public static function isNotNull(mixed $value): bool {
        return !is_null($value);
    }

    /**
     * Проверка значения по callback-функции или массиву callback-функций по логике "И".
     *
     * @param mixed $value
     * @param callable|ArrayBase $callback
     * @param bool $throw_exception
     * @return mixed
     * @throws stdException
     */
    public static function Validated(mixed $value, callable|ArrayBase $callback, bool $throw_exception = true): mixed {
        if (is_callable($callback)) {
            $callback = new ArrayBase([$callback]);
        }

        foreach ($callback as $callback_item) {
            if (!$callback_item($value)) {
                if ($throw_exception) {
                    throw new stdException(
                        'Значение не прошло проверку callback-функции',
                        [
                            'value' => $value,
                            'callback' => $callback
                        ]
                    );
                }

                return null;
            }
        }

        return $value;
    }
}
