<?php
namespace phputil;

/**
 *  Easy and powerful validator for PHP.
 *  
 *  @author	Thiago Delgado Pinto
 */
class Validator {
	
	const DEFAULT_LOCALE = 'en'; // English
	
	private $locale;
	private $encoding;

	private $messageHandler;
	private $formatChecker;
	private $ruleChecker;
	
	/**
	 *	Creates the validator.  
	 *
	 *  @param string $locale	(Optional) Locale. Defaults to DEFAULT_LOCALE.
	 *  @param string $encoding	(Optional) Encoding. Defaults to
	 *  						Encoding::DEFAULT_ENCODING.
	 *  
	 *  @return Validator
	 */
	function __construct( $locale = null, $encoding = null ) {
		$this->locale = isset( $locale ) ? $locale : self::DEFAULT_LOCALE;
		$this->encoding = isset( $encoding ) ? $encoding : Encoding::DEFAULT_ENCODING;
		$this->messageHandler = new MessageHandler( $this->locale );
		$this->formatChecker = new FormatChecker( $this->encoding );
		$this->ruleChecker = new RuleChecker( $this->formatChecker, $this->locale, $this->encoding );
	}
	
	/**
	 *  Returns the current validation locale (e.g. "en").
	 *  
	 *  @return string
	 */
	function locale() {
		return $this->locale;
	}
	
	/**
	 *  Returns the current validation encoding (e.g. "UTF-8").
	 *  
	 *  @return string
	 */	
	function encoding() {
		return $this->encoding;
	}
	
	// MESSAGE
	
	/**
	 *  Sets a message to be returned when a value hurts the given rule.
	 *  
	 *  @param string $rule		Rule name.
	 *  @param string $message	Problem message, that is, the message that
	 *  						should be returned when a value hurts the rule.
	 *  @param string $locale	(Optional) Locale.
	 *  
	 *  @return Validator
	 */
	function setMessage( $rule, $message, $locale = null ) {
		$this->messageHandler->set( $rule, $message, $locale );
		return $this;
	}
	
	/**
	 *  Sets all the messages.
	 *  
	 *  @param array $messages	Array of locale => rule => message.
	 *  
	 *  @return Validator
	 */
	function setMessages( array $messages ) {
		$this->messageHandler->setMessages( $messages );
		return $this;
	}	
	
	/**
	 *  Removes the registered message of the given rule.
	 *  
	 *  @param string $rule		Rule name.
	 *  @param string $locale	(Optional) Locale.
	 *  
	 *  @return Validator
	 */	
	function removeMessage( $rule, $locale = null ) {
		$this->messageHandler->remove( $rule, $locale );
		return $this;		
	}
	
	/**
	 *  Returns all the registered messages.
	 *  
	 *  @return array
	 */
	function messages() {
		return $this->messageHandler->messages();
	}
	
	/**
	 *  Returns all the registered messages for a given locale.
	 *  
	 *  @param string $locale Locale.
	 *  
	 *  @return array
	 */	
	function messagesFromLocale( $locale = null ) {
		return $this->messageHandler->messagesFromLocale( $locale );
	}
	
	/**
	 *  Returns the registered message for a given locale.
	 *  
	 *  @param string $rule		Rule name.
	 *  @param string $locale	(Optional) Locale.
	 *  
	 *  @return string
	 */		
	function ruleMessage( $rule, $locale = null ) {
		return $this->messageHandler->ruleMessage( $rule, $locale );
	}	
	
	// RULE
	
	/**
	 *  Sets a rule.
	 *  
	 *  @param string $name			Name for the rule.
	 *  @param callable	$callback	Function or method to be called,
	 *  							which should return a boolean value.
	 *  
	 *	@return Validator
	 */
	function setRule( $name, $callback ) {
		$this->ruleChecker->set( $name, $callback );
		return $this;
	}
	
	/**
	 *  Removes a rule.
	 *  
	 *  @param string $name Name of the rule.
	 *  
	 *	@return Validator
	 */	
	function removeRule( $name ) {
		$this->ruleChecker->remove( $name );
		return $this;
	}	
	
	/**
	 *	Returns all the registered rules.  
	 *
	 *  @return array
	 */
	function rules() {
		return $this->ruleChecker->methods();
	}
	
	// FORMAT
	
	/**
	 *  Sets a format.
	 *  
	 *  @param string $name			Name for the format.
	 *  @param callable	$callback	Function or method to be called,
	 *  							which should return a boolean value.
	 *  
	 *	@return Validator
	 */	
	function setFormat( $name, $callback ) {
		$this->formatChecker->set( $name, $callback );
		return $this;
	}
	
	/**
	 *  Removes a format.
	 *  
	 *  @param string $name Name of the format.
	 *  
	 *	@return Validator
	 */		
	function removeFormat( $name ) {
		$this->formatChecker->remove( $name );
		return $this;
	}	
	
	/**
	 *	Returns all the registered formats.  
	 *
	 *  @return array
	 */	
	function formats() {
		return $this->formatChecker->methods();
	}
	
	// CHECKING
	
	/**
	 *  Checks an array of values, according to an array of rules, and return
	 *  an array of problems.
	 *  
	 *  @param mixed $value		Value to be checked.
	 *  @param array $rules		Array of rule => value.
	 *  @param string $label	Label of the verified value (e.g. "age").
	 *  
	 *  @return array		Array of rule => problem message.
	 */	
	function check( $value, array $rules, $label = null ) {
		$allRules = $this->rules();
		$problems = array();
		foreach ( $rules as $k => $v ) {
			if ( isset( $allRules[ $k ] ) ) {
				// Calls a method of RuleChecker (or a user-defined rule method)
				$result = call_user_func( $allRules[ $k ], $value, $rules[ $k ] );
				if ( ! $result ) {
					$problems[ $k ] = $this->messageHandler->format(
						$value, $k, $rules, $this->locale(), $label );
				}
			}
		}
		return $problems;
	}

	/**
	 *  Checks an array of values, according to an array of rules, and return
	 *  an array of problems.
	 *  
	 *  @param array $valuesMap			Array of field => value.
	 *  @param array $fieldToRulesMap	Array of field => rule => value.
	 *  
	 *  @return array					Array of field => rule => problem message.
	 */
	function checkArray( array $valuesMap, array $fieldToRulesMap ) {
		$allRules = $this->rules();
		$problems = array();
		$this->performArrayCheck( $valuesMap, $fieldToRulesMap, $allRules, $problems );
		return $problems;
	}
	
	
	private function performArrayCheck(
		array $valuesMap
		, array $fieldToRulesMap
		, array $allRules
		, array &$problems
		) {
		
		foreach ( $fieldToRulesMap as $field => $rules ) {
			
			$value = array_key_exists( $field, $valuesMap ) ? $valuesMap[ $field ] : '';
			$label = isset( $rules[ Option::LABEL ] ) ? $rules[ Option::LABEL ] : $field;
			
			$fieldProblems = array();
			foreach ( $rules as $k => $v ) {
				
				if ( Rule::WITH == $k ) {				
					
					if ( ! array_key_exists( $field, $problems ) ) {
						$problems[ $field ] = array();
					}
					
					$this->performArrayCheck(
						(array) $valuesMap[ $field ]
						, $v
						, $allRules
						, $problems[ $field ]
						);
						
					if ( empty( $problems[ $field ] ) ) {
						unset( $problems[ $field ] );
					}
						
					continue;
				}
				
				if ( ! isset( $allRules[ $k ] ) ) {
					continue;
				}			
				
				// Calls a method of RuleChecker (or a user-defined rule method)
				$result = call_user_func( $allRules[ $k ], $value, $rules[ $k ] );
				if ( ! $result ) {
					$fieldProblems[ $k ] = $this->messageHandler->format(
						$value, $k, $rules, $this->locale(), $label );
				}
			}
			
			if ( ! empty( $fieldProblems ) ) {
				$problems[ $field ] = $fieldProblems;
			}
		}		
	}
	

	/**
	 *  Checks an object, according to an array of rules, and return
	 *  an array of problems.
	 *  
	 *  @param object $object			Object of stdClass or another class.
	 *  @param array $attrToRulesMap	Array of attribute => rule => value.
	 *  @param string $getterPrefix		(Optional) Prefix of getter methods.
	 *  								Defaults to 'get'. It should be
	 *  								ignored for an object of stdClass.
	 *  @param bool $useCamelCase		Whether it should use camelCase for
	 *  								accessing object's methods. It should
	 *  								be ignored for an object of stdClass.
	 *  
	 *  @return array					Array of attribute => rule => problem message.
	 */	
	function checkObject(
			$object,
			array $attrToRulesMap,
			$getterPrefix = 'get',
			$useCamelCase = true
			) {
		$array = (array) $this->toArray( $object );
		return $this->checkArray( $array, $attrToRulesMap );
	}		
	
	// UTIL
	
	function formatChecker() {
		return $this->formatChecker;
	}	
	
	function ruleChecker() {
		return $this->ruleChecker;
	}
	
	function messageHandler() {
		return $this->messageHandler;
	}
	
	// PRIVATE
	
	/** @return array */
	private function toArray( $obj ) {
		if ( is_object( $obj ) && 'stdClass' != get_class( $obj ) ) {
			$values = RTTI::getAttributes( $obj, RTTI::allFlags(), 'get', true, true );
			return $values;
		}
		return (array) $obj;
	}
}

?>