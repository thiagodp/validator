<?php
namespace phputil\validator;

/**
 *  Default formats.
 *  
 *  @author	Thiago Delgado Pinto
 */
class Format {
	
	const ANYTHING			= 'anything';
	const STRING			= 'string';				// same as "anything"
	const NAME				= 'name';				// alpha, space, dot, dash, single comma, with at least two characters
													
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

?>