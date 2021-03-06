<?php

namespace t35\Library;

use t35\Library\Arrays\ArrayBase;
use t35\Library\Exceptions\stdException;
use t35\Library\Strings\EStringFormat;
use t35\Library\Strings\StringBase;

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
     * @return \t35\Library\Strings\StringBase
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
     * @param ArrayBase $dirs Папки, в которых находятся библиотеки, подготовленные для Composer'а. Например, находящиеся в разработке, либо корневая папка проекта.
     * Если используется rglob, указанная папка может содержать множество вложенных библиотек.
     * @param bool $recursive Если "true" - будет использована функция rglob, иначе - glob.
     * @return void
     * @see SimpleLibrary::rglob()
     * @see glob()
     */
    public static function ComposerLoad(ArrayBase $dirs, bool $recursive = true): void {
        foreach ($dirs as $dir) {
            $files = ($recursive ? self::rglob($dir . "*/composer.json") : glob($dir . "*/composer.json"));
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

    /**
     * Рекурсивный glob.
     *
     * @param string $pattern
     * @param int $flags
     * @return ArrayBase
     */
    public static function rglob(string $pattern, int $flags = 0): ArrayBase {
        $pattern = preg_replace('/\[(.*?)]/', '[[]\1[]]', $pattern);

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
