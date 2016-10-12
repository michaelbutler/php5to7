<?php

namespace michaelbutler\php5to7;

class Upgrader
{
    /** @var string */
    private $inputPath;
    private $backup;

    /** @var Options */
    private $originalOptions;

    /** @var int */
    private $skipped = 0;

    /** @var int */
    private $written = 0;

    public function __construct(Options $options)
    {
        $this->originalOptions = $options;
        $this->inputPath = $options->inputPath;
        $this->backup = $options->backup;
    }

    public function run()
    {
        $fileBucket = $this->getDirectoryIterator();
        /** @var \SplFileInfo $file */
        foreach ($fileBucket as $file) {
            if (!$file->getExtension() !== 'php') {
                ++$this->skipped;
                continue;
            }
            $string = $this->processOneFile($file);
            if (!$this->originalOptions->overwrite) {
                echo $string;
                // Echo a separator
                echo "\n-=-=-=-=-\n";
                continue;
            }
            file_put_contents($file->getRealPath(), $string);
            ++$this->written;
        }
    }

    /**
     * Validates the input path and sets up directory iterator to traverse.
     *
     * @throws UpgraderException
     * @return \Traversable
     */
    private function getDirectoryIterator()
    {
        if (!is_readable($this->inputPath)) {
            throw new UpgraderException("The input path $this->inputPath is not readable.");
        }
        if (!is_file($this->inputPath) && !is_dir($this->inputPath)) {
            throw new UpgraderException("The input path $this->inputPath is not a file nor directory.");
        }
        if (is_dir($this->inputPath)) {
            return FileProvider::fromDir($this->inputPath);
        }
        return FileProvider::fromSingle($this->inputPath);
    }

    /**
     * Inject type hints into the code by modifying the $tokens.
     *
     * @param array $tokens Tokens array to operate on. Will be modified by reference.
     * @param int $index The current token position. Will be token directly after the PHPdoc block.
     * @param MethodSignature $methodSignature Gives information about the method signature following the doc block.
     * @return int The index to continue on (after the method signature.
     */
    private function fixUpMethodSignature(&$tokens, $index, MethodSignature $methodSignature)
    {
        $size = $methodSignature->getSize();
        $map = $methodSignature->getVariableTypeMap();
        do {
            $size--;
            $index++;

            if ($size <= 0 || !isset($tokens[$index])) {
                break;
            }

            $token = $tokens[$index];

            if (is_string($token)
                || $token[0] !== T_VARIABLE
                || $this->isAlreadyScalarTypeHint($tokens[$index - 2]) // this one is already typehinted
                || !isset($map[$token[1]]) // We don't have information about this parameter
            ) {
                continue;
            }

            $typeHint = $map[$token[1]]['type'];

            if (!$this->isScalarTypeHint($typeHint)) {
                continue;
            }

            $tokens[$index][1] = $typeHint . ' ' . $tokens[$index][1];
        } while (true);

        return $index;
    }

    private function isScalarTypeHint($hint)
    {
        return in_array($hint, [
            'int',
            'bool',
            'float',
            'string',
        ], true);
    }

    private function isAlreadyScalarTypeHint($token)
    {
        $typehint = $token;
        if (!is_string($token)) {
            $typehint = $token[1];
        }

        return in_array($typehint, [
            'int',
            'bool',
            'float',
            'string',
            'array',
            'callable',
        ], true);
    }

    /**
     * Get the PHP source code back from token list.
     * @param array[] $tokens
     * @return string
     */
    private function rebuildSourceCode($tokens)
    {
        $buffer = '';
        foreach ($tokens as $token) {
            if (is_string($token)) {
                $buffer .= $token;
                continue;
            }
            $buffer .= $token[1];
        }
        return $buffer;
    }

    /**
     * @param \SplFileInfo $file
     * @return string
     * @throws UpgraderException
     */
    private function processOneFile(\SplFileInfo $file)
    {
        $contents = file_get_contents($file->getRealPath());
        $tokens = token_get_all($contents);
        if (empty($tokens)) {
            throw new UpgraderException('Could not parse the input file using token_get_all.');
        }

        $methodSignature = null;

        $index = 0;
        while (isset($tokens[$index])) {
            $token = $tokens[$index];
            $index++;
            if ($token[0] === T_DOC_COMMENT) {
                $methodSignature = MethodSignature::createFromTokens($tokens, $index - 1);
                if (!$methodSignature) {
                    continue;
                }
                $index = $this->fixUpMethodSignature($tokens, $index, $methodSignature);
            }
        }

        return $this->rebuildSourceCode($tokens);
    }

    /**
     * @return bool
     */
    public function isBackupEnabled()
    {
        return (bool) $this->backup;
    }
}
