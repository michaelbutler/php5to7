<?php

namespace michaelbutler\php5to7;

class MethodSignature
{
    public $tokens;

    public $variableTypeMap;

    public function __construct($tokens)
    {
        $this->tokens = $tokens;
    }

    public function getSize()
    {
        return count($this->tokens);
    }

    /**
     * @param array[] $tokens
     * @param int $index
     * @return MethodSignature
     */
    public static function createFromTokens($tokens, $index)
    {
        $relatedTokens = [];

        if (!isset($tokens[$index])) {
            return null;
        }

        $token = $tokens[$index];
        $index++;

        if ($token[0] !== T_DOC_COMMENT) {
            return null;
        }

        $relatedTokens[] = $token;

        while (true) {
            $token = $tokens[$index];
            $index++;

            if (is_string($token) && $token === '{') {
                // We have all the tokens we need after hitting the {
                break;
            }

            // A doc comment right after a doc comment should be ignored.
            // We don't do anything for class doc blocks
            if ($token[0] === T_DOC_COMMENT || $token[0] === T_CLASS) {
                return null;
            }

            if (is_string($token)) {
                $token = [
                    0 => 0,
                    1 => $token,
                    2 => 0,
                    'index' => $index - 1,
                ];
            } else {
                $token['index'] = $index - 1;
            }
            $relatedTokens[] = $token;
        }

        return new self($relatedTokens);
    }

    /**
     * @return array
     */
    public function getVariableTypeMap()
    {
        if (!empty($this->variableTypeMap)) {
            return $this->variableTypeMap;
        }

        $this->variableTypeMap = [];

        $comment = $this->tokens[0][1];

        $matches = [];

        preg_match_all('~@param ([^ ]+) (\$[A-Za-z0-9_]+)~', $comment, $matches);

        list(, $types, $variables) = $matches;

        $infoArray = [];
        foreach ($variables as $index => $value) {
            $infoArray[$value] = [
                'name' => $value,
                'type' => $types[$index],
            ];
        }

        $this->variableTypeMap = $infoArray;

        return $this->variableTypeMap;
    }
}
