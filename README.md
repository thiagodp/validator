# validator

Easy and powerful validation library for PHP.

[![Build Status](https://travis-ci.org/thiagodp/validator.svg?branch=master)](https://travis-ci.org/thiagodp/validator)

We use [semantic versioning](http://semver.org/). See [our releases](https://github.com/thiagodp/validator/releases).

## Installation

```command
composer require phputil/validator
```

## Dependencies

Dependends only on [phputil/rtti](https://github.com/thiagodp/rtti).
(We use it to be able to retrieve private and protected values from non-stdClass objects)

## Features

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
- [x] custom (you can add others easily)

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
- [x] custom (you can add others easily)

_\* Not fully tested, but it should work._

_** Not fully tested, and it will change soon._

### Message Replacements

- [x] **any rule** (i.e.: `{min_length}`, `{max_value}`, etc.): shows the rule value.
- [x] `{min_value}` and `{max_value}` can be used when `{value_range}` is defined.
- [x] `{min_length}` and `{max_length}` can be used when `{length_range}` is defined.
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
- [ ] Builder classes available.

## Tests

[See here](https://github.com/thiagodp/validator/tree/master/tests).

## Examples

The following examples are also available at the `examples` folder.

### Validating values

```php
$vd = new Validator();

$rules = array(
	Rule::FORMAT => Format::NUMERIC
	);
	
$age = 'hi'; // I know, this is a strange value

// Will return the validation message for each hurt rule
$problems = $vd->check( $age, $rules );	
// "Hi" hurt only the FORMAT rule
var_dump( $problems ); // array( 'format' => '' )

$vd->setMessage( Rule::FORMAT, 'It must be numeric!' );

$problems = $vd->check( $age, $rules );
var_dump( $problems ); // array( 'format' => 'It must be numeric!' )

// Lets improve the message to include a label...
$vd->setMessage( Rule::FORMAT, '{label} must be numeric!' );
// ...and lets give the label as a third argument
$problems = $vd->check( $age, $rules, 'Age' );
var_dump( $problems ); // array( 'format' => 'Age must be numeric!' )

// Not lets add another rule
$rules[ Rule::MIN_VALUE ] = 18;
$age = 17;
$problems = $vd->check( $age, $rules, 'Age' );
var_dump( $problems ); // array( 'min' => '' ) <<- No message for MIN_VALUE, remember?
// Now lets define a message
$vd->setMessage( Rule::MIN_VALUE, '{label} must be >= {min_value}.' );
$problems = $vd->check( $age, $rules, 'Age' );
var_dump( $problems ); // array( 'min' => 'Age must be >= 18.' )
```

### Validating arrays

```php
$vd = new Validator();

$values = array(
	'name' => 'Bob',
	'age' => 16,
	'sisterName' => 'Suzan'
	);
	
$rules = array(
	'name' => array( Rule::LENGTH_RANGE => array( 2, 60 ) ),
	'age' => array( Rule::MIN_VALUE => 18 ),
	'sisterName' => array( Rule::LENGTH_RANGE => array( 2, 60 ) )
	);

// checkObject will return the validation messages for each key
$problems = $vd->checkArray( $values, $rules );
var_dump( $problems );
// prints:
// array(
//	'name' => array(),
//	'age' => array( 'min_value' => '' )
//	'sisterName' => array(),
//	)

// Increases min length to 5, so now "Bob" is invalid
$rules[ 'name' ][ Rule::LENGTH_RANGE ] = array( 5, 60 );

// Sets the message for LENGTH_RANGE. Messages can in include special
// fields such as the name of validated field, the rule value and the
// evaluated value!
$vd->setMessage( Rule::LENGTH_RANGE, '{label} must have {length_range} characters.' );

$problems = $vd->checkArray( $values, $rules );
var_dump( $problems ); // "name must have 5-60 characters."

// Lets overwrite the message
$vd->setMessage( Rule::LENGTH_RANGE,
	'{label} must have from {min_length} to {max_length} characters.' );
	
$problems = $vd->checkArray( $values, $rules );
var_dump( $problems ); // "name must have from 5 to 60 characters."

// Now lets define a label for the field "name"
$rules[ 'name' ][ Option::LABEL ] = 'The Name';

$problems = $vd->checkArray( $values, $rules );
var_dump( $problems ); // "The Name must have from 5 to 60 characters."
```

### Validating objects:

```php
// Validating an object of stdClass
$obj = new \stdClass;
$obj->foo = 'foo';

$rules = array( 'foo' => array( Rule::MAX_LENGTH => 2 ) );

// checkObject works just like checkArray()
$problems = $vd->checkObject( $obj, $rules );
	
var_dump( $problems );

class Foo {
	private $bar;
	
	function __construct( $bar ) {
		$this->bar = $bar;
	}
	
	function getBar() { return $this->bar; }
}

$foo = new Foo( 'hello' );

$rules = array( 'bar' => array( Rule::MAX_LENGTH => 2 ) );

// same way
$problems = $vd->checkObject( $obj, $rules );

var_dump( $problems );
```