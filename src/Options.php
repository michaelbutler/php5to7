<?php

namespace michaelbutler\php5to7;

class Options
{
    private $args;

    public $inputPath;
    public $backup = false;
    public $backupPrefix = '.bak';
    public $showHelp = false;

    public $overwrite = false;

    public function __construct(array $args)
    {
        $this->args = $args;
        $this->parseArgs();
    }

    private function parseArgs()
    {
        foreach ($this->args as $arg) {
            if ($arg !== $this->args[0] && strpos($arg, '-') !== 0) {
                $this->inputPath = $arg;
            }
            if ($arg === '--backup') {
                $this->backup = true;
            } elseif ($arg === '--overwrite') {
                $this->overwrite = true;
            } elseif ($arg === '--help' || $arg === '-h') {
                $this->showHelp = true;
            }
        }
    }
}
