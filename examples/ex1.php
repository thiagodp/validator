<?php
namespace phputil;

require '../vendor/autoload.php';

// Example 1

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

?>