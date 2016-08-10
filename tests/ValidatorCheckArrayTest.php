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
 * Tests only the checkArray() method of the validator.
 *
 * @author	Thiago Delgado Pinto
 */
class ValidatorCheckArrayTest extends PHPUnit_Framework_TestCase {
	
	private $vd = null;
	
	function setUp() {
		$this->vd = new Validator();
	}
	
	function test_returns_a_message_for_each_hurt_rule_of_each_field() {
		
		$requiredMsg = '{label} is required.';
		$minLengthMsg = 'Minimum length for {label} is {min_length}.';
		$invalidFormatMsg = 'Invalid format for {label}.';
		$this->vd->setMessage( Rule::REQUIRED, $requiredMsg );
		$this->vd->setMessage( Rule::MIN_LENGTH, $minLengthMsg );
		$this->vd->setMessage( Rule::FORMAT, $invalidFormatMsg );
		
		$field1 = 'dummy1';
		$field2 = 'dummy2';
		
		$rules = array(
			$field1 => array(
				Rule::REQUIRED => true,
				Rule::MIN_LENGTH => 2
				),
			$field2 => array(
				Rule::MAX_LENGTH => 3
				)
			);		
		
		$values = array(
			$field1 => '',
			$field2 => '12345'
			);
		
		$problems = $this->vd->checkArray( $values, $rules );
			
		$this->assertTrue( isset( $problems[ $field1 ] ) );
		$this->assertTrue( isset( $problems[ $field1 ][ Rule::REQUIRED ] ) );
		$this->assertTrue( isset( $problems[ $field1 ][ Rule::MIN_LENGTH ] ) );
		
		$this->assertTrue( isset( $problems[ $field2 ] ) );
		$this->assertTrue( isset( $problems[ $field2 ][ Rule::MAX_LENGTH ] ) );
	}
	
	
	function test_returns_a_labeled_message_for_each_hurt_rule_of_each_field() {

		$minLengthMsg = 'Minimum length for {label} is {min_length}.';
		$maxLengthMsg = 'Maximum length for {label} is {max_length}.';
		$this->vd->setMessage( Rule::MIN_LENGTH, $minLengthMsg );
		$this->vd->setMessage( Rule::MAX_LENGTH, $maxLengthMsg );
		
		$field1 = 'dummy1';
		$field2 = 'dummy2';
		$label1 = 'Dummy 1';
		$label2 = 'Dummy 2';
			
		$rules = array(
			$field1 => array(
				Rule::MIN_LENGTH => 2,
				Option::LABEL => $label1
				),
			$field2 => array(
				Rule::MAX_LENGTH => 3,
				Option::LABEL => $label2
				)
			);
			
		$values = array(
			$field1 => '1',
			$field2 => '12345'
			);			
		
		$problems = $this->vd->checkArray( $values, $rules );
		$this->assertNotFalse( mb_strpos( $problems[ $field1 ][ Rule::MIN_LENGTH ], $label1, 0, $this->vd->encoding() ) );		
		$this->assertNotFalse( mb_strpos( $problems[ $field2 ][ Rule::MAX_LENGTH ], $label2, 0, $this->vd->encoding() ) );		
	}
	
	function test_includes_length_range_rule_in_messages() {
		$rules = array(
			'foo' => array( Rule::LENGTH_RANGE => array( 5, 10 )  )
			);
		$values = array( 'foo' => 'bar' );
		
		$msg = '{length_range}';
		$this->vd->setMessage( Rule::LENGTH_RANGE, $msg );
		
		$problems = $this->vd->checkArray( $values, $rules );
		$expected = '5-10';
		$got = $problems[ 'foo' ][ Rule::LENGTH_RANGE ];
		$this->assertEquals( $expected, $got );
	}
	
	function test_includes_min_length_for_length_range_rules_in_messages() {
		$rules = array(
			'foo' => array( Rule::LENGTH_RANGE => array( 5, 10 )  )
			);
		$values = array( 'foo' => 'bar' );
		
		$msg = '{min_length}';
		$this->vd->setMessage( Rule::LENGTH_RANGE, $msg );
		
		$problems = $this->vd->checkArray( $values, $rules );
		$expected = '5';
		$got = $problems[ 'foo' ][ Rule::LENGTH_RANGE ];
		$this->assertEquals( $expected, $got );
	}
	
	function test_includes_max_length_for_length_range_rules_in_messages() {
		$rules = array(
			'foo' => array( Rule::LENGTH_RANGE => array( 5, 10 )  )
			);
		$values = array( 'foo' => 'bar' );
		
		$msg = '{max_length}';
		$this->vd->setMessage( Rule::LENGTH_RANGE, $msg );
		
		$problems = $this->vd->checkArray( $values, $rules );
		$expected = '10';
		$got = $problems[ 'foo' ][ Rule::LENGTH_RANGE ];
		$this->assertEquals( $expected, $got );
	}
	
	function test_includes_min_value_for_value_range_rules_in_messages() {
		$rules = array(
			'foo' => array( Rule::VALUE_RANGE => array( 5, 10 )  )
			);
		$values = array( 'foo' => 'bar' );
		
		$msg = '{min_value}';
		$this->vd->setMessage( Rule::VALUE_RANGE, $msg );
		
		$problems = $this->vd->checkArray( $values, $rules );
		$expected = '5';
		$got = $problems[ 'foo' ][ Rule::VALUE_RANGE ];
		$this->assertEquals( $expected, $got );
	}
	
	function test_validates_array_values() {
		$values = array(
			'foo' => array( 100, 200 )
			);
		$rules = array(
			'foo' => array( Rule::MAX_COUNT => 1 )
			);
		$problems = $this->vd->checkArray( $values, $rules );	
		$this->assertTrue( isset( $problems[ 'foo' ] ) );
	}	
	
	function test_validates_sub_rules_using_with() {
		
		$values = array(
			'foo' => 0,
			'bar' => array( 'val' => 10 )
			);
		
		$rules = array(
			'foo' => array( Rule::VALUE_RANGE => array( 5, 10 )  ),
			'bar' => array(
					Rule::WITH => array(
						'val' => array( Rule::MAX_VALUE => 9 )
					)
				)
			);
		$problems = $this->vd->checkArray( $values, $rules );
		
		//echo 'Returned problems: '; var_dump( $problems );
		
		$this->assertTrue( isset( $problems[ 'foo' ] ) );
		$this->assertTrue( isset( $problems[ 'bar' ] ) );
		$this->assertTrue( isset( $problems[ 'bar' ][ 'val' ] ) );
	}
	
}