<?php
namespace phputil;

require_once 'Encoding.php';
require_once 'RuleChecker.php';
require_once 'MessageHandler.php';


/**
 *  Easy and powerful validator for PHP.
 *  
 *  @author	Thiago Delgado Pinto
 */
class Validator {
	
	const DEFAULT_LOCALE = 'en'; // English
	
	private $locale;
	private $encoding;
	//
	private $messageHandler;
	private $formatChecker;
	private $ruleChecker;
	
	
	function __construct( $locale = null, $encoding = null ) {
		$this->locale = isset( $locale ) ? $locale : self::DEFAULT_LOCALE;
		$this->encoding = isset( $encoding ) ? $encoding : Encoding::DEFAULT_ENCODING;
		$this->messageHandler = new MessageHandler( $this->locale );
		$this->formatChecker = new FormatChecker( $this->encoding );
		$this->ruleChecker = new RuleChecker( $this->formatChecker, $this->locale, $this->encoding );
	}
	
	function locale() {
		return $this->locale;
	}
	
	function encoding() {
		return $this->encoding;
	}
	
	// MESSAGE ________________________________________________________________
	
	function addMessage( $rule, $message, $locale = null ) {
		$this->messageHandler->add( $rule, $message, $locale );
		return $this;
	}
	
	function messages() {
		return $this->messageHandler->messages();
	}
	
	function setMessages( array $messages ) {
		$this->messageHandler->setMessages( $messages );
		return $this;
	}	
	
	function messagesFromLocale( $locale = null ) {
		return $this->messageHandler->messagesFromLocale( $locale );
	}
	
	function ruleMessage( $ruleName, $locale = null ) {
		return $this->messageHandler->ruleMessage( $ruleName, $locale );
	}	
	
	// RULE ___________________________________________________________________
	
	function addRule( $ruleName, $ruleCallback ) {
		$this->ruleChecker->add( $ruleName, $ruleCallback );
		return $this;
	}
	
	function removeRule( $ruleName ) {
		$this->ruleChecker->remove( $ruleName );
		return $this;
	}	
	
	function rules() {
		return $this->ruleChecker->methods();
	}
	
	// FORMAT _________________________________________________________________
	
	function addFormat( $formatName, $formatCallback ) {
		$this->formatChecker->add( $formatName, $formatCallback );
		return $this;
	}
	
	function removeFormat( $formatName, $formatCallback ) {
		$this->formatChecker->add( $formatName, $formatCallback );
		return $this;
	}	
	
	function formats() {
		return $this->formatChecker->methods();
	}
	
	// CHECKING _______________________________________________________________
	
	function check( $value, array $rules ) {
		$allRules = $this->rules();
		$problems = array();
		foreach ( $rules as $k => $v ) {
			if ( isset( $allRules[ $k ] ) ) {
				$result = call_user_func( $allRules[ $k ], $value, $rules[ $k ] );
				if ( ! $result ) {
					$problems[ $k ] = $this->messageHandler->format( $value, $k, $rules );
					//$problems[ $k ] = isset( $this->messages[ $k ] ) ? $this->messages[ $k ] : '';
				}
			}
		}
		return $problems;
	}
	
	function checkArray( array $valuesMap, array $fieldToRulesMap ) {
		$allRules = $this->rules();
		$problems = array();
		foreach ( $fieldToRulesMap as $field => $rules ) {
			$value = array_key_exists( $field, $valuesMap ) ? $valuesMap[ $field ] : '';
			$problems[ $field ] = $this->check( $value, $rules );
		}
		return $problems;
	}
	
	// UTIL ___________________________________________________________________
	
	function formatChecker() {
		return $this->formatChecker;
	}	
	
	function ruleChecker() {
		return $this->ruleChecker;
	}
	
	function messageHandler() {
		return $this->messageHandler;
	}	
}

?>