<?php
namespace phputil\validator\tests;

require_once 'vendor/autoload.php';

use \PHPUnit_Framework_TestCase;

use \phputil\validator\Encoding;
use \phputil\validator\Format;
use \phputil\validator\Rule;
use \phputil\validator\Option;
use \phputil\validator\Validator;

/**
 * Tests only the check() method of the validator.
 *
 * @author	Thiago Delgado Pinto
 */
class ValidatorCheckTest extends PHPUnit_Framework_TestCase {
	
	private $vd = null;
	
	function setUp() {
		$this->vd = new Validator();
	}
	
	function test_returns_hurt_rules() {
		$problems = $this->vd->check( '', array( Rule::REQUIRED => true ) );
		$this->assertTrue( isset( $problems[ Rule::REQUIRED ] ) );
	}
	
	function test_does_not_return_rules_not_hurt() {
		$problems = $this->vd->check( 'a', array( Rule::REQUIRED => true ) );
		$this->assertFalse( isset( $problems[ Rule::REQUIRED ] ) );
	}	
	
	function test_returns_an_empty_message_for_a_hurt_rule() {
		$problems = $this->vd->check( '', array( Rule::REQUIRED => true ) );
		$this->assertEquals( '', $problems[ Rule::REQUIRED ] );
	}
	
	function test_returns_the_defined_message_for_a_hurt_rule() {
		$message = 'Required!';
		$this->vd->setMessage( Rule::REQUIRED, $message );
		$problems = $this->vd->check( '', array( Rule::REQUIRED => true ) );
		$this->assertEquals( $message, $problems[ Rule::REQUIRED ] );
	}
	
	function test_replaces_the_label_even_when_label_is_not_set() {
		$requiredMsg = '{label} is required.';
		$this->vd->setMessage( Rule::REQUIRED, $requiredMsg );
		$problems = $this->vd->check( '', array( Rule::REQUIRED => true ), 'name' );
		$msg = $problems[ Rule::REQUIRED ];
		$this->assertFalse( mb_strpos( $msg, '{label}' ) );
	}

	function test_returns_a_message_for_each_hurt_rule() {
		$requiredMsg = '{label} is required.';
		$minLengthMsg = 'Minimum length for {label} is {min_length}.';
		$invalidFormatMsg = 'Invalid format for {label}.';
		$this->vd->setMessage( Rule::REQUIRED, $requiredMsg );
		$this->vd->setMessage( Rule::MIN_LENGTH, $minLengthMsg );
		$this->vd->setMessage( Rule::FORMAT, $invalidFormatMsg );
		$problems = $this->vd->check( '', array(
			Rule::REQUIRED => true,
			Rule::MIN_LENGTH => 2,
			Rule::FORMAT => Format::NUMERIC
			) );
		$this->assertTrue( isset( $problems[ Rule::REQUIRED ] ) );
		$this->assertTrue( isset( $problems[ Rule::MIN_LENGTH ] ) );
		$this->assertFalse( isset( $problems[ Rule::FORMAT ] ) ); // false! Numeric does not check empty string
	}
	
	function test_returns_a_labeled_message_for_a_hurt_rule() {
		$requiredMsg = '{label} is required.';
		$this->vd->setMessage( Rule::REQUIRED, $requiredMsg );
		$problems = $this->vd->check( '', array(
			Rule::REQUIRED => true,
			Option::LABEL => 'dummy'
			) );
		$this->assertTrue( isset( $problems[ Rule::REQUIRED ] ) );
		$this->assertEquals( 0, mb_strpos( $problems[ Rule::REQUIRED ], 'dummy', 0, $this->vd->encoding() ) );
	}
	
	function test_validates_with_user_defined_rule_function() {
		$this->vd->setRule( 'aRule', function( $x ) { return 0 == $x; } );
		$problems = $this->vd->check( 1, array( 'aRule' => true ) );
		$this->assertTrue( isset( $problems[ 'aRule' ] ) );
	}
	
	function test_validates_with_user_defined_format_function() {
		$this->vd->setFormat( 'aFormat', function( $value ) {
				return mb_strpos( $value, 'https://' ) === 0;
				} );
		$problems = $this->vd->check( 'https://', array( Rule::FORMAT => 'aFormat' ) );
		$this->assertFalse( isset( $problems[ Rule::FORMAT ] ) );
		$problems = $this->vd->check( 'http://', array( Rule::FORMAT => 'aFormat' ) );
		$this->assertTrue( isset( $problems[ Rule::FORMAT ] ) );
	}
	
	function test_validates_required() {
		$rules = array(
			Rule::REQUIRED => true
			);
		$problems = $this->vd->check( '', $rules );
		$this->assertTrue( isset( $problems[ Rule::REQUIRED ] ) );
	}
	
	function test_validates_min_length() {
		$rules = array(
			Rule::MIN_LENGTH => 1
			);
		$problems = $this->vd->check( '', $rules );
		$this->assertTrue( isset( $problems[ Rule::MIN_LENGTH ] ) );
	}
	
	function test_validates_max_length() {
		$rules = array(
			Rule::MAX_LENGTH => 1
			);
		$problems = $this->vd->check( 'ab', $rules );
		$this->assertTrue( isset( $problems[ Rule::MAX_LENGTH ] ) );
	}
	
	function test_validates_length_range() {
		$rules = array(
			Rule::LENGTH_RANGE => array( 1, 2 )
			);
		$problems = $this->vd->check( '', $rules );
		$this->assertTrue( isset( $problems[ Rule::LENGTH_RANGE ] ) );
		$problems = $this->vd->check( 'a', $rules );
		$this->assertFalse( isset( $problems[ Rule::LENGTH_RANGE ] ) );
		$problems = $this->vd->check( 'ab', $rules );
		$this->assertFalse( isset( $problems[ Rule::LENGTH_RANGE ] ) );
		$problems = $this->vd->check( 'abc', $rules );
		$this->assertTrue( isset( $problems[ Rule::LENGTH_RANGE ] ) );
	}
	
	function test_validates_min_value() {
		$rules = array(
			Rule::MIN_VALUE => 100
			);
		$problems = $this->vd->check( 99, $rules );
		$this->assertTrue( isset( $problems[ Rule::MIN_VALUE ] ) );
	}
	
	function test_validates_max_value() {
		$rules = array(
			Rule::MAX_VALUE => 100
			);
		$problems = $this->vd->check( 101, $rules );
		$this->assertTrue( isset( $problems[ Rule::MAX_VALUE ] ) );
	}
	
	function test_validates_value_range() {
		$rules = array(
			Rule::VALUE_RANGE => array( 100, 200 )
			);
		$problems = $this->vd->check( 99, $rules );
		$this->assertTrue( isset( $problems[ Rule::VALUE_RANGE ] ) );
		
		$problems = $this->vd->check( 100, $rules );
		$this->assertFalse( isset( $problems[ Rule::VALUE_RANGE ] ) );
		
		$problems = $this->vd->check( 200, $rules );
		$this->assertFalse( isset( $problems[ Rule::VALUE_RANGE ] ) );
		
		$problems = $this->vd->check( 201, $rules );
		$this->assertTrue( isset( $problems[ Rule::VALUE_RANGE ] ) );
	}
	
	function test_validates_min_count() {
		$rules = array(
			Rule::MIN_COUNT => 1
			);
		$problems = $this->vd->check( array(), $rules );
		$this->assertTrue( isset( $problems[ Rule::MIN_COUNT ] ) );
		
		$problems = $this->vd->check( array( 'a' ), $rules );
		$this->assertFalse( isset( $problems[ Rule::MIN_COUNT ] ) );
	}
	
	function test_validates_max_count() {
		$rules = array(
			Rule::MAX_COUNT => 2
			);
		$problems = $this->vd->check( array( 1, 2, 3 ), $rules );
		$this->assertTrue( isset( $problems[ Rule::MAX_COUNT ] ) );
		
		$problems = $this->vd->check( array( 1, 2 ), $rules );
		$this->assertFalse( isset( $problems[ Rule::MAX_COUNT ] ) );
	}
	
	function test_validates_count_range() {
		$rules = array(
			Rule::COUNT_RANGE => array( 1, 2 )
			);
		$problems = $this->vd->check( array(), $rules );
		$this->assertTrue( isset( $problems[ Rule::COUNT_RANGE ] ) );
		
		$problems = $this->vd->check( array( 1 ), $rules );
		$this->assertFalse( isset( $problems[ Rule::COUNT_RANGE ] ) );
		
		$problems = $this->vd->check( array( 1, 2 ), $rules );
		$this->assertFalse( isset( $problems[ Rule::COUNT_RANGE ] ) );
		
		$problems = $this->vd->check( array( 1, 2, 3 ), $rules );
		$this->assertTrue( isset( $problems[ Rule::COUNT_RANGE ] ) );
	}	
	
	function test_validates_in() {
		$rules = array(
			Rule::IN => array( 'a', 'c' )
			);
		$problems = $this->vd->check( 'b' , $rules );
		$this->assertTrue( isset( $problems[ Rule::IN ] ) );
		
		$problems = $this->vd->check( 'a', $rules );
		$this->assertFalse( isset( $problems[ Rule::IN ] ) );
	}
	
	function test_validates_not_in() {
		$rules = array(
			Rule::NOT_IN => array( 'a', 'c' )
			);
		$problems = $this->vd->check( 'b' , $rules );
		$this->assertFalse( isset( $problems[ Rule::NOT_IN ] ) );
		
		$problems = $this->vd->check( 'a', $rules );
		$this->assertTrue( isset( $problems[ Rule::NOT_IN ] ) );
	}
	
	function test_validates_start_with() {
		$rules = array(
			Rule::START_WITH => array( 'a', 'c' )
			);
		$problems = $this->vd->check( 'b' , $rules );
		$this->assertTrue( isset( $problems[ Rule::START_WITH ] ) );
		
		$problems = $this->vd->check( 'allow', $rules );
		$this->assertFalse( isset( $problems[ Rule::START_WITH ] ) );
		
		$problems = $this->vd->check( 'can', $rules );
		$this->assertFalse( isset( $problems[ Rule::START_WITH ] ) );		
	}
	
	function test_validates_not_start_with() {
		$rules = array(
			Rule::NOT_START_WITH => array( 'a', 'c' )
			);
		$problems = $this->vd->check( 'b' , $rules );
		$this->assertFalse( isset( $problems[ Rule::NOT_START_WITH ] ) );
		
		$problems = $this->vd->check( 'allow', $rules );
		$this->assertTrue( isset( $problems[ Rule::NOT_START_WITH ] ) );
		
		$problems = $this->vd->check( 'can', $rules );
		$this->assertTrue( isset( $problems[ Rule::NOT_START_WITH ] ) );		
	}	
	
	function test_validates_end_with() {
		$rules = array(
			Rule::END_WITH => array( 'a', 'l' )
			);
		$problems = $this->vd->check( 'b' , $rules );
		$this->assertTrue( isset( $problems[ Rule::END_WITH ] ) );
		
		$problems = $this->vd->check( 'gotcha', $rules );
		$this->assertFalse( isset( $problems[ Rule::END_WITH ] ) );
		
		$problems = $this->vd->check( 'camel', $rules );
		$this->assertFalse( isset( $problems[ Rule::END_WITH ] ) );		
	}	
	
	function test_validates_not_end_with() {
		$rules = array(
			Rule::NOT_END_WITH => array( 'a', 'l' )
			);
		$problems = $this->vd->check( 'b' , $rules );
		$this->assertFalse( isset( $problems[ Rule::NOT_END_WITH ] ) );
		
		$problems = $this->vd->check( 'gotcha', $rules );
		$this->assertTrue( isset( $problems[ Rule::NOT_END_WITH ] ) );
		
		$problems = $this->vd->check( 'camel', $rules );
		$this->assertTrue( isset( $problems[ Rule::NOT_END_WITH ] ) );		
	}
	
	function test_validates_contains() {
		$rules = array(
			Rule::CONTAINS => array( 'a', 'l' )
			);
		$problems = $this->vd->check( 'b' , $rules );
		$this->assertTrue( isset( $problems[ Rule::CONTAINS ] ) );
		
		$problems = $this->vd->check( 'gotcha', $rules );
		$this->assertFalse( isset( $problems[ Rule::CONTAINS ] ) );
		
		$problems = $this->vd->check( 'camel', $rules );
		$this->assertFalse( isset( $problems[ Rule::CONTAINS ] ) );		
	}	
	
	function test_validates_multiple_rules() {
		$rules = array(
			Rule::FORMAT => Format::EMAIL,
			Rule::LENGTH_RANGE => array( 36, 60 ),
			Rule::NOT_END_WITH => array( 'site.com', 'site.com.br' ),
			);
			
		$problems = $this->vd->check( 'bob#site.com', $rules );
		$this->assertFalse( empty( $problems ) );
		$this->assertTrue( isset( $problems[ Rule::FORMAT ] ) );
		$this->assertTrue( isset( $problems[ Rule::LENGTH_RANGE ] ) );
		$this->assertTrue( isset( $problems[ Rule::NOT_END_WITH ] ) );
	}
	
}