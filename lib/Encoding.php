<?php
namespace phputil;

/**
 *  Default encoding.
 *  
 *  @author	Thiago Delgado Pinto
 */
class Encoding {
	const DEFAULT_ENCODING	= 'UTF-8';
	// Some useful encodings
	const UTF_8				= 'UTF-8';
	const ISO_8859_1		= 'ISO-8859-1';		// PHP's default, returned by mb_internal_encoding()
	const WIN_1251			= 'Windows-1251';
	const ASCII				= 'ASCII';
}

?>