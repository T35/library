<?php

namespace t35\Library;

use JetBrains\PhpStorm\Pure;
use t35\Library\Arrays\ArrayBase;
use t35\Library\Arrays\ListCallables;
use t35\Library\Callback;
use t35\Library\Exceptions\stdException;

class ValidatingMethods {
    /**
     * Константы VM_(Validating Method) это callback-функции для проверки значения.
     * @see ArrayBase::getValid(), SimpleLibrary::Validated()
     * В качестве callback-функций также можно передать объекты:
     * @see Callback\CallbackValueInList, Callback\CallbackRegExp
     */
    public const VM_SYSTEM_NAME = [ValidatingMethods::class, 'isSystemName'];
    public const VM_NOT_EMPTY_ARRAY = [ValidatingMethods::class, 'isNotEmptyArray'];
    public const VM_IS_STRING_LIST = [ValidatingMethods::class, 'isStringList'];
    public const VM_IS_STRING_LIST_OR_EMPTY_ARRAY = [ValidatingMethods::class, 'isStringListOrEmptyArray'];
    public const VM_IS_NOT_NULL = [ValidatingMethods::class, 'isNotNull'];
    public const VM_CLASS_EXISTS = [ValidatingMethods::class, 'isClassExists'];
    public const VM_IS_CALLABLE = [ValidatingMethods::class, 'isCallable'];
    public const VM_IS_CALLABLE_LIST = [ValidatingMethods::class, 'isCallableList'];
    public const VM_IS_ASSOC = [ValidatingMethods::class, 'isAssoc'];
    public const VM_IS_ASSOC_OR_EMPTY_ARRAY = [ValidatingMethods::class, 'isAssocOrEmptyArray'];
    public const VM_IS_ARRAY = [ValidatingMethods::class, 'isArray'];

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
            && count($value)
            && count(array_filter($value, function ($item) {
                return is_string($item);
            })) == count($value);
    }

    public static function isStringListOrEmptyArray(mixed $value): bool {
        return
            self::isStringList($value)
            || (is_array($value) && !count($value));
    }

    public static function isNotNull(mixed $value): bool {
        return !is_null($value);
    }

    public static function isClassExists(mixed $value): bool {
        return is_string($value) && class_exists($value);
    }

    public static function isCallable(mixed $value): bool {
        return is_callable($value);
    }

    public static function isCallableList(mixed $value): bool {
        if ($value instanceof ListCallables)
            return true;

        if (!is_array($value))
            return false;

        foreach ($value as $item) {
            if (!is_callable($item))
                return false;
        }

        return true;
    }

    #[Pure] public static function isAssoc(mixed $value): bool {
        return ArrayBase::array_is_assoc($value);
    }

    #[Pure] public static function isAssocOrEmptyArray(mixed $value): bool {
        return
            self::isAssoc($value)
            || (is_array($value) && !count($value));
    }

    public static function isArray(mixed $value): bool {
        return is_array($value);
    }

    /**
     * Проверка значения по callback-функции или массиву callback-функций по логике "И".
     *
     * @param mixed $value
     * @param ListCallables $callbackList
     * @param FailedValue $failedValue
     * @return mixed
     * @throws Exceptions\FailedValue
     */
    public static function Validated(mixed $value, ListCallables $callbackList, FailedValue $failedValue = new FailedValue(null)): mixed {
        foreach ($callbackList as $callback) {
            if (!$callback($value)) {
                return $failedValue->Get(new \t35\Library\Exceptions\FailedValue(
                    'Значение не прошло проверку callback-функции',
                    [
                        'value' => $value,
                        'callback' => $callbackList
                    ]
                ));
            }
        }

        return $value;
    }
}
