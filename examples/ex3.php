<?php
require '../vendor/autoload.php';

use \phputil\validator\Validator;
use \phputil\validator\Rule;
use \phputil\validator\Option;
use \phputil\validator\Format;

//
// Example 3
//
// Tip: We recommend that you see ex2.php before reading this file.
//

//
// Validating an object of stdClass (dynamic object)
//

$vd = new Validator();

$obj = new \stdClass;
$obj->foo = 'foo';

$rules = array( 'foo' => array( Rule::MAX_LENGTH => 2 ) );
$problems = $vd->checkObject( $obj, $rules );
	
var_dump( $problems ); // array( 'foo' => array( 'max_length' => '' ) )

//
// Validating a object of a class
//

class Foo {
	private $bar;
	
	function __construct( $bar ) {
		$this->bar = $bar;
	}
	
	function getBar() { return $this->bar; }
}

$foo = new Foo( 'hello' );
$rules = array( 'bar' => array( Rule::MAX_LENGTH => 2 ) );

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

// Now lets create a more complex object and rules

class Bar {
	private $x;
	protected $y;
	public $z;
	
	function __construct( $x, $y, $z ) { $this->x = $x; $this->y = $y; $this->z = $z; }
	function getX() { return $this->x; }
	function getY() { return $this->y; }
}

$bar = new Bar( 100, 200, array( 'j' => 'hello', 'k' => array( 'a', 'b', 'c' ) ) );
$foo = new Foo( $bar ); // Lets validate $foo

$rules = array(
	'bar' => array( Rule::WITH => array(
				'x' => array( Rule::MIN_VALUE => 101 ),
				'y' => array( Rule::MAX_VALUE => 199 ),
				'z' => array( Rule::WITH => array(
					'j' => array( Rule::MAX_LENGTH => 2 ),
					'k' => array( Rule::MAX_COUNT => 2 )
				) )
			)
		)
	);
	
$problems = $vd->checkObject( $foo, $rules );	
var_dump( $problems );
/* all attributes of $foo will have problems:
array(
	'bar' => array(
		'x' => array( 'min_value' => '' ),
		'y' => array( 'max_value' => '' ),
		'z' => array(
			'j' => array( 'max_length' => 'j must have at most 2 characters.' ),
			'k' => array( 'max_count' => '' )
			)
	)
)
*/
?>