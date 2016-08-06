<?php
namespace phputil;

require_once 'Encoding.php';
require_once 'FormatChecker.php';

/**
 *  Default rules.
 *  
 *  @author	Thiago Delgado Pinto
 */
class Rule {
	
	const REQUIRED		= 'required';
	const MIN_LENGTH	= 'min_length';
	const MAX_LENGTH	= 'max_length';
	const LENGTH_RANGE	= 'length_range';
	const MIN_VALUE		= 'min';
	const MAX_VALUE		= 'max';
	const RANGE			= 'range';
	const REGEX			= 'regex';
	const FORMAT		= 'format';
	
    static function all() {
        return array_values( ( new \ReflectionClass( __CLASS__ ) )->getConstants() );
    }
}

/**
 *  Rule exception.
 *  
 *  @author	Thiago Delgado Pinto
 */
class RuleException extends \Exception {}

/**
 *  Rule checker.
 *  
 *  @author	Thiago Delgado Pinto
 */
class RuleChecker {
	
	const DEFAULT_LOCALE = 'en';
	
	static function originalMethods( RuleChecker $instance ) {
		$array = Rule::all();
		$methods = array();
		foreach ( $array as $a ) {
			$methods[ $a ] = array( $instance, $a );
		}
		return $methods;
	}
	
	private $originalMethods; // kept for performance reasons only	
	private $methods;
	private $formatChecker;
	private $encoding;
	
	function __construct( FormatChecker $formatChecker, $locale = null, $encoding = null ) {
		$this->originalMethods = RuleChecker::originalMethods( $this );
		$this->methods = array();
		$this->formatChecker = $formatChecker;
		$this->locale = isset( $locale ) ? $locale : self::DEFAULT_LOCALE;
		$this->encoding = isset( $encoding ) ? $encoding : Encoding::DEFAULT_ENCODING;
	}
	
	// METHOD HANDLING
	
	function add( $name, $callback ) {
		$this->methods[ $name ] = $callback;
		return $this;
	}
	
	function remove( $name ) {
		unset( $this->methods[ $name ] );
		return $this;
	}
	
	function methods() {
		return array_merge( $this->originalMethods, $this->methods );
	}	
	
	// CONFIGURATION
	
	function locale( $locale = null ) { // getter/setter
		if ( isset( $locale ) ) {
			$this->locale = $locale;
		}
		return $this->locale;		
	}
	
	function encoding( $encoding = null ) { // getter/setter
		if ( isset( $encoding ) ) {
			$this->encoding = $encoding;
		}
		return $this->encoding;
	}
	
	// RULES
	
	function required( $value, $ruleValue = true ) {
		return false === $ruleValue
			|| 0 === $ruleValue
			|| mb_strlen( $value, $this->encoding ) > 0
			;
	}
	
	function min_length( $value, $ruleValue ) {
		return mb_strlen( $value, $this->encoding ) >= $ruleValue;
	}
	
	function max_length( $value, $ruleValue ) {
		return mb_strlen( $value, $this->encoding ) <= $ruleValue;
	}

	function length_range( $value, $ruleValue ) {
		if ( ! is_array( $ruleValue ) ) {
			return mb_strlen( $value, $this->encoding ) === $ruleValue;
		}
		$array = $this->rangeValues( $ruleValue );
		$min = $array[ 0 ];
		$max = $array[ 1 ];
		return $this->min_length( $value, $min ) && $this->max_length( $value, $max );
	}
	
	function min( $value, $ruleValue ) {
		return $value >= $ruleValue;
	}
	
	function max( $value, $ruleValue ) {
		return $value <= $ruleValue;
	}
	
	function range( $value, $ruleValue ) {
		if ( ! is_array( $ruleValue ) ) {
			return $value === $ruleValue;
		}
		$array = $this->rangeValues( $ruleValue );
		$min = $array[ 0 ];
		$max = $array[ 1 ];
		return $this->min( $value, $min ) && $this->max( $value, $max );		
	}
	
	function regex( $value, $ruleValue ) {
		return 1 === preg_match( $ruleValue, $value );
	}
	
	function format( $value, $ruleValue ) {
		/*
		$methods = $this->formatChecker->methods();
		if ( ! isset( $methods[ $ruleValue ] ) ) {
			throw new FormatException( 'Formatting rule "'. $ruleValue . '" is not available.' );
		}
		return call_user_func( $methods[ $ruleValue ], $value );
		*/
		$methods = $this->formatChecker->methods();
		if ( is_string( $ruleValue ) ) {
			if ( ! isset( $methods[ $ruleValue ] ) ) {
				throw new FormatException( 'Formatting rule "'. $ruleValue . '" is not available.' );
			}
			return call_user_func( $methods[ $ruleValue ], $value );
		}
		if ( ! is_array( $ruleValue ) ) {
			throw new FormatException( 'Formatting rule should be a string or an array.' );
		}		
		if ( ! isset( $ruleValue[ $this->locale ] ) ) {
			throw new FormatException( 'Formatting rule for locale "'. $this->locale . '" is not available.' );
		}
		
		$name = $ruleValue[ $this->locale ];
		if ( ! isset( $methods[ $name ] ) ) {
			throw new FormatException( 'Formatting rule "'. $name . '" is not available.' );
		}
		return call_user_func( $methods[ $name ], $value );
	}
	
	// OTHER
	
	protected function rangeValues( array $ruleValue ) {
		$array = array_values( $ruleValue );
		if ( count( $array ) !== 2 ) {
			throw new RuleException( 'Invalid range value: array size is '. count( $array ) .'. Correct is 2.' );
		}
		$min = $array[ 0 ];
		$max = $array[ 1 ];
		if ( ! is_integer( $min ) || ! is_integer( $max ) ) {
			throw new RuleException( 'Invalid range value: min or max are not integer values.' );
		}
		if ( $min > $max ) {
			return array( $max, $min );
		}		
		return array( $min, $max );
	}
	
}

?>