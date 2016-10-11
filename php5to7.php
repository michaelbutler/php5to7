#!/usr/bin/env php
<?php

use michaelbutler\php5to7;

$autoloads = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../../../autoload.php',
    __DIR__ . '/../vendor/autoload.php',
];
foreach ($autoloads as $file) {
    if (file_exists($file)) {
        require $file;
        break;
    }
}

ini_set('xdebug.max_nesting_level', 3000);

// Disable XDebug var_dump() output truncation
ini_set('xdebug.var_display_max_children', -1);
ini_set('xdebug.var_display_max_data', -1);
ini_set('xdebug.var_display_max_depth', -1);

$helpMsg = <<<HELP
Usage:
php php5to7.php [OPTIONS] <file/directory>

--overwrite     Instead of outputting the changed source, writes
                the php7 changes into the file.
--backup        For every file it modifies, copies the original
                to filename.php.bak.
-h  --help      This help message.

<file/directory> The path to the individual file, or a directory
                 to process every php file recursively.

https://github.com/michaelbutler/php5to7
Licensed under the GPLv3 (http://www.gnu.org/licenses/gpl.html)
HELP;

$options = new php5to7\Options($argv);

if ($options->showHelp) {
    echo $helpMsg;
    return;
}

if (empty($options->inputPath)) {
    echo "Please pass the input path (file or directory) as an argument.\n";
    echo $helpMsg;
    return;
}

$upgrader = new php5to7\Upgrader($options);
$upgrader->run();
