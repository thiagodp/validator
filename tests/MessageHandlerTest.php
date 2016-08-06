<?php
namespace phputil\tests;

require_once 'lib/MessageHandler.php'; // phpunit will be executed from the project root

use PHPUnit_Framework_TestCase;
use phputil\MessageHandler;

/**
 * Tests MessageHandler.
 *
 * @author	Thiago Delgado Pinto
 */
class MessageHandlerTest extends PHPUnit_Framework_TestCase {
	
	private $mh = null;
	
	function setUp() {
		$this->locale = 'en';
		$this->mh = new MessageHandler( $this->locale );
	}
	
	function test_can_add_messages_to_an_empty_locale() {
		$m = new MessageHandler( '' );
		$message = 'value';
		$m->set( 'key', $message );
		$this->assertEquals( $message, $m->ruleMessage( 'key' ) );
	}
	
	function test_can_add_messages_to_a_defined_locale() {
		$message = 'value';
		$this->mh->set( 'key', $message );
		$this->assertEquals( $message, $this->mh->ruleMessage( 'key' ) );
	}
	
	function test_can_get_messages_from_a_locale() {
		$message = 'value';
		$this->mh->set( 'key', $message );
		$expected = array( 'key' => $message );
		$this->assertEquals( $expected, $this->mh->messagesFromLocale() );
	}
	
	function test_can_get_all_messages() {
		$message = 'value';
		$this->mh->set( 'key', $message );
		$expected = array( $this->locale => array( 'key' => $message ) );
		$this->assertEquals( $expected, $this->mh->messages() );
	}
	
	function test_format_returns_an_empty_message_when_the_rule_is_not_found() {
		$expected = '';
		$got = $this->mh->format( 'any value', 'rule key', array() );
		$this->assertEquals( $expected, $got );
	}	
	
	function test_format_allows_to_use_the_value() {
		$value = 'hello';
		$message = 'value is {value}';
		$this->mh->set( 'key', $message );
		
		$expected = "value is $value";
		$got = $this->mh->format( $value, 'key', array() );
		
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_allows_to_use_the_value_more_than_once() {
		$value = 'hello';
		$message = 'value is {value} {value} {value}';
		$this->mh->set( 'key', $message );
		
		$expected = "value is $value $value $value";
		$got = $this->mh->format( $value, 'key', array() );
		
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_allows_to_use_a_label_as_string() {
		$value = 'Bob';
		$label = 'Name';
		$message = '{label} is {value}';
		
		$rule = 'required';
		$this->mh->set( $rule, $message );
		
		$rules = array( 'label' => $label ); // as string
		$ruleToVerify = $rule;
		
		$expected = "$label is $value";
		$got = $this->mh->format( $value, $ruleToVerify, $rules );
		
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_allows_to_use_a_label_for_a_locale_as_array() {
		$value = 'Bob';
		$label = 'Name';
		$message = '{label} is {value}';
		
		$rule = 'required';
		$this->mh->set( $rule, $message );
		
		$rules = array( 'label' => array( $this->locale => $label ) ); // array with locale
		$ruleToVerify = $rule;
		
		$expected = "$label is $value";
		$got = $this->mh->format( $value, $ruleToVerify, $rules );
		
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_replaces_the_label_even_if_it_receives_an_array_without_a_locale() {
		$value = 'Bob';
		$label = 'Name';
		$message = '{label} is {value}';
		
		$rule = 'required';
		$this->mh->set( $rule, $message );
		
		$rules = array( 'label' => array( $label ) ); // array WITHOUT locale !
		$ruleToVerify = $rule;
		
		$expected = "$label is $value";
		$got = $this->mh->format( $value, $ruleToVerify, $rules );
		
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_allows_to_use_rules_in_the_message() {
		$value = 'Bob';
		$label = 'Name';
		
		$message = '{label} is {value}, required is {required}, min_value is {min_value}';
		$this->mh->set( 'required', $message );
		
		$rules = array(
			'label' => $label,
			'required' => true,
			'min_value' => 2
			);
		$ruleToVerify = 'required';
		
		$expected = "$label is $value, required is ${rules['required']}, min_value is ${rules['min_value']}";
		$got = $this->mh->format( $value, $ruleToVerify, $rules );
		
		$this->assertEquals( $expected, $got );		
	}
		
}

?>