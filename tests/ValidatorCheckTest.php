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
	
}