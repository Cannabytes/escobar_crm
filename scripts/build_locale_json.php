<?php

require_once __DIR__ . '/list_translations.php';

$strings = collectTranslationStrings();

$ru = [];
$en = [];

foreach ($strings as $string) {
    $ru[$string] = $string;
    $en[$string] = $string;
}

$options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;

file_put_contents(__DIR__ . '/../lang/ru.json', json_encode($ru, $options) . PHP_EOL);
file_put_contents(__DIR__ . '/../lang/en.json', json_encode($en, $options) . PHP_EOL);


