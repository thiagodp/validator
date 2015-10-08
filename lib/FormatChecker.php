<?php
namespace phputil;

/**
 *  Default formats.
 *  
 *  @author	Thiago Delgado Pinto
 */
class Format {
	
	const ANYTHING		= 'anything';
	const WORD			= 'word';		// ALPHA_NUMERIC + SPACE + TAB	
	const ALPHA_NUMERIC	= 'alphanumeric';
	const ALPHA			= 'alpha';
	const ASCII			= 'ascii';		// character codes 0-127
	const NUMERIC		= 'numeric';
	const INTEGER		= 'integer';
	const PRICE			= 'price';		// numeric(N,2)
	const TAX			= 'tax';		// numeric(N,3)
	const DATE			= 'date';		// mm/dd/yyyy
	const TIME			= 'time';		// hh:mm:ss
	const SHORT_TIME	= 'shorttime';	// hh:mm
	const DATE_TIME		= 'datetime';
	const EMAIL			= 'email';
	const HTTP_URL		= 'http';		// http://...
	const URL			= 'url';		// any://...
	const IP			= 'ip';			// ipv4 or ipv6
	const IPV4			= 'ipv4';
	const IPV6			= 'ipv6';
	
    static function all() {
        return array_values( ( new \ReflectionClass( __CLASS__ ) )->getConstants() );
    }	
}


/**
 *  Format exception.
 *  
 *  @author	Thiago Delgado Pinto
 */
class FormatException extends \Exception {}
	
/**
 *  Format checker.
 *  
 *  @author	Thiago Delgado Pinto
 */
class FormatChecker {
	
	static function originalMethods( FormatChecker $instance ) {
		$array = Format::all();
		$methods = array();
		foreach ( $array as $a ) {
			$methods[ $a ] = array( $instance, '_' . $a );
		}
		return $methods;
	}	
	
	private $originalMethods; // kept for performance reasons only
	private $methods;
	
	private $encoding;
	private $decimalPlacesSeparator;
	
	private $dateFormat;
	private $timeFormat;
	private $shortTimeFormat;
	private $dateTimeFormat;
	
	function __construct( $encoding = null ) {
		$this->originalMethods = FormatChecker::originalMethods( $this );
		$this->methods = array();
		$this->encoding = isset( $encoding ) ? $encoding : Encoding::DEFAULT_ENCODING;
		$this->decimalPlacesSeparator = '.';
	}
	
	// METHOD HANDLING ________________________________________________________
	
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
	
	// CONFIGURATION __________________________________________________________
	
	function encoding( $encoding = null ) { // getter/setter
		if ( isset( $encoding ) ) {
			$this->encoding = $encoding;
		}
		return $this->encoding;
	}
	
	function decimalPlacesSeparator( $separator = null ) { // getter/setter
		if ( isset( $separator ) ) {
			$this->decimalPlacesSeparator = $separator;
		}
		return $this->decimalPlacesSeparator;
	}
	
	function dateFormat( $dateFormat = null ) { // getter/setter
		if ( isset( $dateFormat ) ) {
			$this->dateFormat = $dateFormat;
		}
		return $this->dateFormat;
	}
	
	function timeFormat( $timeFormat = null ) { // getter/setter
		if ( isset( $timeFormat ) ) {
			$this->timeFormat = $timeFormat;
		}
		return $this->timeFormat;
	}
	
	function shortTimeFormat( $shortTimeFormat = null ) { // getter/setter
		if ( isset( $shortTimeFormat ) ) {
			$this->shortTimeFormat = $shortTimeFormat;
		}
		return $this->shortTimeFormat;
	}
	
	function dateTimeFormat( $dateTimeFormat = null ) { // getter/setter
		if ( isset( $dateTimeFormat ) ) {
			$this->dateTimeFormat = $dateTimeFormat;
		}
		return $this->dateTimeFormat;
	}	
	
	protected function decimalPlacesSeparatorAsString() {
		return is_array( $this->decimalPlacesSeparator )
			? implode( '', $this->decimalPlacesSeparator )
			: $this->decimalPlacesSeparator;
	}
	
	function encodingIsUnicode() {
		return preg_match( '/(utf)/i', $this->encoding );
	}
	
	function encodingRegExSymbol() {
		return $this->encodingIsUnicode() ? 'u' : '';
	}
	
	// FORMATTING _____________________________________________________________
	
	function _anything( $value ) {
		return true;
	}
	
	function _word( $value ) {
		$u = $this->encodingRegExSymbol();
		return preg_match( '/[[:word:]]*/' . $u, $value );
	}	
	
	function _alphanumeric( $value ) {
		return preg_match( '/[[:alnum:]]*/' . $u, $value );
	}
	
	function _alpha( $value ) {
		return preg_match( '/[[:alpha:]]*/' . $u, $value );
	}
	
	function _ascii( $value ) {
		return preg_match( '/[[:ascii:]]*/' . $u, $value );
	}
	
	function _numeric( $value ) {
		return is_numeric( $value );
	}
	
	function _integer( $value ) {
		return is_integer( $value );
	}
	
	function _price( $value ) {
		$separators = $this->decimalPlacesSeparatorAsString();
		return preg_match( '[[:digit:]]*[' . $separators . ']{0,1}[][[:digit:]]{1,2}', $value );
	}
	
	function _tax( $value ) {
		$separators = $this->decimalPlacesSeparatorAsString();
		return preg_match( '[[:digit:]]*[' . $separators . ']{0,1}[][[:digit:]]{1,3}', $value );
	}	

}

?>