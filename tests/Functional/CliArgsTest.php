<?php
/**
 * This file is part of CliArgs.
 * git: https://github.com/cheprasov/php-cli-args
 *
 * (C) Alexander Cheprasov <cheprasov.84@ya.ru>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Test\Functional;

use CliArgs\CliArgs;

class CliArgsTest extends \PHPUnit_Framework_TestCase
{
    public function providerTestFilterHelp()
    {
        return [
            [
                [__FILE__, '--help'],
                ['help' => ['filter' => 'help']],
                "HELP:\n\n"
                ."    --help\n"
            ],
            [
                [__FILE__, '--help'],
                ['help' => ['filter' => 'help', 'alias' => 'h']],
                "HELP:\n\n"
                ."    --help -h\n"
            ],
            [
                [__FILE__, '--help'],
                [
                    'help' => ['filter' => 'help', 'alias' => 'h'],
                    'json' => ['filter' => 'json', 'alias' => 'j', 'help' => 'Example of json'],
                ],
                "HELP:\n\n"
                ."    --help -h\n\n"
                ."    --json -j\n"
                ."        Example of json\n"
            ],
            [
                [__FILE__, '-h'],
                [
                    'help' => ['filter' => 'help', 'alias' => 'h'],
                    'json' => ['filter' => 'json', 'alias' => 'j', 'help' => 'Example of json'],
                ],
                "HELP:\n\n"
                ."    --help -h\n\n"
                ."    --json -j\n"
                ."        Example of json\n"
            ],
            [
                [__FILE__, '-h', 'json'],
                [
                    'help' => ['filter' => 'help', 'alias' => 'h'],
                    'json' => ['filter' => 'json', 'alias' => 'j', 'help' => 'Example of json'],
                ],
                "HELP:\n\n"
                ."    --json -j\n"
                ."        Example of json\n"
            ],
            [
                [__FILE__, '-h', 'j'],
                [
                    'help' => ['filter' => 'help', 'alias' => 'h'],
                    'json' => ['filter' => 'json', 'alias' => 'j', 'help' => 'Example of json'],
                ],
                "HELP:\n\n"
                ."    --json -j\n"
                ."        Example of json\n"
            ],
            [
                [__FILE__, '--help', 'j'],
                [
                    'help' => ['filter' => 'help', 'alias' => 'h'],
                    'json' => ['filter' => 'json', 'alias' => 'j', 'help' => 'Example of json'],
                ],
                "HELP:\n\n"
                ."    --json -j\n"
                ."        Example of json\n"
            ],
            [
                [__FILE__, '--help', 'json'],
                [
                    'help' => ['filter' => 'help', 'alias' => 'h'],
                    'json' => ['filter' => 'json', 'alias' => 'j', 'help' => 'Example of json'],
                ],
                "HELP:\n\n"
                ."    --json -j\n"
                ."        Example of json\n"
            ],
            [
                [__FILE__, '--help', 'json'],
                [
                    'h' => ['filter' => 'help', 'alias' => 'help'],
                    'j' => ['filter' => 'json', 'alias' => 'json', 'help' => 'Example of json'],
                ],
                "HELP:\n\n"
                ."    -j --json\n"
                ."        Example of json\n"
            ],
        ];
    }

    /**
     * @see \CliArgs\CliArgs::getArg
     * @dataProvider providerTestFilterHelp
     * @param array $argv
     * @param array $config
     * @param string $expect
     */
    public function testFilterHelp($argv, $config, $expect)
    {
        $GLOBALS['argv'] = $argv;
        $CliArgs = new CliArgs($config);
        $this->assertEquals(true, $CliArgs->isFlagExists('help', 'h'));
        $this->assertEquals($expect, $CliArgs->getArg('help'));
    }

    public function providerTestFilters()
    {
        return [
            // JSON
            [
                [__FILE__, '--json'],
                ['json' => ['filter' => 'json']],
                'json',
                null
            ],
            [
                [__FILE__, '-j', '{"a":1,"b":2,"c":3}'],
                ['json' => ['filter' => 'json', 'alias' => 'j']],
                'j',
                ['a' => 1, 'b' => 2, 'c' => 3]
            ],
            [
                [__FILE__, '-j', '{"a":1,"b":2,"c":3}'],
                ['j' => ['filter' => 'json', 'alias' => 'json']],
                'j',
                ['a' => 1, 'b' => 2, 'c' => 3]
            ],
            // FLAG
            [
                [__FILE__, '-f'],
                ['f' => ['filter' => 'flag']],
                'f',
                true
            ],
            [
                [__FILE__, '-a'],
                ['f' => ['filter' => 'flag']],
                'f',
                null
            ],
            [
                [__FILE__, '-f'],
                ['f' => ['filter' => 'flag', 'alias' => 'flag']],
                'flag',
                true
            ],
            [
                [__FILE__, '--flag'],
                ['flag' => ['filter' => 'flag', 'alias' => 'f']],
                'f',
                true
            ],
            [
                [__FILE__, '--flag'],
                ['flag' => ['filter' => 'flag', 'alias' => 'f']],
                'f',
                true
            ],
            [
                [__FILE__, '--flag', 'foo'],
                ['flag' => ['filter' => 'flag', 'alias' => 'f']],
                'f',
                true
            ],
            // BOOL
            [
                [__FILE__, '-b'],
                ['b' => ['filter' => 'bool']],
                'b',
                null
            ],
            [
                [__FILE__, '-b'],
                ['b' => ['filter' => 'bool', 'default' => false]],
                'b',
                false
            ],
            [
                [__FILE__, '-b', 'false'],
                ['b' => ['filter' => 'bool']],
                'b',
                false
            ],
            [
                [__FILE__, '-b', 'NO'],
                ['b' => ['filter' => 'bool']],
                'b',
                false
            ],
            [
                [__FILE__, '-b', '0'],
                ['b' => ['filter' => 'bool']],
                'b',
                false
            ],
            [
                [__FILE__, '-b', '1'],
                ['b' => ['filter' => 'bool']],
                'b',
                true
            ],
            [
                [__FILE__, '-b', 'True'],
                ['b' => ['filter' => 'bool']],
                'b',
                true
            ],
            [
                [__FILE__, '-b', 'YES'],
                ['b' => ['filter' => 'bool']],
                'b',
                true
            ],
            [
                [__FILE__, '-b', 'YES'],
                ['b' => ['filter' => 'bool', 'alias' => 'bool']],
                'bool',
                true
            ],
            // FLOAT
            [
                [__FILE__, '-f'],
                ['f' => ['filter' => 'float']],
                'f',
                null
            ],
            [
                [__FILE__, '-f'],
                ['f' => ['filter' => 'float', 'default' => 0.0]],
                'f',
                0.0
            ],
            [
                [__FILE__, '-f', '123.45'],
                ['f' => ['filter' => 'float', 'default' => 0.0]],
                'f',
                123.45
            ],
            [
                [__FILE__, '--float', '123.45'],
                ['f' => ['filter' => 'float', 'default' => 0.0, 'alias' => 'float']],
                'f',
                123.45
            ],
            // INT
            [
                [__FILE__, '-i'],
                ['i' => ['filter' => 'int']],
                'i',
                null
            ],
            [
                [__FILE__, '-i'],
                ['i' => ['filter' => 'int', 'default' => 0]],
                'i',
                0
            ],
            [
                [__FILE__, '-i', '123'],
                ['i' => ['filter' => 'int', 'default' => 0]],
                'i',
                123
            ],
            [
                [__FILE__, '--foo', '123.45'],
                ['f' => ['filter' => 'int', 'default' => 0, 'alias' => 'foo']],
                'f',
                123
            ],
            [
                [__FILE__, '--foo', '123abc'],
                ['f' => ['filter' => 'int', 'default' => 0, 'alias' => 'foo']],
                'f',
                123
            ],
            // FUNCTION
            [
                [__FILE__, '-i'],
                ['i' => ['filter' => function($a) { return $a * 2;} ]],
                'i',
                null
            ],
            [
                [__FILE__, '-i', '5'],
                ['i' => ['filter' => function($a) { return $a * 2;} ]],
                'i',
                10
            ],
            [
                [__FILE__, '--func', '15'],
                ['i' => ['filter' => function($a) { return $a * 2;}, 'alias' => 'func']],
                'i',
                30
            ],
            [
                [__FILE__, '--name', 'alexander cheprasov'],
                [
                    'n' => [
                        'filter' => function($name) {
                            return mb_convert_case($name, MB_CASE_TITLE, 'UTF-8');},
                        'alias' => 'name'
                    ]
                ],
                'n',
                'Alexander Cheprasov'
            ],
            // ENUM
            [
                [__FILE__, '-e'],
                ['e' => ['filter' => [1,2,3]]],
                'e',
                null
            ],
            [
                [__FILE__, '-e'],
                ['e' => ['filter' => [1,2,3], 'default' => 0]],
                'e',
                0
            ],
            [
                [__FILE__, '-e', '1'],
                ['e' => ['filter' => [1,2,3], 'default' => 0]],
                'e',
                0
            ],
            [
                [__FILE__, '-e', '1'],
                ['e' => ['filter' => ['1','2','3']]],
                'e',
                '1'
            ],
            [
                [__FILE__, '-e', '2'],
                ['e' => ['filter' => ['1','2','3']]],
                'e',
                '2'
            ],
            [
                [__FILE__, '-e', '4'],
                ['e' => ['filter' => ['1','2','3']]],
                'e',
                null
            ],
            // WITHOUT FILTER
            [
                [__FILE__, '-e', '42'],
                ['e' => []],
                'e',
                '42'
            ],
            [
                [__FILE__, '--foo', '42'],
                ['foo' => []],
                'foo',
                '42'
            ],
            [
                [__FILE__, '--foo'],
                ['foo' => []],
                'foo',
                null
            ],
            [
                [__FILE__, '--foo'],
                ['foo' => []],
                'foo',
                null
            ],
        ];
    }

    /**
     * @see \CliArgs\CliArgs::getArg
     * @dataProvider providerTestFilters
     * @param array $argv
     * @param array $config
     * @param string|string[] $arg
     * @param mixed $expect
     */
    public function testFilters($argv, $config, $arg, $expect)
    {
        $GLOBALS['argv'] = $argv;
        $CliArgs = new CliArgs($config);
        $this->assertEquals($expect, $CliArgs->getArg($arg));
    }

    public function providerTestGetArguments()
    {
        return [
            [
                [__FILE__, '-e'],
                [__FILE__, 'e' => null]
            ],
            [
                [__FILE__, '-f', 'bar', '--foo', 'baz'],
                [__FILE__, 'f' => 'bar', 'foo' => 'baz']
            ],
            [
                [__FILE__, 'a', 'b', 'c'],
                [__FILE__, 'a', 'b', 'c']
            ],
            [
                [__FILE__, '-abc=e'],
                [__FILE__, 'a' => null, 'b' => null, 'c' => null, '=' => null, 'e' => null]
            ],
            [
                [__FILE__, '--abc'],
                [__FILE__, 'abc' => null]
            ],
            [
                [__FILE__, '--foo=bar'],
                [__FILE__, 'foo' => 'bar']
            ],
            [
                [__FILE__, '--foo=bar', 'baz', 'foo'],
                [__FILE__, 'foo' => 'bar', 'baz', 'foo']
            ],
        ];
    }

    /**
     * @see \CliArgs\CliArgs::getArguments
     * @dataProvider providerTestGetArguments
     * @param array $argv
     * @param array $arguments
     */
    public function testGetArguments($argv, $arguments)
    {
        $GLOBALS['argv'] = $argv;
        $CliArgs = new CliArgs();
        $this->assertEquals($arguments, $CliArgs->getArguments());
    }

    public function providerTestGetArg()
    {
        return [
            [
                [__FILE__, '--foo', 'bar'],
                null,
                'foo',
                null
            ],
            [
                [__FILE__, '--foo', 'bar'],
                ['foo' => []],
                'foo',
                'bar'
            ],
            [
                [__FILE__, '--foo', 'bar'],
                ['foo' => ['filter' => 'flag']],
                'foo',
                true
            ],
            [
                [__FILE__, '--user-id'],
                ['user-id' => ['filter' => 'int', 'default' => 0]],
                'user-id',
                0
            ],
            [
                [__FILE__, '--user-id', '42'],
                ['user-id' => ['filter' => 'int', 'default' => 0]],
                'user-id',
                42
            ],
            [
                [__FILE__, '--user-id=42'],
                ['user-id' => ['filter' => 'int', 'default' => 0]],
                'user-id',
                42
            ],
        ];
    }

    /**
     * @see \CliArgs\CliArgs::getArg
     * @dataProvider providerTestGetArg
     * @param array $argv
     * @param array|null $config
     * @param string $arg
     * @param mixed $expect
     */
    public function testGetArg($argv, $config, $arg, $expect)
    {
        $GLOBALS['argv'] = $argv;
        $CliArgs = new CliArgs($config);
        $this->assertEquals($expect, $CliArgs->getArg($arg));
    }

    public function providerTestGetArgs()
    {
        return [
            [
                [__FILE__, '--foo', 'bar'],
                null,
                []
            ],
            [
                [__FILE__, '--foo', 'bar'],
                ['foo' => []],
                ['foo' => 'bar']
            ],
            [
                [__FILE__, '--foo', 'bar'],
                ['foo' => ['filter' => 'flag']],
                ['foo' => true]
            ],
            [
                [__FILE__, '--user-id'],
                ['user-id' => ['filter' => 'int', 'default' => 0]],
                ['user-id' => 0]
            ],
            [
                [__FILE__, '--user-id', '42'],
                ['user-id' => ['filter' => 'int', 'default' => 0]],
                ['user-id' => 42]
            ],
            [
                [__FILE__, '--user-id=42'],
                ['user-id' => ['filter' => 'int', 'default' => 0]],
                ['user-id' => 42]
            ],
            [
                [__FILE__, '--user-id=32', '--sex=m', '--city=London', '--name=Alexander'],
                [
                    'user-id' => ['filter' => 'int', 'default' => 0],
                    'sex' => ['filter' => ['m', 'f']],
                    'city' => [],
                ],
                ['user-id' => 32, 'sex' => 'm', 'city' => 'London']
            ],
        ];
    }

    /**
     * @see \CliArgs\CliArgs::getArgs
     * @dataProvider providerTestGetArgs
     * @param array $argv
     * @param array|null $config
     * @param mixed $expect
     */
    public function testGetArgs($argv, $config, $expect)
    {
        $GLOBALS['argv'] = $argv;
        $CliArgs = new CliArgs($config);
        $this->assertEquals($expect, $CliArgs->getArgs());
    }

    public function providerTestIsFlagExists()
    {
        return [
            [
                [__FILE__],
                'foo', 'bar',
                false
            ],
            [
                [__FILE__, '--bar'],
                'foo', null,
                false
            ],
            [
                [__FILE__, 'foo'],
                'foo', null,
                false
            ],
            [
                [__FILE__, '--foo', 'bar'],
                'foo', null,
                true
            ],
            [
                [__FILE__, '--foo', 'bar'],
                'foo', 'f',
                true
            ],
            [
                [__FILE__, '-f'],
                'foo', 'f',
                true
            ],
            [
                [__FILE__, '-f', 'bar'],
                'f', null,
                true
            ],
        ];
    }

    /**
     * @see \CliArgs\CliArgs::isFlagExists
     * @dataProvider providerTestIsFlagExists
     * @param array $argv
     * @param string $arg
     * @param string|null $alias
     * @param bool $expect
     */
    public function testIsFlagExists($argv, $arg, $alias, $expect)
    {
        $GLOBALS['argv'] = $argv;
        $CliArgs = new CliArgs();
        $this->assertEquals($expect, $CliArgs->isFlagExists($arg, $alias));
    }
}
