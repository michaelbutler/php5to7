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

if (empty($argv[1])) {
    echo "Please pass the input path (file or directory) as the first argument.\n";
    exit(2);
}

$input_path = $argv[1];

$upgrader = new php5to7\Upgrader($input_path);
$upgrader->run();
