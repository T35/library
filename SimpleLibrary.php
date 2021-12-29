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
     * Загрузка библиотек по логике Composer'а.
     *
     * @param string|null $autoloadPath Для загрузки библиотек, скаченных Composer'ом.
     * @param ArrayBase|null $advancedDirs Папки, в которых находятся библиотеки, подготовленные для Composer'а. Например, находящиеся в разработке.
     * Причем указанная папка может содержать множество вложенных библиотек. Данные папки это папки для функции glob с шаблоном поиска файлов composer.json.
     * @return void
     */
    public static function ComposerLoad(string $autoloadPath = null, ArrayBase $advancedDirs = null): void {
        if ($autoloadPath !== null) {
            if (file_exists($autoloadPath)) {
                include_once($autoloadPath);
            }
        }

        if ($advancedDirs !== null) {
            foreach ($advancedDirs as $advancedDir) {
                $files = glob($advancedDir . "*/composer.json");
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
