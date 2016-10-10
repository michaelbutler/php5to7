<?php

namespace Nonsense\FooBar;

/**
 * Class FooBar
 *
 *
 * Lots of text here...
 */
class FooBar
{
    const MY_INT = 777;

    public $bar;
    public $baz;

    /**
     * @param int $param1 This does some stuff.
     *
     * @param int $param2 This does some more stuff.
     * @return int
     */
    public function myFunction($param1, $param2)
    {
        echo $param1 * $param2;

        return $param1 * $param2;
    }

    /**
     * Random phpdoc block
     * @param bool $foo
     */

    /**
     * @param string $param1 This does some stuff.
     *
     * @param string $param2 This does some more stuff.
     *
     * @param array $param3 This does some more stuff.
     *
     * @return string
     */
    public function myFunction2($param1, $param2, array $param3 = [])
    {
        echo $param1 . $param2;

        return $param1 . $param2;
    }
}
