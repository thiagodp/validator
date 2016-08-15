<?php
namespace phputil\validator\tests;

require_once 'vendor/autoload.php';

use \PHPUnit_Framework_TestCase;

use \phputil\validator\MessageHandler;

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
	
	// Locale
	
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
	
	function test_format_returns_the_correct_localized_message() {
		$messages = array(
			'en' => array( 'hello' => 'Hello!' ),
			'pt' => array( 'hello' => 'Olá!' )
			);
		$this->mh->setMessages( $messages );
		
		$msg = $this->mh->ruleMessage( 'hello' );
		$this->assertEquals( 'Hello!', $msg );
		
		$this->mh->locale( 'pt' );
		$msg = $this->mh->ruleMessage( 'hello' );
		$this->assertEquals( 'Olá!', $msg );
	}
	
	// Value
	
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
	
	// Label
	
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
	
	// Length Range
	
	function test_format_replaces_length_range_with_their_values() {
		$rules = array( 'length_range' => array( 2, 5 ) );
		$this->mh->set( 'length_range', 'Required length: {length_range}' );
		$got = $this->mh->format( '', 'length_range', $rules );
		$expected = 'Required length: 2-5';
		$this->assertEquals( $expected, $got );		
	}
	
	function test_format_replaces_min_length_when_using_length_range() {
		$rules = array( 'length_range' => array( 2, 5 ) );
		$this->mh->set( 'length_range', 'Required length: {min_length}' );
		$got = $this->mh->format( '', 'length_range', $rules );
		$expected = 'Required length: 2';
		$this->assertEquals( $expected, $got );		
	}
	
	function test_format_replaces_max_length_when_using_length_range() {
		$rules = array( 'length_range' => array( 2, 5 ) );
		$this->mh->set( 'length_range', 'Required length: {max_length}' );
		$got = $this->mh->format( '', 'length_range', $rules );
		$expected = 'Required length: 5';
		$this->assertEquals( $expected, $got );		
	}
	
	// Start With
	
	function test_format_replaces_start_with() {
		$rules = array( 'start_with' => 'hello' );
		$this->mh->set( 'start_with', 'Should start with {start_with}.' );
		$got = $this->mh->format( '', 'start_with', $rules );
		$expected = 'Should start with hello.';
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_replaces_start_with_by_all_their_values_separated_with_comma() {
		$rules = array( 'start_with' => array( 'hello', 'hi' ) );
		$this->mh->set( 'start_with', 'Should start with one of these: {start_with}.' );
		$got = $this->mh->format( '', 'start_with', $rules );
		$expected = 'Should start with one of these: hello, hi.';
		$this->assertEquals( $expected, $got );
	}
	
	// Mixed rules
	
	function test_format_allows_to_use_mixed_rules_in_the_message() {
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