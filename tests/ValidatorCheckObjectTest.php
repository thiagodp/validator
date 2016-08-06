<?php
namespace phputil\tests;

require_once 'vendor/autoload.php';

use PHPUnit_Framework_TestCase;

use phputil\Encoding;
use phputil\Format;
use phputil\Rule;
use phputil\Option;
use phputil\Validator;

/** Just a dummy class. */
class ADummy {
	
	private $foo;
	public $bar;
	
	function __construct( $foo, $bar ) {
		$this->foo = $foo;
		$this->bar = $bar;
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
	
	function test_non_stdclass_works_arrays() {
		$obj = new ADummy( 'foo', 'bar' );
		$obj->bar = '';
		$problems = $this->vd->checkObject(
			$obj,
			array(
				'foo' => array( Rule::MAX_LENGTH => 2 ),
				'bar' => array( Rule::REQUIRED => true )
			)
			);
		$this->assertNotFalse( isset( $problems[ 'foo' ][ Rule::MAX_LENGTH ] ) );
		$this->assertNotFalse( isset( $problems[ 'bar' ][ Rule::REQUIRED ] ) );
	}
	
}