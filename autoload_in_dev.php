<?php

$filename = __DIR__ . '/composer.json';
$composer_json = json_decode(file_get_contents($filename), true);
$composer_files = $composer_json['autoload']['files'] ?? false;
if ($composer_files && is_array($composer_files)) {
    foreach ($composer_files as $composer_file) {
        include_once dirname($filename) . DIRECTORY_SEPARATOR . $composer_file;
    }
}
