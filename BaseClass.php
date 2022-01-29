<?php

namespace t35\Library;

use t35\Library\Strings\StringBase;

class BaseClass {
    /**
     * Возвращает имя класса в виде класса StringBase.
     *
     * @return \t35\Library\Strings\StringBase
     *@see \t35\Library\Strings\StringBase
     */
    public static function class(): StringBase {
        return new StringBase(static::class);
    }

    public static function isThatClass(string $className): bool {
        return $className == static::class;
    }

    public static function isThatSubclass(string $className): bool {
        return is_subclass_of($className, static::class);
    }
}
