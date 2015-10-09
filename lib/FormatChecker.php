<?php
namespace phputil;

/**
 *  Default formats.
 *  
 *  @author	Thiago Delgado Pinto
 */
class Format {
	
	const ANYTHING			= 'anything';
	const NAME				= 'name';				// alpha, space, dot, dash
	const WORD				= 'word';				// alpha, underline
	const ALPHA_NUMERIC		= 'alphanumeric';		// alpha, number
	const ALPHA				= 'alpha';				// just alpha
	const ASCII				= 'ascii';				// character codes 0-127
	const NUMERIC			= 'numeric';			// number
	const INTEGER			= 'integer';
	const PRICE				= 'price';				// numeric(N,2)
	const TAX				= 'tax';				// numeric(N,3)
	/*
	const DATE_YMD			= 'date_ymd';			// yyyy/dd/mm
	const DATE_MDY			= 'date_mdy';			// mm/dd/yyyy
	const DATE_DMY			= 'date_dmy';			// dd/mm/yyyy
	
	const DATE_YMD_DOTTED	= 'date_ymd_dotted';	// yyyy.dd.mm
	const DATE_MDY_DOTTED	= 'date_mdy_dotted';	// mm.dd.yyyy
	const DATE_DMY_DOTTED	= 'date_dmy_dotted';	// dd.mm.yyyy
	*/
	const DATE				= 'date';				// mm/dd/yyyy
	const TIME				= 'time';				// hh:mm
	const SHORT_TIME		= 'longtime';			// hh:mm:ss
	const DATE_TIME			= 'datetime';			// mm/dd/yyyy hh:mm
	const LONG_DATE_TIME	= 'longdatetime';		// mm/dd/yyyy hh:mm:ss	
	const EMAIL				= 'email';
	const HTTP_URL			= 'http';				// http://... or https://...
	const URL				= 'url';				// any://...
	const IP				= 'ip';					// ipv4 or ipv6
	const IPV4				= 'ipv4';
	const IPV6				= 'ipv6';
	
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
	private $longTimeFormat;
	private $dateTimeFormat;
	private $longDateTimeFormat;
	
	function __construct( $encoding = null ) {
		$this->originalMethods = FormatChecker::originalMethods( $this );
		$this->methods = array();
		$this->encoding = isset( $encoding ) ? $encoding : Encoding::DEFAULT_ENCODING;
		$this->decimalPlacesSeparator = '.';
		$this->dateFormat = 'm/d/Y';
		$this->timeFormat = 'H:i';
		$this->longTimeFormat = 'H:i:s';
		$this->dateTimeFormat = 'm/d/Y H:i';
		$this->longDateTimeFormat = 'm/d/Y H:i:s';
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
	
	function longTimeFormat( $longTimeFormat = null ) { // getter/setter
		if ( isset( $longTimeFormat ) ) {
			$this->longTimeFormat = $longTimeFormat;
		}
		return $this->longTimeFormat;
	}
	
	function dateTimeFormat( $dateTimeFormat = null ) { // getter/setter
		if ( isset( $dateTimeFormat ) ) {
			$this->dateTimeFormat = $dateTimeFormat;
		}
		return $this->dateTimeFormat;
	}

	function longDateTimeFormat( $longDateTimeFormat = null ) { // getter/setter
		if ( isset( $longDateTimeFormat ) ) {
			$this->longDateTimeFormat = $longDateTimeFormat;
		}
		return $this->longDateTimeFormat;
	}
	
	protected function decimalPlacesSeparatorAsString() {
		return is_array( $this->decimalPlacesSeparator )
			? implode( '', $this->decimalPlacesSeparator )
			: $this->decimalPlacesSeparator;
	}
	
	function encodingIsUnicode() {		
		return preg_match( '/^(utf)/i', $this->encoding );
	}
	
	function encodingRegExSymbol() {
		return $this->encodingIsUnicode() ? 'u' : '';
	}
	
	// FORMATTING _____________________________________________________________
	
	function _anything( $value ) {
		return true;
	}
	
	function _name( $value ) {
		return $this->matches( '^([[:alpha:]]{2,}((( )|(\.)|(\-)|(\')|(\. ))?[[:alpha:]]+\.?)*){0,1}$', $value );
	}
	
	function _word( $value ) {
		return $this->matches( '^[[:word:]]*$', $value );
	}	
	
	function _alphanumeric( $value ) {
		return $this->matches( '^[[:alnum:]]*$', $value );
	}
	
	function _alpha( $value ) {
		return $this->matches( '^[[:alpha:]]*$', $value );
	}
	
	function _ascii( $value ) {
		return $this->matches( '^[[:ascii:]]*$', $value );
	}
	
	function _numeric( $value ) {
		return empty( $value ) || is_numeric( $value );
	}
	
	function _integer( $value ) {
		return empty( $value ) || is_integer( $value );
	}
	
	function _price( $value ) {
		if ( empty( $value ) ) { return true; }
		$separators = $this->decimalPlacesSeparatorAsString();
		return $this->matches( '^[[:digit:]]*[' . $separators . ']{0,1}[][[:digit:]]{1,2}$', $value );
	}
	
	function _tax( $value ) {
		if ( empty( $value ) ) { return true; }
		$separators = $this->decimalPlacesSeparatorAsString();
		return $this->matches( '^[[:digit:]]*[' . $separators . ']{0,1}[][[:digit:]]{1,3}$', $value );
	}
	
	function _date( $value ) {
		return $this->checkDateTime( $this->dateFormat, $value );
	}
	
	function _time( $value ) {
		//if ( empty( $value ) ) { return true; }
		//return $this->matches( '^([01]?[0-9]|2[0-3]):[0-5][0-9]$', $value );
		return $this->checkDateTime( $this->timeFormat, $value );
	}
	
	function _longtime( $value ) {
		//if ( empty( $value ) ) { return true; }
		//return $this->matches( '^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$', $value );
		return $this->checkDateTime( $this->longTimeFormat, $value );
	}
	
	function _datetime( $value ) {
		return $this->checkDateTime( $this->dateTimeFormat, $value );
	}
	
	function _longdatetime( $value ) {
		return $this->checkDateTime( $this->longDateTimeFormat, $value );
	}	
	
	function _email( $value ) {
		if ( empty( $value ) ) { return true; }
		return $this->matches( '^[[:alpha:]]\w*([-+.\']\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$', $value );
	}
	
	function _http( $value ) {
		if ( empty( $value ) ) { return true; }
		return $this->matches( '^((https?:\/\/)?\w+([-.]\w+)*\.\w+([-.]\w+)*)$', $value );
	}
	
	function _url( $value ) {
		if ( empty( $value ) ) { return true; }
		return $this->matches( '^(\w+(:\/\/)?\w+([-.]\w+)*\.\w+([-.]\w+)*)$', $value );
	}
	
	function _ip( $value ) {
		return $this->_ipv4( $value ) || $this->_ipv6( $value );
	}
	
	function _ipv4( $value ) {
		if ( empty( $value ) ) { return true; }
		$regex = '^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$';
		return $this->matches( $regex, $value );
	}
	
	function _ipv6( $value ) {
		if ( empty( $value ) ) { return true; }
		return $this->matches( '^(?:[A-Fa-f0-9]{1,4}:){7}[A-Fa-f0-9]{1,4}$', $value );
	}
	
	// OTHER __________________________________________________________________
	
	protected function matches( $regex, $value ) {
		$u = $this->encodingRegExSymbol();		
		return 1 === preg_match( '/'. $regex . '/' . $u, $value );
	}
	
	private function checkDateTime( $format, $value ) {
		if ( empty( $value ) ) { return true; }		
		try {
			\DateTime::createFromFormat( $format, $value );
			return true;
		} catch ( \Exception $e ) {
			return false;
		}
	}	

}

?>