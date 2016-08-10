<?php
require '../vendor/autoload.php';

use \phputil\Validator;
use \phputil\Rule;
use \phputil\Option;
use \phputil\Format;

//
// Example 2
//
// Tip: we recommend you seeing ex1.php before reading this file.
//

$vd = new Validator();

$rules = array(
	'name' => array( Rule::LENGTH_RANGE => array( 2, 60 ) ),
	'age' => array( Rule::MIN_VALUE => 18 ),
	'sisterName' => array( Rule::LENGTH_RANGE => array( 2, 60 ) )
	);
	
$values = array(
	'name' => 'Bob',		// valid
	'age' => 16,			// invalid
	'sisterName' => 'Suzan'	// valid
	);	

$problems = $vd->checkArray( $values, $rules );
var_dump( $problems ); // array( 'age' => array( 'min_value' => '' ) )

// Lets increase min length to 5, so now the value "Bob" is invalid
$rules[ 'name' ][ Rule::LENGTH_RANGE ] = array( 5, 60 );
// and configure a message for LENGTH_RANGE
$vd->setMessage( Rule::LENGTH_RANGE, '{label} must have {length_range} characters.' );

$problems = $vd->checkArray( $values, $rules );
var_dump( $problems );
// array(
//  'name' => array( 'length_range' => 'name must have 5-60 characters.' )
//	'age' => array( 'min_value' => '' )
// )


// Lets overwrite the message
$vd->setMessage( Rule::LENGTH_RANGE,
	'{label} must have from {min_length} to {max_length} characters.' );
	
$problems = $vd->checkArray( $values, $rules );
var_dump( $problems );
// array(
//  'name' => array( 'length_range' => 'name must have from 5 to 60 characters.' )
//	'age' => array( 'min_value' => '' )
// )


// Now lets define a label for the field "name"
$rules[ 'name' ][ Option::LABEL ] = 'The Name';

$problems = $vd->checkArray( $values, $rules );
var_dump( $problems );
// array(
//  'name' => array( 'length_range' => 'The Name must have from 5 to 60 characters.' )
//	'age' => array( 'min_value' => '' )
// )
?>