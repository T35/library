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
     * @param mixed $var
     * @param EStringFormat $format
     * @return StringBase
     * @throws stdException
     */
    public static function GetVarDump(mixed $var, EStringFormat $format = EStringFormat::HTML): StringBase {
        ob_start();
        var_dump($var);
        $content = new StringBase(ob_get_contents());
        ob_end_clean();

        if ($format == EStringFormat::HTML) {
            $content->Prefix(new StringBase('<pre>'))->Postfix(new StringBase('</pre>'));
        }

        return $content;
    }

    /**
     * Загрузка библиотек по логике Composer'а.
     *
     * @param ArrayBase|null $advancedDirs Папки, в которых находятся библиотеки, подготовленные для Composer'а. Например, находящиеся в разработке.
     * Причем указанная папка может содержать множество вложенных библиотек. Данные папки это папки для функции rglob с шаблоном поиска файлов composer.json.
     * @see SimpleLibrary::rglob()
     * @return void
     */
    public static function ComposerLoad(ArrayBase $advancedDirs = null): void {
        if ($advancedDirs !== null) {
            foreach ($advancedDirs as $advancedDir) {
                $files = self::rglob($advancedDir . "*/composer.json");
                foreach ($files as $filename) {
                    $composer_json = json_decode(file_get_contents($filename), true);
                    $composer_files = $composer_json['autoload']['files'] ?? false;
                    if ($composer_files && is_array($composer_files)) {
                        foreach ($composer_files as $composer_file) {
                            include_once dirname($filename) . DIRECTORY_SEPARATOR . $composer_file;
                        }
                    }
                }
            }
        }
    }

    /**
     * Определяет, можно ли считать массив ассоциативным.
     *
     * @param array|ArrayBase $arr
     * @return bool
     */
    #[Pure] public static function is_assoc(array|ArrayAccess $arr): bool {
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

    /**
     * Рекурсивный glob.
     *
     * @param string $pattern
     * @param int $flags
     * @return ArrayBase
     */
    public static function rglob(string $pattern, int $flags = 0): ArrayBase {
        $result = new ArrayBase();

        $result->putAll(glob($pattern, $flags), false);

        $paths = glob(dirname($pattern) . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR | GLOB_NOSORT);
        $pattern_part = str_replace(dirname($pattern), '', $pattern);
//        echo $pattern_part, '<br>';
        foreach ($paths as $path) {
//            echo $path, '<br>';
            $result->putAll(self::rglob($path . $pattern_part, $flags), false);
        }

        return $result;
    }
}
