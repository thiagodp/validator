<?php
namespace phputil\tests;

require_once 'vendor/autoload.php';

use PHPUnit_Framework_TestCase;

use phputil\FormatChecker;
use phputil\RuleChecker;
use phputil\FormatOption;


/** Class used in some tests */
class Dummy {
	function doSomething() { return false; }
}


/**
 * Tests RuleChecker.
 *
 * @author	Thiago Delgado Pinto
 */
class RuleCheckerTest extends PHPUnit_Framework_TestCase {
		
	private $fc = null;
	private $rc = null;
	
	function setUp() {
		$this->fc = new FormatChecker();
		$this->rc = new RuleChecker( $this->fc );
	}
	
	// METHOD HANDLING
	
	function test_can_add_a_method() {
		$dummy = new Dummy();
		$this->rc->set( 'myRule', array( $dummy, 'doSomething' ) );
		$exists = isset( $this->rc->methods()[ 'myRule' ] );
		$this->assertTrue( $exists );
	}
	
	function test_can_remove_a_method() {
		$dummy = new Dummy();
		$this->rc->set( 'myRule', array( $dummy, 'doSomething' ) );
		$this->rc->remove( 'myRule' );
		$exists = isset( $this->rc->methods()[ 'myRule' ] );
		$this->assertFalse( $exists );
	}
	
	function test_can_add_a_function() {
		$this->rc->set( 'myRule', function( $x ) { return true; } );
		$exists = isset( $this->rc->methods()[ 'myRule' ] );
		$this->assertTrue( $exists );
	}	
	
	// RULES
	
	// Required
	
	function test_required_returns_false_when_value_is_empty() {
		$this->assertFalse( $this->rc->required( '' ) );
	}
	
	function test_required_returns_true_when_value_is_not_empty() {
		$this->assertTrue( $this->rc->required( ' ' ) );
	}
	
	// Min Length
	
	function test_min_length_returns_false_when_length_is_lower() {
		$this->assertFalse( $this->rc->min_length( '123', 4 ) );
	}
	
	function test_min_length_returns_true_when_length_is_equal() {
		$this->assertTrue( $this->rc->min_length( '123', 3 ) );
	}
	
	function test_min_length_returns_true_when_length_is_greater() {
		$this->assertTrue( $this->rc->min_length( '1234', 3 ) );
	}
	
	// Max Length
	
	function test_max_length_returns_false_when_length_is_greater() {
		$this->assertFalse( $this->rc->max_length( '123', 2 ) );
	}
	
	function test_max_length_returns_true_when_length_is_equal() {
		$this->assertTrue( $this->rc->max_length( '123', 3 ) );
	}
	
	function test_max_length_returns_true_when_length_is_lower() {
		$this->assertTrue( $this->rc->max_length( '123', 4 ) );
	}
	
	// Length Range
	
	function test_length_range_returns_false_when_length_is_out_of_range() {
		$this->assertFalse( $this->rc->length_range( '1234', array( 2, 3 ) ) );
		$this->assertFalse( $this->rc->length_range( '1', array( 2, 3 ) ) );
	}
	
	function test_length_range_returns_true_when_length_is_in_the_boundaries() {
		$this->assertTrue( $this->rc->length_range( '1234', array( 2, 4 ) ) );
		$this->assertTrue( $this->rc->length_range( '12', array( 2, 4 ) ) );
	}
	
	function test_length_range_returns_true_when_length_is_inside_range() {
		$this->assertTrue( $this->rc->length_range( '123', array( 2, 4 ) ) );
	}
	
	// Min Value
	
	function test_min_value_returns_false_when_value_is_lower() {
		$this->assertFalse( $this->rc->min_value( 9, 10 ) );
	}
	
	function test_min_value_returns_true_when_value_is_equal() {
		$this->assertTrue( $this->rc->min_value( 10, 10 ) );
	}
	
	function test_min_value_returns_true_when_value_is_greater() {
		$this->assertTrue( $this->rc->min_value( 11, 10 ) );
	}
	
	// Max Value
	
	function test_max_value_returns_false_when_value_is_greater() {
		$this->assertFalse( $this->rc->max_value( 10, 9 ) );
	}
	
	function test_max_value_returns_true_when_value_is_equal() {
		$this->assertTrue( $this->rc->max_value( 10, 10 ) );
	}
	
	function test_max_value_returns_true_when_value_is_lower() {
		$this->assertTrue( $this->rc->max_value( 9, 10 ) );
	}
	
	// Value Range
	
	function test_value_range_returns_false_when_value_is_out_of_range() {
		$this->assertFalse( $this->rc->value_range( 9, array( 10, 20 ) ) );
		$this->assertFalse( $this->rc->value_range( 21, array( 10, 20 ) ) );
	}
	
	function test_range_returns_true_when_value_is_in_the_boundaries() {
		$this->assertTrue( $this->rc->value_range( 10, array( 10, 20 ) ) );
		$this->assertTrue( $this->rc->value_range( 20, array( 10, 20 ) ) );
	}
	
	function test_range_returns_true_when_value_is_inside_range() {
		$this->assertTrue( $this->rc->value_range( 15, array( 10, 20 ) ) );
	}
	
	// Min Count
	
	function test_min_count_returns_true_when_value_is_greater() {
		$this->assertTrue( $this->rc->min_count( array( 'a', 'b' ), 1 ) );
	}
	
	function test_min_count_returns_true_when_value_is_equal() {
		$this->assertTrue( $this->rc->min_count( array( 'a' ), 1 ) );
	}
	
	function test_min_count_returns_false_when_value_is_lower() {
		$this->assertFalse( $this->rc->min_count( array(), 1 ) );
	}	
	
	// Max Count
	
	function test_max_count_returns_false_when_value_is_greater() {
		$this->assertFalse( $this->rc->max_count( array( 'a', 'b' ), 1 ) );
	}
	
	function test_max_count_returns_true_when_value_is_equal() {
		$this->assertTrue( $this->rc->max_count( array( 'a' ), 1 ) );
	}
	
	function test_max_count_returns_true_when_value_is_lower() {
		$this->assertTrue( $this->rc->max_count( array(), 1 ) );
	}	
	
	// Count Range
	
	function test_count_range_returns_false_when_value_is_out_of_range() {
		$this->assertFalse( $this->rc->count_range( array(), array( 1, 2 ) ) );
		$this->assertFalse( $this->rc->count_range( array( 'a', 'b', 'c' ), array( 1, 2 ) ) );
	}
	
	function test_count_range_returns_true_when_value_is_in_the_boundaries() {
		$this->assertTrue( $this->rc->count_range( array( 'a'), array( 1, 2 ) ) );
		$this->assertTrue( $this->rc->count_range( array( 'a', 'b' ), array( 1, 2 ) ) );
	}
	
	function test_count_range_returns_true_when_value_is_inside_range() {
		$this->assertTrue( $this->rc->count_range( array( 'a', 'b' ), array( 1, 3 ) ) );
	}
	
	// In
	
	function test_in_returns_true_when_non_array_value_is_equal() {
		$this->assertTrue( $this->rc->in( 100, 100 ) );
	}
	
	function test_in_returns_false_when_non_array_value_is_not_equal() {
		$this->assertFalse( $this->rc->in( 100, 99 ) );
	}	
	
	function test_in_returns_true_when_in_array() {
		$this->assertTrue( $this->rc->in( 100, array( 100 ) ) );
	}
	
	function test_in_returns_false_when_not_in_array() {
		$this->assertFalse( $this->rc->in( 100, array( 99 ) ) );
	}
	
	// Not In
	
	function test_not_in_returns_false_when_non_array_value_is_equal() {
		$this->assertFalse( $this->rc->not_in( 100, 100 ) );
	}
	
	function test_not_in_returns_true_when_non_array_value_is_not_equal() {
		$this->assertTrue( $this->rc->not_in( 100, 99 ) );
	}	
	
	function test_not_in_returns_false_when_in_array() {
		$this->assertFalse( $this->rc->not_in( 100, array( 100 ) ) );
	}
	
	function test_not_in_returns_true_when_not_in_array() {
		$this->assertTrue( $this->rc->not_in( 100, array( 99 ) ) );
	}	
	
	// RegEx
	
	function test_regex_returns_false_does_not_match() {
		$this->assertFalse( $this->rc->regex( '5', '/^[a-z]$/' ) );
	}
	
	function test_regex_returns_true_when_matches() {
		$this->assertTrue( $this->rc->regex( 't', '/^[a-z]$/' ) );
	}
	
	// Format
	
	function test_format_returns_false_does_not_match() {
		$this->assertFalse( $this->rc->format( 'a', 'numeric' ) );
	}
	
	function test_format_returns_true_when_matches() {
		$this->assertTrue( $this->rc->format( '5', 'numeric' ) );
	}
	
	function test_format_allows_array_with_format_name() {
		$result = $this->rc->format( '5', array( FormatOption::NAME => 'numeric' ) );
		$this->assertTrue( $result );
	}
	
	function test_format_allows_array_with_format_name_and_parameter() {
		$result = $this->rc->format( '1999-12-31',
			array( FormatOption::NAME => 'date_ymd', FormatOption::SEPARATOR => '-' )
			);
		$this->assertTrue( $result );
	}	
		
}

?>