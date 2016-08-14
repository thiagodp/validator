<?php
namespace phputil\validator\tests;

require_once 'vendor/autoload.php';

use \PHPUnit_Framework_TestCase;

use \phputil\validator\Encoding;
use \phputil\validator\Format;
use \phputil\validator\Rule;
use \phputil\validator\Option;
use \phputil\validator\Validator;

class Foo {
	private $x;
	private $y;
	public function getX() { return $this->x; }
	public function setX( $x ) { $this->x = $x; }
	public function getY() { return $this->y; }
	public function setY( $y ) { $this->y = $y; }	
}

class Bar {
	
	public $a;
	protected $b;
	private $foo;
	
	function __construct( $a, $b, $foo = null ) {
		$this->a = $a;
		$this->b = $b;
		$this->foo = $foo;
	}
	
	function getB() {
		return $this->b;
	}	
	
	function getFoo() {
		return $this->foo;
	}
}

/**
 * Tests only the checkObject() method of the validator.
 *
 * @author	Thiago Delgado Pinto
 */
class ValidatorCheckObjectTest extends PHPUnit_Framework_TestCase {
	
	private $vd = null;
	
	function setUp() {
		$this->vd = new Validator();
	}

	function test_stdclass_works_like_arrays() {
		$obj = new \stdClass;
		$obj->foo = 'foo';
		$problems = $this->vd->checkObject(
			$obj,
			array( 'foo' => array( Rule::MAX_LENGTH => 2 ) )
			);
		$this->assertNotFalse( isset( $problems[ 'foo' ][ Rule::MAX_LENGTH ] ) );
	}
	
	function test_non_stdclass_works_like_arrays() {
		$obj = new Bar( 'aaa', '' );
		$problems = $this->vd->checkObject(
			$obj,
			array(
				'a' => array( Rule::MAX_LENGTH => 2 ),
				'b' => array( Rule::REQUIRED => true )
			)
			);			
		$this->assertNotFalse( isset( $problems[ 'a' ][ Rule::MAX_LENGTH ] ) );
		$this->assertNotFalse( isset( $problems[ 'b' ][ Rule::REQUIRED ] ) );
	}
	
	function test_validates_complex_stdclass_objects_using_with() {
		
		$obj = new \stdClass();
		$obj->foo = 0;
		$obj->bar = new \stdClass();
		$obj->bar->val = 10;
		$obj->bar->boo = new \stdClass();
		$obj->bar->boo->voo = 20;
		$obj->bar->boo->xoo = 20; // valid
		$obj->bar->boo->zoo = array( 'a' );
		
		$rules = array(
			'foo' => array( Rule::VALUE_RANGE => array( 5, 10 )  ),
			'bar' => array(
				Rule::WITH => array(
					'val' => array( Rule::MAX_VALUE => 9 ),
					'boo' => array(
						Rule::WITH => array(
							'voo' => array( Rule::MIN_VALUE => 21 ),
							'xoo' => array( Rule::MIN_VALUE => 20 ),
							'zoo' => array( Rule::MIN_COUNT => 2 ),
							)
						),
					)
				)
			);
		$problems = $this->vd->checkObject( $obj, $rules );
		
		$this->assertTrue( isset( $problems[ 'foo' ] ) );
		$this->assertTrue( isset( $problems[ 'bar' ] ) );
		$this->assertTrue( isset( $problems[ 'bar' ][ 'val' ] ) );
		$this->assertTrue( isset( $problems[ 'bar' ][ 'boo' ] ) );
		$this->assertTrue( isset( $problems[ 'bar' ][ 'boo' ][ 'voo' ] ) );
		$this->assertFalse( isset( $problems[ 'bar' ][ 'boo' ][ 'xoo' ] ) );
		$this->assertTrue( isset( $problems[ 'bar' ][ 'boo' ][ 'zoo' ] ) );
	}	
	
	
	function test_validates_complex_non_stdclass_objects_using_with() {
		$foo = new Foo();
		$foo->setX( 100 );
		$foo->setY( 200 ); // valid
		
		$obj = new Bar( array( 'a', 'b' ), 49, $foo );
		
		$rules = array(
			'a' => array( Rule::MAX_COUNT => 1 ),
			'b' => array( Rule::MIN_VALUE => 50 ),
			'foo' => array( Rule::WITH =>
					array(
						'x' => array( Rule::MIN_VALUE => 101 ),
						'y' => array( Rule::MIN_VALUE => 200 )
					)
				)
			);
		
		$problems = $this->vd->checkObject( $obj, $rules );
		
		$this->assertTrue( isset( $problems[ 'a' ] ) );
		$this->assertTrue( isset( $problems[ 'b' ] ) );
		$this->assertTrue( isset( $problems[ 'foo' ] ) );
		$this->assertTrue( isset( $problems[ 'foo' ][ 'x' ] ) );
		$this->assertFalse( isset( $problems[ 'foo' ][ 'y' ] ), true );
	}
	
}