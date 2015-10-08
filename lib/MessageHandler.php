<?php
namespace phputil;

/**
 *  Message handler.
 *  
 *  @author	Thiago Delgado Pinto
 */
class MessageHandler {
	
	// Additional keys
	const VALUE = 'value';
	const LABEL	= 'label';	// value is string | array( locale => string )
	// ---
	
	private $locale;
	private $messages;
	
	function __construct( $locale = 'en' ) {
		$this->locale = $locale;
		$this->messages = array();
	}
	
	function locale( $locale = null ) { // getter/setter
		if ( isset( $locale ) ) {
			$this->locale = $locale;
		}
		return $this->locale;
	}
	
	function add( $rule, $message, $locale = null ) {
		$loc = isset( $locale ) ? $locale : $this->locale();
		if ( ! isset( $this->messages[ $loc ] ) ) {
			$this->messages[ $loc ] = array();
		}
		$this->messages[ $loc ][ $rule ] = $message;
		return $this;
	}	
	
	function messages() {
		return $this->messages;
	}
	
	function setMessages( array $messages ) {
		$this->messages = $messages;
		return $this;
	}
	
	function messagesFromLocale( $locale = null ) {
		$loc = isset( $locale ) ? $locale : $this->locale();
		return isset( $this->messages[ $loc ] ) ? $this->messages[ $loc ] : array();
	}
	
	function ruleMessage( $ruleName, $locale = null ) {
		$locMessages = $this->messagesFromLocale( $locale );
		return isset( $locMessages[ $ruleName ] ) ? $locMessages[ $ruleName ] : '';
	}
	
	function format( $value, $ruleName, array $ruleValuesMap, $locale = null ) {
		$matches = array();
		$msg = $this->ruleMessage( $ruleName, $locale );
		if ( '' === $msg ) { return $msg; }
		$hasFields = preg_match_all( '/\{\w+\}/', $msg, $matches );
		if ( ! $hasFields ) {
			return $msg;
		}
		$rules = $ruleValuesMap; // copy
		$rules[ self::VALUE ] = $value; // add value, so that {value} is possible.
		if ( isset( $rules[ self::LABEL ] )
			&& is_array( $rules[ self::LABEL ] )
			&& count( $rules[ self::LABEL ] ) > 0
			) {
			
			$localeArray = array();
			if ( isset( $locale ) ) { // First the given locale
				$localeArray []= $locale;
			}
			$localeArray []= $this->locale(); // Then the default locale
			
			$label = '';
			foreach ( $localeArray as $loc ) {
				if ( isset( $rules[ self::LABEL ][ $loc ] ) ) {
					$label = $rules[ self::LABEL ][ $loc ];
					break;
				}
			}			
			// overwrites			
			$rules[ self::LABEL ] = $label;
		}
		//print_r( $matches ); die();
		foreach ( $matches[ 0 ] as $m ) {
			$key = str_replace( array( '{', '}' ), '', $m );
			if ( isset( $rules[ $key ] ) ) {
				$value = $rules[ $key ];
				$msg = str_replace( $m, $value, $msg );
			}
		}
		return $msg;
	}
	
}

?>