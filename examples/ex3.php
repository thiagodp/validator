<?php
namespace phputil;

require '../vendor/autoload.php';

// Example 3

$vd = new Validator();

// Validating an object of stdClass
$obj = new \stdClass;
$obj->foo = 'foo';

$rules = array( 'foo' => array( Rule::MAX_LENGTH => 2 ) );

// checkObject works just like checkArray()
$problems = $vd->checkObject( $obj, $rules );
	
var_dump( $problems ); // array( 'foo' => array( 'max_length' => '' ) )

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
$problems = $vd->checkObject( $foo, $rules );
var_dump( $problems ); // array( 'bar' => array( 'max_length' => '' ) )

$vd->setMessage( Rule::MAX_LENGTH, '{label} must have at most {max_length} characters.' );
$problems = $vd->checkObject( $foo, $rules );
var_dump( $problems );
// array( 'bar' => array( 'max_length' => 'bar must have at most 2 characters.' ) )

$rules[ 'bar' ][ Option::LABEL ] = 'My Bar';
$problems = $vd->checkObject( $foo, $rules );
var_dump( $problems );
// array( 'bar' => array( 'max_length' => 'My Bar must have at most 2 characters.' ) )