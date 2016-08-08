<?php
namespace phputil\tests;

require_once 'lib/FormatChecker.php';
require_once 'lib/RuleChecker.php';

use PHPUnit_Framework_TestCase;
use phputil\FormatChecker;
use phputil\RuleChecker;


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
	
	// METHOD HANDLING ________________________________________________________
	
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
	
	// RULES __________________________________________________________________
	
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
	
	// Min
	
	function test_min_returns_false_when_value_is_lower() {
		$this->assertFalse( $this->rc->min_value( 9, 10 ) );
	}
	
	function test_min_returns_true_when_value_is_equal() {
		$this->assertTrue( $this->rc->min_value( 10, 10 ) );
	}
	
	function test_min_returns_true_when_value_is_greater() {
		$this->assertTrue( $this->rc->min_value( 11, 10 ) );
	}
	
	// Max
	
	function test_max_returns_false_when_value_is_greater() {
		$this->assertFalse( $this->rc->max_value( 10, 9 ) );
	}
	
	function test_max_returns_true_when_value_is_equal() {
		$this->assertTrue( $this->rc->max_value( 10, 10 ) );
	}
	
	function test_max_returns_true_when_value_is_lower() {
		$this->assertTrue( $this->rc->max_value( 9, 10 ) );
	}
	
	// Range
	
	function test_range_returns_false_when_value_is_out_of_range() {
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
		
}

?>