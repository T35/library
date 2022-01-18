<?php

namespace t35\Library;

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
     * @param ListCallables $callbackList
     * @param FailedValue $failedValue
     * @return mixed
     * @throws Exceptions\FailedValue
     */
    public static function Validated(mixed $value, ListCallables $callbackList, FailedValue $failedValue = new FailedValue(null)): mixed {
        foreach ($callbackList as $callback) {
            if (!$callback($value)) {
                return $failedValue->Get(new stdException(
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
