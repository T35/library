<?php

namespace t35\Library;

class BaseClass {
    /**
     * Возвращает имя класса в виде класса StringBase.
     *
     * @see StringBase
     * @return StringBase
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

    public static function isConstructableByStructure(): bool {
        return ($interfaces = class_implements(static::class)) && in_array(IConstructableByStructure::class, $interfaces);
    }
}
