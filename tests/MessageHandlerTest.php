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
	
	function test_empty_locale() {
		$m = new MessageHandler( '' );
		$message = 'value';
		$m->add( 'key', $message );
		$this->assertEquals( $message, $m->ruleMessage( 'key' ) );
	}
	
	function test_rule_message() {
		$message = 'value';
		$this->mh->add( 'key', $message );
		$this->assertEquals( $message, $this->mh->ruleMessage( 'key' ) );
	}
	
	function test_messages_from_locale() {
		$message = 'value';
		$this->mh->add( 'key', $message );
		$expected = array( 'key' => $message );
		$this->assertEquals( $expected, $this->mh->messagesFromLocale() );
	}
	
	function test_messages() {
		$message = 'value';
		$this->mh->add( 'key', $message );
		$expected = array( $this->locale => array( 'key' => $message ) );
		$this->assertEquals( $expected, $this->mh->messages() );
	}
	
	function test_format_returns_empty_message_when_rule_is_not_found() {
		$expected = '';
		$got = $this->mh->format( 'any value', 'rule key', array() );
		$this->assertEquals( $expected, $got );
	}	
	
	function test_format_message_with_value() {
		$value = 'hello';
		$message = 'value is {value}';
		$this->mh->add( 'key', $message );
		
		$expected = "value is $value";
		$got = $this->mh->format( $value, 'key', array() );
		
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_message_with_value_more_than_one_time_also_works() {
		$value = 'hello';
		$message = 'value is {value} {value} {value}';
		$this->mh->add( 'key', $message );
		
		$expected = "value is $value $value $value";
		$got = $this->mh->format( $value, 'key', array() );
		
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_message_with_label_string() {
		$value = 'Bob';
		$label = 'Name';
		$message = '{label} is {value}';
		
		$rule = 'required';
		$this->mh->add( $rule, $message );
		
		$rules = array( 'label' => $label ); // as string
		$ruleToVerify = $rule;
		
		$expected = "$label is $value";
		$got = $this->mh->format( $value, $ruleToVerify, $rules );
		
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_message_with_label_array_with_locale() {
		$value = 'Bob';
		$label = 'Name';
		$message = '{label} is {value}';
		
		$rule = 'required';
		$this->mh->add( $rule, $message );
		
		$rules = array( 'label' => array( $this->locale => $label ) ); // array with locale
		$ruleToVerify = $rule;
		
		$expected = "$label is $value";
		$got = $this->mh->format( $value, $ruleToVerify, $rules );
		
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_message_with_label_array_without_locale_makes_the_label_empty() {
		$value = 'Bob';
		$label = 'Name';
		$message = '{label} is {value}';
		
		$rule = 'required';
		$this->mh->add( $rule, $message );
		
		$rules = array( 'label' => array( $label ) ); // array WITHOUT locale !
		$ruleToVerify = $rule;
		
		$expected = " is $value";
		$got = $this->mh->format( $value, $ruleToVerify, $rules );
		
		$this->assertEquals( $expected, $got );
	}
	
	function test_format_message_with_rule_name() {
		$value = 'Bob';
		$label = 'Name';
		
		$message = '{label} is {value}, required is {required}, min_value is {min_value}';
		$this->mh->add( 'required', $message );
		
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