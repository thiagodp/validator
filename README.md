# phputil\validator

Easy and powerful validation library for PHP.

[![Build Status](https://travis-ci.org/thiagodp/validator.svg?branch=master)](https://travis-ci.org/thiagodp/validator)

We use [semantic versioning](http://semver.org/). See [our releases](https://github.com/thiagodp/validator/releases).

## Installation

```command
composer require phputil/validator
```
Dependends only on [phputil/rtti](https://github.com/thiagodp/rtti).
We use it to be able to retrieve private and protected values from non-`stdClass` objects.

## Features

- [x] Validate basic types (see [example 1](https://github.com/thiagodp/validator/tree/master/examples/ex1.php))
- [x] Validate arrays (see [example 2](https://github.com/thiagodp/validator/tree/master/examples/ex3.php))
- [x] Validate dynamic objects (`stdClass`) (see [example 3](https://github.com/thiagodp/validator/tree/master/examples/ex3.php))
- [x] Validate objects (of user-created classes) with private or protected attributes (see  [example 3](https://github.com/thiagodp/validator/tree/master/examples/ex3.php))

### Available Rules

- [x] `required`
- [x] `min_length`
- [x] `max_length`
- [x] `length_range`
- [x] `min_value`
- [x] `max_value`
- [x] `value_range`
- [x] `min_count` (for arrays)
- [x] `max_count` (for arrays)
- [x] `count_range` (for arrays)
- [x] `regex`
- [x] `format`: allows to use a format (see [Available Formats](#available-formats))
- [x] `with`: allows to define rules for sub-arrays or sub-objects.
- [x] **custom**: you can add others easily. See below.

#### Adding a custom rule

```php
// Adding a custom rule called "myRule" in which the value should be zero:
$validator->setRule( 'myRule', function( $value ) { return 0 == $value; } );
```
Now checking the custom rule:
```php
$value = rand( 0, 5 ); // Value to be checked, a random between 0 and 5 (inclusive)
$rules = array( 'myRule' => true ); // Rules to be checked. In this example, just "myRule".
$problems = $validator->check( $value, $rules ); // check() will return the hurt rules
echo isset( $problems[ 'myRule' ] ) ? 'myRule as hurt' : 'passed';
```

### Available Formats

- [x] `anything`
- [x] `string` (same as `anything`)
- [x] `name`
- [x] `word`
- [x] `alphanumeric`
- [x] `alpha`
- [x] `ascii`
- [x] `numeric`
- [x] `integer`
- [x] `double`
- [x] `float` (same as `double`)
- [x] `monetary`
- [x] `price` (same as `monetary`)
- [x] `tax`
- [x] `date` (equals to `date_dmy`)
- [x] `date_dmy`
- [x] `date_mdy`
- [x] `date_ymd`
- [x] `time`
- [x] `longtime`
- [x] `datetime` (equals to `datetime_dmy`)
- [x] `datetime_dmy`
- [x] `datetime_mdy`
- [x] `datetime_ymd`
- [x] `longdatetime` (equals to `longdatetime_dmy`)
- [x] `longdatetime_dmy`
- [x] `longdatetime_mdy`
- [x] `longdatetime_ymd`
- [x] `email`
- [x] `http`
- [x] `url`
- [x] `ip`
- [x] `ipv4`
- [x] `ipv6`
- [x] **custom**: you can add others easily. See below.

You may specify the separator for date-based formats. Default is "/", for example "31/12/1999".

#### Adding a custom format

```php
// Adding a format "myFormat" in which the value should start with "https://"
$validator->setFormat( 'myFormat', function( $value ) {
	return mb_strpos( $value, 'https://' ) === 0;
	} );
```

Now checking the format:

```php
$value = 'http://non-https-site.com';
$rules = array( Rule::FORMAT => 'myFormat' ); // rules to be checked
$problems = $validator->check( $value, $rules ); // check() returns the hurt rules
echo isset( $problems[ Rule::FORMAT ] ) ? 'myFormat as hurt' : 'passed';
```

### Message Replacements

- [x] `{min_length}` shows the minimum length;
- [x] `{max_length}` shows the maximum length;
- [x] `{length_range}` shows the minimum and the maximum length;
- [x] `{min_value}` shows the minimum value;
- [x] `{max_value}` shows the maximum value;
- [x] `{value_range}` shows minimum and maximum values;
- [x] `{min_count}` shows the minimum count;
- [x] `{max_count}` shows the maximum count;
- [x] `{count_range}` shows the minimum count and the maximum count;
- [x] `{regex}` shows the defined regex;
- [x] `{label}` shows the defined label, if defined. Otherwise, shows the array key or object attribute name;
- [x] `{value}` shows the value.

Notes:

- [x] `{min_value}` and `{max_value}` are available when the `{value_range}` is used;
- [x] `{min_length}` and `{max_length}` are available when the `{length_range}` is used;
- [x] `{min_count}` and `{max_count}` are available when the `{count_range}` is used.

### More

- [x] Supports UTF-8 and other common formats (ISO-8859-1, Windows-1251, ASCII, etc.)
- [x] Error messages and formats can be specified by locale.
- [x] Error messages and formats can be specified at once. This allows you, for example, read them from a JSON file.
- [x] Formats and rules can be specified without having to extend any class.
- [x] Classes use a [fluent interface](https://en.wikipedia.org/wiki/Fluent_interface) (that is, you type less).
- [ ] Builder classes available (soon)

## Tests

[See here](https://github.com/thiagodp/validator/tree/master/tests).

## Examples

[See all](https://github.com/thiagodp/validator/tree/master/examples)

[ex1.php](https://github.com/thiagodp/validator/tree/master/examples/ex1.php) - Validating values

[ex2.php](https://github.com/thiagodp/validator/tree/master/examples/ex2.php) - Validating arrays

[ex3.php](https://github.com/thiagodp/validator/tree/master/examples/ex3.php) - Validating objects
