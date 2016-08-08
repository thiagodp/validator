# phputil\validator

Easy and powerful validation library for PHP.

[![Build Status](https://travis-ci.org/thiagodp/validator.svg?branch=master)](https://travis-ci.org/thiagodp/validator)

We use [semantic versioning](http://semver.org/). See [our releases](https://github.com/thiagodp/validator/releases).

## Installation

```command
composer require phputil/validator
```

## Dependencies

Dependends only on [phputil\rtti](https://github.com/thiagodp/rtti).
(We use it to be able to retrieve private and protected values from non-`stdClass` objects)

## Features

- [x] Validate basic types
- [x] Validate arrays
- [x] Validate dynamic objects (`stdClass`)
- [x] Validate objects (of user-created classes) with private or protected attributes

### Available Rules

- [x] `required`
- [x] `min_length`
- [x] `max_length`
- [x] `length_range`
- [x] `min_value`
- [x] `max_value`
- [x] `value_range`
- [x] `regex`
- [x] `format`
- [x] custom: you can add others easily. Simple like this:

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
- [x] `name`
- [x] `word`
- [x] `alphanumeric`*
- [x] `alpha only`*
- [x] `ascii`*
- [x] `numeric`
- [x] `integer`
- [x] `price`
- [x] `tax`
- [x] `date_dmy`
- [x] `date_mdy`
- [x] `date_ymd`
- [x] `date_dmy_dotted`
- [x] `date_mdy_dotted`
- [x] `date_ymd_dotted`
- [x] `date_dmy_dashed`
- [x] `date_mdy_dashed`
- [x] `date_ymd_dashed`
- [x] `date`**
- [x] `time`**
- [x] `longtime`**
- [x] `datetime`**
- [x] `longdatetime`**
- [x] `email`
- [x] `http`
- [x] `url`
- [x] `ip`
- [x] `ipv4`
- [x] `ipv6`
- [x] custom: you can add others easily. See below.

_\* Not fully tested, but it should work._

_** Not fully tested, and it will change soon._

#### Adding a custom rule

```php
// Adding a format "myFormat" in which the value should start with "https://"
$validator->setFormat( 'myFormat', function( $value ) {
	return mb_strpos( $value, 'https://' ) === 0;
	} );
```

Now checking the rule:

```php
$value = 'http://non-https-site.com';
$rules = array( Rule::FORMAT => 'myFormat' ); // rules to be checked
$problems = $validator->check( $value, $rules ); // check() returns the hurt rules
echo isset( $problems[ Rule::FORMAT ] ) ? 'myFormat as hurt' : 'passed';
```

### Message Replacements

- [x] **any rule** (i.e.: `{min_length}`, `{max_value}`, etc.): shows the rule value.
- [x] `{min_value}` and `{max_value}` are also available when `{value_range}` is defined.
- [x] `{min_length}` and `{max_length}` are also available when `{length_range}` is defined.
- [x] `{label}` shows the defined replacement for array keys or object field names.
- [x] `{value}` shows the value.

### More

- [x] Supports UTF-8 and other common formats (ISO-8859-1, Windows-1251, ASCII, etc.)
- [x] Error messages and formats can be specified by locale.
- [x] Error messages and formats can be specified at once, and thus read them from a JSON file.
- [x] Formats and rules can be specified without having to extend any class.
- [x] Classes use a [fluent interface](https://en.wikipedia.org/wiki/Fluent_interface) (that is, you type less).
- [x] Can check a single value.
- [x] Can check a value array.
- [x] Can check an object.
- [ ] Builder classes available (soon)

## Tests

[See here](https://github.com/thiagodp/validator/tree/master/tests).

## Examples

[See all](https://github.com/thiagodp/validator/tree/master/examples)

[ex1.php](https://github.com/thiagodp/validator/tree/master/examples/ex1.php) - Validating values

[ex2.php](https://github.com/thiagodp/validator/tree/master/examples/ex2.php) - Validating arrays

[ex3.php](https://github.com/thiagodp/validator/tree/master/examples/ex3.php) - Validating objects
