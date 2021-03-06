# phputil\validator

Easy and powerful validation library for PHP.

[![Build Status](https://travis-ci.org/thiagodp/validator.svg?branch=master)](https://travis-ci.org/thiagodp/validator)

We use [semantic versioning](http://semver.org/). See [our releases](https://github.com/thiagodp/validator/releases).

## Installation

```command
composer require phputil/validator
```
Dependends only on [phputil/rtti](https://github.com/thiagodp/rtti).

## An Example

A step-by-step example for demonstrating its use.
```php
// Suppose that your application receives an object in a JSON like this:
$json = <<<EXAMPLE
{
	"name": "Bob Developer",
	"likes": 150,
	"phone": { "number": "99988-7766", "notes": "WhatsApp, Telegram" },
	"friends": [ "Suzan", "Mike", "Jane" ]
}
EXAMPLE;
// So you transform the JSON into an object
$obj = json_decode( $json );
// Now you want to validate this object, and you create a Validator
$validator = new Validator();
// And define the rules
$rules = array(
	// name must have from 2 to 60 characters
	'name' => array( Rule::LENGTH_RANGE => array( 2, 60 ), Option::LABEL => 'Name' ),
	// likes must be greater or equal to zero
	'likes' => array( Rule::MIN_VAUE => 0 ),
	// for the phone...
	'phone' => array( Rule::WITH => array(
		// number must follow a regex
		'number' => array(
			Rule::REGEX => '/^[0-9]{5}\\-[0-9]{4}$/',
			Option::LABEL => 'Phone Number'
			)
	) ),
	// have a friend limit
	'friends' => array( Rule::MAX_COUNT => 100 )
);
// And define the messages (we also could load it from a JSON file)
$messages = array(
	'en' => array( // "en" means "english" locale. This is the default locale.
		Rule::LENGTH_RANGE => '{label} must have from {min_length} to {max_length} characters.',
		Rule::MIN_VAUE => '{label} must be greater than or equal to {min_value}.',
		Rule::REGEX => '{label} has an invalid format.',
		Rule::MAX_COUNT => '{label} must have up to {max_count} item(s).',
	)
);
$validator->setMessages( $messages );

// Now we will check the object using our rules
$problems = $validator->checkObject( $obj, $rules );
// In this moment, problems will be an empty array because all values passed.
// That is: $problems === array()

// However, lets make our rules harder, just to understand how the validation works
$rules[ 'name' ][ Rule::LENGTH_RANGE ] = array( 2, 5 ); // Max of 5
$rule[ 'friends' ][ Rule::MAX_COUNT ] = 1; // just one friend (the best one :-)

// And check again
$problems = $validator->checkObject( $obj, $rules );
// Now $problems is an array like this:
// array(
//	'name' => array( 'length_range' => 'Name must have from 2 to 5 characters.' ),
//	'friends' => array( 'max_count' => 'friends must have up to 1 item(s)' )
// )
// Which means that we have two fields with problems. The format is:
//  field => hurt rule => message
// For example, the field "name" hurt the "length_range" rule and its message is
// "Name must have from 2 to 5 characters.".
//
// If we need to know whether "name" has a problem, we just check with isset:
if ( isset( $problems[ 'name' ] ) ) {
	echo 'Name has a problem', PHP_EOL;
}
// If we are only interested in the messages, and don't care about the fields,
// we just use the ProblemsTransformer
$messages = ( new ProblemsTransformer() )->justTheMessages( $problems );
var_dump( $messages );
// Will print something like:
// array( 'Name must have from 2 to 5 characters.', 'friends must have up to 1 item(s)' )
//
// That's it for now. Enjoy it!
```

## Features

- [x] Validates basic types (see [example 1](https://github.com/thiagodp/validator/tree/master/examples/ex1.php))
- [x] Validates arrays (see [example 2](https://github.com/thiagodp/validator/tree/master/examples/ex3.php))
- [x] Validates dynamic objects (`stdClass`) (see [example 3](https://github.com/thiagodp/validator/tree/master/examples/ex3.php))
- [x] Validates objects (of user-created classes) with private or protected attributes (see  [example 3](https://github.com/thiagodp/validator/tree/master/examples/ex3.php))
- [x] Supports localized validation messages (different locales)
- [x] Supports different string formats (UTF, ISO-8859-1, ASCII, etc.)

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
- [x] `in` (for arrays)
- [x] `not_in` (for arrays)
- [x] `start_with` (accepts a string or an array of strings, compared with "or")
- [x] `not_start_with` (accepts a string or an array of strings, compared with "or")
- [x] `end_with` (accepts a string or an array of strings, compared with "or")
- [x] `not_end_with` (accepts a string or an array of strings, compared with "or")
- [x] `contains` (accepts a string or an array of strings, compared with "or")
- [x] `not_contains` (accepts a string or an array of strings, compared with "or")
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
- [x] `{length_range}` shows the minimum and the maximum length (e.g. "5-10");
- [x] `{min_value}` shows the minimum value;
- [x] `{max_value}` shows the maximum value;
- [x] `{value_range}` shows minimum and maximum values (e.g. "5-10");
- [x] `{min_count}` shows the minimum count;
- [x] `{max_count}` shows the maximum count;
- [x] `{count_range}` shows the minimum count and the maximum count (e.g. "5-10");
- [x] `{in}` shows the set of items separated by comma;
- [x] `{not_in}` shows the set of items separated by comma;
- [x] `{start_with}` shows the string or the set of strings separated by comma;
- [x] `{not_start_with}` shows the string or the set of strings separated by comma;
- [x] `{end_with}` shows the string or the set of strings separated by comma;
- [x] `{not_end_with}` shows the string or the set of strings separated by comma;
- [x] `{contains}` shows the string or the set of strings separated by comma;
- [x] `{not_contains}` shows the string or the set of strings separated by comma;
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
