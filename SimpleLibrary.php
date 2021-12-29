<?php
namespace t35\Library;

use ArrayAccess;
use JetBrains\PhpStorm\Pure;

class SimpleLibrary {
    public const REG_EXP_SYSTEM_NAME = '/^[a-zA-Z][a-zA-Z0-9_]+$/';

    public static function PreVarDump(mixed $var, bool $echo = true): string {
        ob_start();
        var_dump($var);
        $content = '<pre>' . ob_get_contents() . '</pre>';
        ob_end_clean();

        if ($echo)
            echo $content;

        return $content;
    }

    /**
     * Определяет, можно ли считать массив ассоциативным.
     *
     * @param array|ArrayBase $arr
     * @return bool
     */
    #[Pure]public static function is_assoc(array|ArrayAccess $arr): bool {
        if ($arr instanceof ArrayBase) {
            if (\count($arr) == 0) return false;
            return array_keys($arr) !== range(0, \count($arr) - 1);
        }

        if ($arr instanceof ArrayAccess) {
            if (count($arr) == 0) return false;
            return array_keys($arr) !== range(0, count($arr) - 1);
        }

        if ($arr === array()) return false;
        return \array_keys($arr) !== range(0, \count($arr) - 1);
    }
}
