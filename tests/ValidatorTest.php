<?php
namespace phputil\tests;

require_once 'vendor/autoload.php';

use PHPUnit_Framework_TestCase;

use phputil\Encoding;
use phputil\Format;
use phputil\Rule;
use phputil\Option;
use phputil\Validator;

/**
 *  A dummy class.
 */
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
 * Tests Validator.
 *
 * @author	Thiago Delgado Pinto
 */
class ValidatorTest extends PHPUnit_Framework_TestCase {
	
	private $vd = null;
	
	function setUp() {
		$this->vd = new Validator();
	}
	
	function test_check_returns_problems_with_each_rule() {
		$problems = $this->vd->check( '', array( Rule::REQUIRED => true ) );
		$this->assertTrue( isset( $problems[ Rule::REQUIRED ] ) );
	}
	
	function test_check_returns_empty_message_for_rule_with_undefined_message() {
		$problems = $this->vd->check( '', array( Rule::REQUIRED => true ) );
		$this->assertEquals( '', $problems[ Rule::REQUIRED ] );
	}
	
	function test_check_returns_the_rule_defined_message() {
		$message = 'Required!';
		$this->vd->addMessage( Rule::REQUIRED, $message );
		$problems = $this->vd->check( '', array( Rule::REQUIRED => true ) );
		$this->assertEquals( $message, $problems[ Rule::REQUIRED ] );
	}

	function test_check_returns_a_message_for_each_failed_rule() {
		$requiredMsg = '{label} is required.';
		$minLengthMsg = 'Minimum length for {label} is {min_length}.';
		$invalidFormatMsg = 'Invalid format for {label}.';
		$this->vd->addMessage( Rule::REQUIRED, $requiredMsg );
		$this->vd->addMessage( Rule::MIN_LENGTH, $minLengthMsg );
		$this->vd->addMessage( Rule::FORMAT, $invalidFormatMsg );
		$problems = $this->vd->check( '', array(
			Rule::REQUIRED => true,
			Rule::MIN_LENGTH => 2,
			Rule::FORMAT => Format::NUMERIC
			) );
		$this->assertTrue( isset( $problems[ Rule::REQUIRED ] ) );
		$this->assertTrue( isset( $problems[ Rule::MIN_LENGTH ] ) );
		$this->assertFalse( isset( $problems[ Rule::FORMAT ] ) ); // false! Numeric does not check empty string
	}
	
	function test_check_returns_a_labeled_message_for_a_failed_rule() {
		$requiredMsg = '{label} is required.';
		$this->vd->addMessage( Rule::REQUIRED, $requiredMsg );
		$problems = $this->vd->check( '', array(
			Rule::REQUIRED => true,
			Option::LABEL => 'dummy'
			) );
		$this->assertTrue( isset( $problems[ Rule::REQUIRED ] ) );
		$this->assertEquals( 0, mb_strpos( $problems[ Rule::REQUIRED ], 'dummy', 0, $this->vd->encoding() ) );
	}
	
	function test_check_array_returns_a_message_for_each_failed_rule_for_each_field() {
		$requiredMsg = '{label} is required.';
		$minLengthMsg = 'Minimum length for {label} is {min_length}.';
		$invalidFormatMsg = 'Invalid format for {label}.';
		$this->vd->addMessage( Rule::REQUIRED, $requiredMsg );
		$this->vd->addMessage( Rule::MIN_LENGTH, $minLengthMsg );
		$this->vd->addMessage( Rule::FORMAT, $invalidFormatMsg );
		
		$field1 = 'dummy1';
		$field2 = 'dummy2';
		
		$problems = $this->vd->checkArray(
			array(
				$field1 => '',
				$field2 => '12345'
			),
		
			array(
				$field1 => array(
					Rule::REQUIRED => true,
					Rule::MIN_LENGTH => 2
					),
				$field2 => array(
					Rule::MAX_LENGTH => 3
					)
				)
			);
			
		$this->assertTrue( isset( $problems[ $field1 ] ) );
		$this->assertTrue( isset( $problems[ $field1 ][ Rule::REQUIRED ] ) );
		$this->assertTrue( isset( $problems[ $field1 ][ Rule::MIN_LENGTH ] ) );
		
		$this->assertTrue( isset( $problems[ $field2 ] ) );
		$this->assertTrue( isset( $problems[ $field2 ][ Rule::MAX_LENGTH ] ) );
	}
	
	
	function test_check_array_returns_a_labeled_message_for_each_failed_rule_of_each_field() {

		$minLengthMsg = 'Minimum length for {label} is {min_length}.';
		$maxLengthMsg = 'Maximum length for {label} is {max_length}.';
		$this->vd->addMessage( Rule::MIN_LENGTH, $minLengthMsg );
		$this->vd->addMessage( Rule::MAX_LENGTH, $maxLengthMsg );
		
		$field1 = 'dummy1';
		$field2 = 'dummy2';
		$label1 = 'Dummy 1';
		$label2 = 'Dummy 2';
		
		$problems = $this->vd->checkArray(
			array(
				$field1 => '1',
				$field2 => '12345'
			),
		
			array(
				$field1 => array(
					Rule::MIN_LENGTH => 2,
					Option::LABEL => $label1
					),
				$field2 => array(
					Rule::MAX_LENGTH => 3,
					Option::LABEL => $label2
					)
			)
		);
		
		$this->assertNotFalse( mb_strpos( $problems[ $field1 ][ Rule::MIN_LENGTH ], $label1, 0, $this->vd->encoding() ) );		
		$this->assertNotFalse( mb_strpos( $problems[ $field2 ][ Rule::MAX_LENGTH ], $label2, 0, $this->vd->encoding() ) );		
	}
	
	
	function test_check_object_of_stdclass_works_like_for_arrays() {
		$obj = new \stdClass;
		$obj->foo = 'foo';
		$problems = $this->vd->checkObject(
			$obj,
			array( 'foo' => array( Rule::MAX_LENGTH => 2 ) )
			);
		$this->assertNotFalse( isset( $problems[ 'foo' ][ Rule::MAX_LENGTH ] ) );
	}
	
	function test_check_object_of_some_class_works_like_for_arrays() {
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

?>