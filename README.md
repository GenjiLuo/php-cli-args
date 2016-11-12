[![MIT license](http://img.shields.io/badge/license-MIT-brightgreen.svg)](http://opensource.org/licenses/MIT)
# CliArgs v1.0.0 for PHP >= 5.5

##

WORK IN PROGRESS
CLASS IN DEVELOPMENT

## About
Easy way to gets options from the command line argument list.

## Note
This class is not workable when [register_argc_argv](http://php.net/manual/en/ini.core.php#ini.register-argc-argv) is disabled.

## Usage

### Config

Note: all params from cli that you want to use should be specified in config, otherwise they will be ignored.
```php
$config = [
    // You should specify key as name of option from the command line argument list.
    // Example, name <param-name> for --param-name option
    'param-name' => [

        'alias' => 'p',
            // [optional], [string]
            // Alias helps to have short or long name for this key.
            // Example, name <p> for -p option

        'default' => false,
            // [optional], [mixed], [default = null]
            // Default value will returned if param is not setted
            // or params has not value.

        'help' => 'Some description about param',
            // [optional], [string]
            // Text that will returned, if you request help

        'filter' => 'int',
            // [optional], [string | array | function]
            // Filter for the return value.
            // You can use next filters: flag, bool, int, float, help, json, <array>, <function>

            // 'int' - cast to integer before return.
            // 'float' - cast to float before return.
            // 'bool' - cast to float before return. Yes, true, 1 = TRUE, other = FALSE
            // 'json' - decode JSON data before return.
            // 'flag' - will return TRUE, if key is exists in command line argument list.
            // <array> - use array for enums. Example use ['a', 'b', 'c'] to get only one of these.
            // <function> - use function($value, $default) { ... } to process value by yourself
            // 'help' - use this filter if you want generate and return help about all config.
    ]
];

$CliArgs = new CliArgs($config);
```

Examples of config:

0
```php

// Simple configs

// The config1 and config2 are equal
$config1 = ['foo', 'bar', 'a'];
$config2 = [
    'foo' => [],
    'bar' => [],
    'a' => [],
];

// The config3 and config4 are equal
$config3 = ['foo' => 'f', 'bar' => 'b', 'a'];
$config4 = [
    'foo' => [
        'alias' => 'f',
    ],
    'bar' => [
        'alias' => 'b',
    ],
    'a' => [],
];
```

1
```php
$config = [
    'help' => [
        'alias' => 'h',
        'filter' => 'help',
        'help' => 'Show help about all options',
    ],
    'data' => [
        'alias' => 'd',
        'filter' => 'json',
        'help' => 'Some description about this param',
    ],
    'user-id' => [
        'alias' => 'u',
        'filter' => 'int',
        'help' => 'Some description about this param',
    ]
];
$CliArgs = new CliArgs($config);
```
```
Show help
> some-script.php --help
<?php if ($CliArgs->isFlagExists('help', 'h')) echo $CliArgs->getArg('help'); ?>

Show help only for param data
> some-script.php --help data
<?php if ($CliArgs->isFlagExists('help', 'h')) echo $CliArgs->getArg('help'); ?>

All the same:
> some-script.php --data='{"foo":"bar"}' --user-id=42
or
> some-script.php --data '{"foo":"bar"}' --user-id 42
or
> some-script.php -d '{"foo":"bar"}' --user-id 42
or
> some-script.php -d '{"foo":"bar"}' -u 42

<?php
    print_r($CliArgs->getArg('data'));
    print_r($CliArgs->getArg('d'));
    print_r($CliArgs->getArg('user-id'));
    print_r($CliArgs->getArg('u'));
```

2
```php
    $config = [
        'flag' => [
            'alias' => 'f',
            'filter' => 'flag',
        ],
        'id' => [
            'filter' => 'int',
        ],
        'any' => [],
    ];
```
```
> some-script.php --flag

> some-script.php -f

> some-script.php -f --id=42 --any="any value"

> some-script.php --any="any value"

<?php
    print_r($CliArgs->isFlagExists('flag', 'f'));
    print_r($CliArgs->getArg('data'));
    print_r($CliArgs->getArg('d'));
    print_r($CliArgs->getArg('user-id'));
    print_r($CliArgs->getArg('u'));

```

3
```php
    $config = [
        'name' => [
            'alias' => 'n',
            'filter' => function($name, $default) {
                return $name ? mb_convert_case($name, MB_CASE_TITLE, 'UTF-8') : $defult;
            },
            'default' => 'No name',
        ],
        'sex' => [
            'alias' => 's',
            'filter' => ['m', 'f'],
            'default' => null,
        ],
        'city' => [
            'alias' => 'c',
            'filter' => function($city) {
                // ... some checks of city
            },
        ],
        'email' => [
            'alias' => 'e',
            'filter' => function($city) {
                // ... some checks of email
            },
        ]
    ];
```
```
> some-script.php --name alexander

> some-script.php -f

> some-script.php -f --id=42 --any="any value"

> some-script.php --any="any value"

<?php
    print_r($CliArgs->getArg('name'));
    print_r($CliArgs->('d'));
    print_r($CliArgs->getArg('user-id'));
    print_r($CliArgs->getArg('u'));
```
### Create a new instance
```php
// simple config
$config = ['foo' => 'f', 'bar' => 'b'];
$CliArgs = new CliArgs($config);
```

### Methods

```
> example.php --foo Hello --bar World
```

###### new CliArgs([array|null])
Constructor.
```php
$config = ['foo' => 'f', 'bar' => 'b'];
$CliArgs = new CliArgs($config);
```

###### getArgs(): array
Get all params.
```php
$argv = $CliArgs->getArgs();
print_r($argv);
// array(
//    'foo' => 'Hello',
//    'bar' => 'World',
// )
```

###### getArg(string $arg): mixed
Get one param
```php
$arg = $CliArgs->getArg('foo');
// or $CliArgs->getArg('f');
echo $arg; // Hello
```

###### isFlagExists(string $arg, string|null $alias): bool
Checks if the given key exists in the arguments console list.
Returns true if $arg or $alias are exists
```php
echo $CliArgs->isFlagExists('f'); // false
echo $CliArgs->isFlagExists('foo'); // true
echo $CliArgs->isFlagExists('foo', 'f'); // true
```

###### getArguments(): array
Get prepared ARGV
```php
print_r($CliArgs->getArguments());
// array(
//     0 => 'example.php'
//    'foo' => 'Hello',
//    'bar' => 'World',
// )
```

## Installation

### Composer

Download composer:

    wget -nc http://getcomposer.org/composer.phar

and add dependency to your project:

    php composer.phar require cheprasov/php-cli-args

## Running tests

    ./vendor/bin/phpunit


## Something doesn't work

Feel free to fork project, fix bugs and finally request for pull
