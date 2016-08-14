<?php
namespace phputil\validator\tests;

require_once 'vendor/autoload.php';

use \PHPUnit_Framework_TestCase;

use \phputil\validator\ProblemsTransformer;

class ProblemsTransformerTest extends PHPUnit_Framework_TestCase {
	
	private $t;
	
	function setUp() {
		$this->t = new ProblemsTransformer();
	}

	function test_strip_rules_of_simple_problems() {
		$problems = array(
			array( 'min_value' => 'x min_value message' ),
			array( 'max_value' => 'y max_value message' ),
		);
		
		$r = $this->t->stripRules( $problems );
		$this->assertEquals( $r[ 0 ][ 0 ], 'x min_value message' );
		$this->assertEquals( $r[ 1 ][ 0 ], 'y max_value message' );
	}
	
	function test_strip_rules_of_complex_problems() {
		$problems = array(
			'bar' => array(
				'x' => array( 'min_value' => 'x min_value message' ),
				'y' => array( 'max_value' => 'y max_value message' ),
				'z' => array(
					'j' => array( 'max_length' => 'j max_length' ),
					'k' => array( 'max_count' => 'k max_count' )
					)
			)
		);
		
		$r = $this->t->stripRules( $problems );
		
		$this->assertTrue( is_array( $r[ 'bar' ][ 'x' ] ) );
		$this->assertTrue( isset( $r[ 'bar' ][ 'x' ][ 0 ] ) );
		$this->assertEquals( $r[ 'bar' ][ 'x' ][ 0 ], 'x min_value message' );
		$this->assertEquals( $r[ 'bar' ][ 'z' ][ 'k' ][ 0 ], 'k max_count' );
	}
	
	function test_messages_of_simple_problems() {
		$problems = array(
			array( 'min_value' => 'x min_value message' ),
			array( 'max_value' => 'y max_value message' ),
		);
		
		$r = $this->t->justTheMessages( $problems );
		
		$this->assertEquals( $r[ 0 ], 'x min_value message' );
		$this->assertEquals( $r[ 1 ], 'y max_value message' );		
	}
	
	function test_messages_of_complex_problems() {
		$problems = array(
			'bar' => array(
				'x' => array( 'min_value' => 'x min_value message' ),
				'y' => array( 'max_value' => 'y max_value message' ),
				'z' => array(
					'j' => array( 'max_length' => 'j max_length' ),
					'k' => array( 'max_count' => 'k max_count' )
					)
			)
		);
		
		$r = $this->t->justTheMessages( $problems );
		
		$this->assertEquals( $r[ 0 ], 'x min_value message' );
		$this->assertEquals( $r[ 3 ], 'k max_count' );
	}	
	
}

?>