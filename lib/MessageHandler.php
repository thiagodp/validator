<?php
namespace phputil;

/**
 *  Option.
 *  
 *  @author	Thiago Delgado Pinto
 */
class Option {
	const LABEL	= 'label';	// value is string | array( locale => string )	
}

/**
 *  Message handler.
 *  
 *  @author	Thiago Delgado Pinto
 */
class MessageHandler {
	
	// Additional keys
	const VALUE = 'value';
	
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
	
	function set( $rule, $message, $locale = null ) {
		$loc = isset( $locale ) ? $locale : $this->locale();
		if ( ! isset( $this->messages[ $loc ] ) ) {
			$this->messages[ $loc ] = array();
		}
		$this->messages[ $loc ][ $rule ] = $message;
		return $this;
	}
	
	function remove( $rule, $locale = null ) {
		$loc = isset( $locale ) ? $locale : $this->locale();
		if ( isset( $this->messages[ $loc ] ) ) {
			unset( $this->messages[ $loc ][ $rule ] );
		}
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
	
	function format(
			$value
			, $ruleName
			, array $rules
			, $locale = null
			, $label = null
			) {
		$matches = array();
		$msg = $this->ruleMessage( $ruleName, $locale );
		if ( '' === $msg ) { return $msg; }
		$hasFields = preg_match_all( '/\{\w+\}/', $msg, $matches );
		if ( ! $hasFields ) {
			return $msg;
		}
		// Adds "value", so that {value} is possible.
		$rules[ self::VALUE ] = $value;
		
		$hasOptionLabel = isset( $rules[ Option::LABEL ] );
		
		$canBeALabel = $hasOptionLabel && $this->canBeALabel( $rules[ Option::LABEL ] );	
			
		if ( $canBeALabel ) {
			
			if ( is_array( $rules[ Option::LABEL ] ) ) {
				
				$localeArray = array();
				if ( isset( $locale ) ) { // First the given locale
					$localeArray []= $locale;
				}
				$localeArray []= $this->locale(); // Then the default locale
				
				$value = $rules[ Option::LABEL ];
				$lbl = '';
				foreach ( $localeArray as $loc ) {
					if ( isset( $value[ $loc ] ) ) {
						$lbl = $value[ $loc ];
						break;
					}
				}
				
				// Replaces the rule with the correct label for the locale
				if ( $lbl != '' ) {
					$rules[ Option::LABEL ] = $lbl;
				} else if ( isset( $value[ 0 ] ) ) { // without locale
					$rules[ Option::LABEL ] = $value[ 0 ];
				}					
			} // else - not needed, it is a string

		} else if ( $label !== null ) {
			$rules[ Option::LABEL ] = $label; // Adds "label"
		} else if ( $hasOptionLabel ) {
			unset( $rules[ Option::LABEL ] );
		}
		//print $msg . PHP_EOL;
		//print_r( $matches ); die();
		
		// When a "length_range" rule is found, includes the parsing
		// of "min_length" and "max_length". When a "value_range" is
		// found, includes the parsing of "min_value" and "max_value".
		// The parsing is included by including the rule.
		$ranges = array(
			'length_range' => array( 'min_length', 'max_length' )
			, 'value_range' => array( 'min_value', 'max_value' )
			);
		foreach ( $ranges as $rKey => $rValue ) {
			if ( ! isset( $rules[ $rKey ] ) ) { continue; }
			if ( ! is_array( $rValue ) || count( $rValue ) !== 2 ) { continue; }
			foreach ( $rValue as $rValueKey => $rValueValue ) {
				if ( isset( $rules[ $rValueValue ] ) ) { continue; }
				// Includes a rule using the respective value for min or max
				$rules[ $rValueValue ] = $rules[ $rKey ][ $rValueKey ];
			}
		}
		
		// Replaces the fields in the messages
		foreach ( $matches[ 0 ] as $m ) {
			$v = is_array( $m ) ? $m[ 0 ] : $m;
			$key = str_replace( array( '{', '}' ), '', $v );
			if ( isset( $rules[ $key ] ) ) {
				$value = $rules[ $key ];
				if ( is_array( $value ) ) { // it is a range
					$v0 = isset( $value[ 0 ] ) ? $value[ 0 ] : '';
					$v1 = isset( $value[ 1 ] ) ? $value[ 1 ] : '';
					$value = $v0 . '-' . $v1;
				}
				$msg = str_replace( $v, $value, $msg );
			}
		}
		
		return $msg;
	}
	
	
	// PRIVATE
	
	private function canBeALabel( $value ) {
		return ( is_array( $value ) && count( $value ) > 0 )
			|| ( is_string( $value ) && mb_strlen( $value ) > 0 );
	}
}

?>