<?php
namespace phputil;

/**
 *  Default formats.
 *  
 *  @author	Thiago Delgado Pinto
 */
class Format {
	
	const ANYTHING			= 'anything';
	const STRING			= 'string';				// same as "anything"
	const NAME				= 'name';				// alpha, space, dot, dash
	const WORD				= 'word';				// alpha, underline
	const ALPHA_NUMERIC		= 'alphanumeric';		// alpha, number
	const ALPHA				= 'alpha';				// just alpha
	const ASCII				= 'ascii';				// character codes 0-127
	
	const NUMERIC			= 'numeric';			// number
	const INTEGER			= 'integer';
	const NATURAL			= 'natural';			// integer >= 0	
	const DOUBLE			= 'double';
	const FLOAT				= 'float';				// same as "double"
	
	const MONETARY			= 'monetary';			// numeric(N,2)
	const PRICE				= 'price';				// same as "monetary"
	const TAX				= 'tax';				// numeric(N,3)
	
	// DATE
	
	const DATE_YMD			= 'date_ymd';	// yyyy/dd/mm
	const DATE_MDY			= 'date_mdy';	// mm/dd/yyyy
	const DATE_DMY			= 'date_dmy';	// dd/mm/yyyy
	const DATE				= 'date';		// same as DATE_DMY: the most popular, according to https://en.wikipedia.org/wiki/Date_format_by_country)
	
	// TIME
	
	const TIME				= 'time';		// hh:mm, from 00:00 to 23:59
	const LONG_TIME			= 'longtime';	// hh:mm:ss, from 00:00:00 to 23:59:59
	
	// DATETIME
	
	const DATETIME_YMD		= 'datetime_ymd';	// yyyy/dd/mm hh:mm
	const DATETIME_MDY		= 'datetime_mdy';	// mm/dd/yyyy hh:mm
	const DATETIME_DMY		= 'datetime_dmy';	// dd/mm/yyyy hh:mm
	const DATETIME			= 'datetime';		// same as DATETIME_DMY
	
	// LONGDATETIME
	
	const LONGDATETIME_YMD	= 'longdatetime_ymd';	// yyyy/dd/mm hh:mm
	const LONGDATETIME_MDY	= 'longdatetime_mdy';	// mm/dd/yyyy hh:mm
	const LONGDATETIME_DMY	= 'longdatetime_dmy';	// dd/mm/yyyy hh:mm
	const LONGDATETIME		= 'longdatetime';		// same as LONG_DATETIME_DMY
	
	// WEB
	
	const EMAIL				= 'email';
	const HTTP				= 'http';				// http://... or https://...
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
	private $dateSeparator;
	
	function __construct( $encoding = null ) {
		$this->originalMethods = FormatChecker::originalMethods( $this );
		$this->methods = array();
		$this->encoding = isset( $encoding ) ? $encoding : Encoding::DEFAULT_ENCODING;
		$this->decimalPlacesSeparator = '.';
		$this->dateSeparator = '/';
	}
	
	// METHOD HANDLING
	
	function set( $name, $callback ) {
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
	
	function dateSeparator( $separator = null ) { // getter/setter
		if ( isset( $separator ) ) {
			$this->dateSeparator = $separator;
		}
		return $this->dateSeparator;
	}
	
	function decimalPlacesSeparatorAsString() {
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
	
	// FORMATTING
	
	function _anything( $value ) {
		return true;
	}
	
	function _string( $value ) {
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
	
	function _natural( $value ) {
		return empty( $value ) || ( is_integer( $value ) && $value >= 0 );
	}	
	
	function _double( $value ) {
		return empty( $value ) || is_double( $value );
	}
	
	function _float( $value ) {
		return empty( $value ) || is_float( $value );
	}
	
	function _monetary( $value ) {
		if ( empty( $value ) ) { return true; }
		$separators = $this->decimalPlacesSeparatorAsString();
		return $this->matches( '^[[:digit:]]*[' . $separators . ']{0,1}[][[:digit:]]{1,2}$', $value );		
	}
	
	function _price( $value ) {
		return $this->_monetary( $value );
	}
	
	function _tax( $value ) {
		if ( empty( $value ) ) { return true; }
		$separators = $this->decimalPlacesSeparatorAsString();
		return $this->matches( '^[[:digit:]]*[' . $separators . ']{0,1}[][[:digit:]]{1,3}$', $value );
	}
	
	// DATE
	
	function _date_ymd( $value, $separator = null ) {
		if ( empty( $value ) ) { return true; }
		return $this->matchesDateRegEx( array( 'y', 'm', 'd' ), $separator, $value );
	}
	
	function _date_mdy( $value, $separator = null ) {
		if ( empty( $value ) ) { return true; }
		return $this->matchesDateRegEx( array( 'm', 'd', 'y' ), $separator, $value );
	}
	
	function _date_dmy( $value, $separator = null ) {
		if ( empty( $value ) ) { return true; }
		return $this->matchesDateRegEx( array( 'd', 'm', 'y' ), $separator, $value );
	}
	
	function _date( $value, $separator = null ) {
		return $this->_date_dmy( $value, $separator );
	}
	
	// TIME
	
	function _time( $value ) {
		if ( empty( $value ) ) { return true; }
		return $this->matches( '^([01]?[0-9]|2[0-3]):[0-5][0-9]$', $value );
	}
	
	function _longtime( $value ) {
		if ( empty( $value ) ) { return true; }
		return $this->matches( '^([01]?[0-9]|2[0-3]):[0-5][0-9]:[0-5][0-9]$', $value );
	}
	
	// DATETIME
	
	function _datetime_ymd( $value, $separator = null ) {
		return $this->checkDatetime( $value, $separator, array( 'y', 'm', 'd' ) );
	}
	
	function _datetime_mdy( $value, $separator = null ) {
		return $this->checkDatetime( $value, $separator, array( 'm', 'd', 'y' ) );
	}
	
	function _datetime_dmy( $value, $separator = null ) {
		return $this->checkDatetime( $value, $separator, array( 'd', 'm', 'y' ) );
	}
	
	function _datetime( $value, $separator = null ) {
		return $this->_datetime_dmy( $value, $separator );
	}
	
	private function checkDatetime( $value, $separator, $order, $longTime = false ) {
		if ( empty( $value ) ) { return true; }
		$pieces = explode( ' ', $value );
		if ( count( $pieces ) !== 2 ) { return false; }
		$date = $pieces[ 0 ];
		$time = $pieces[ 1 ];
		return $this->matchesDateRegEx( $order, $separator, $date )
			&& ( true === $longTime ? $this->_longtime( $time ) : $this->_time( $time ) );
	}
	
	// LONGDATETIME

	function _longdatetime_ymd( $value, $separator = null ) {
		return $this->checkDatetime( $value, $separator, array( 'y', 'm', 'd' ), true );
	}
	
	function _longdatetime_mdy( $value, $separator = null ) {
		return $this->checkDatetime( $value, $separator, array( 'm', 'd', 'y' ), true );
	}
	
	function _longdatetime_dmy( $value, $separator = null ) {
		return $this->checkDatetime( $value, $separator, array( 'd', 'm', 'y' ), true );
	}
	
	function _longdatetime( $value, $separator = null ) {
		return $this->_longdatetime_dmy( $value, $separator );
	}
	
	// WEB
	
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
	
	// INTERNAL
	
	protected function matches( $regex, $value ) {
		$u = $this->encodingRegExSymbol();		
		return 1 === preg_match( '/'. $regex . '/' . $u, $value );
	}
	
	private function dateRegEx( array $order, $separator ) {
		$expressions = array(
			'y' => '[0-9]{1,4}',
			'm' => '(0?[1-9]|1[012])',
			'd' => '(0?[1-9]|[12][0-9]|3[01])'
			);
		$pieces = array();
		foreach ( $order as $o ) {
			if ( isset( $expressions[ $o ] ) ) {
				$pieces []= $expressions[ $o ];
			}
		}
		return '^'. implode( $separator, $pieces ) .'$';
	}
	
	private function matchesDateRegEx( array $order, $separator, $value ) {
		$sep = null === $separator ? $this->dateSeparator : $separator;
		$regex = $this->dateRegEx( $order, "\\" . $sep );
		if ( ! $this->matches( $regex, $value ) ) {
			return false;
		}
		$pieces = explode( $sep, $value );
		$date = array();
		foreach ( $order as $k => $v ) {
			$date[ $v ] = $pieces[ $k ];
		}
		return checkdate( $date[ 'm' ], $date[ 'd' ], $date[ 'y' ] );
	}

}

?>