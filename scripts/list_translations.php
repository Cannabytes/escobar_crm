<?php

if (! function_exists('collectTranslationStrings')) {
    /**
     * @param  array<int, string>  $paths
     * @return array<int, string>
     */
    function collectTranslationStrings(array $paths = ['resources', 'app']): array
    {
        $strings = [];

        foreach ($paths as $path) {
            if (! is_dir($path)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS)
            );

            /** @var SplFileInfo $file */
            foreach ($iterator as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $content = file_get_contents($file->getPathname());

                if ($content === false || $content === '') {
                    continue;
                }

                if (preg_match_all("/__\\('(.*?)'\\)/u", $content, $matches)) {
                    foreach ($matches[1] as $string) {
                        // Skip likely translation keys (contain only latin letters, numbers, dots, underscores, dashes or colons)
                        if (preg_match('/^[A-Za-z0-9_.:-]+$/', $string)) {
                            continue;
                        }

                        $strings[$string] = true;
                    }
                }
            }
        }

        $strings = array_keys($strings);
        sort($strings, SORT_STRING);

        return $strings;
    }
}

if (PHP_SAPI === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'])) {
    foreach (collectTranslationStrings() as $string) {
        echo $string, PHP_EOL;
    }
}

