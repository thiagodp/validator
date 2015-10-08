<?php
namespace phputil\tests;

require_once 'lib/FormatChecker.php'; // phpunit will be executed from the project root

use PHPUnit_Framework_TestCase;
use phputil\FormatChecker;

/**
 * Tests FormatChecker.
 *
 * @author	Thiago Delgado Pinto
 */
class FormatCheckerTest extends PHPUnit_Framework_TestCase {
	
	private $fc = null;
	
	function setUp() {
		$this->fc = new FormatChecker();
	}
	
	function verify_characters( array $expectations, $method ) {
		foreach ( $expectations as $characters => $expected ) {
			$result = call_user_func( array( $this->fc, $method ), $characters );
			$msg = 'Rule "' . $method . '" does not match with "' .
				str_replace( array( "\n", "\r" ), array( "\\n", "\\r" ), $characters ) . '"';
			$this->assertEquals( $expected, $result, $msg );
		}
	}

	
	function test_anything() {
		$expectations = array(
			''						=> true,
			' '						=> true,
			". -,A$*@  45!\n\t\r'"	=> true
			);
		
		$this->verify_characters( $expectations, '_anything' );
	}
	
	
	function test_name() {
		$expectations = array(	
			// FAILS ----------------------------------------------------------
			' '										=> false,	// space alone
			'.'										=> false,	// dot alone
			'_'										=> false,	// underline alone			
			'019'									=> false,	// numbers
			'$#!@'									=> false,	// symbols
			"\r\n"									=> false,	// Win EOL
			"\r"									=> false,	// Mac EOL
			'John '									=> false,	// space at end
			'John-'									=> false,	// dash at end
			'John\''								=> false,	// plic at end
			'Jhon 5 Zac'							=> false,	// not accept numbers
			'john@site.com'							=> false,	// not accept symbols
			// PASS -----------------------------------------------------------
			"\n"									=> true,	// Unix EOL			
			''										=> true,
			'AÁÀÄaáàä'								=> true,			
			'Bob'									=> true,
			'Bob Marley'							=> true,
			'Ziggy M.'								=> true,
			'John D\'Angelo Tora-bora A. Bravo C.'	=> true
			);
		
		$this->verify_characters( $expectations, '_name' );
	}
	
	
	function test_word() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			' '			=> false,
			'.'			=> false,			
			'$#!@'		=> false,
			"\r\n"		=> false,	// Win EOL
			"\r"		=> false,	// Mac EOL
			'A '		=> false,
			'A. '		=> false,
			// PASS -----------------------------------------------------------
			"\n"		=> true,	// Unix EOL			
			''			=> true,
			'_'			=> true,
			'AÁÀÄaáàä'	=> true,			
			'019'		=> true,
			'A_'		=> true,
			'_A_'		=> true
			);
		
		$this->verify_characters( $expectations, '_word' );
	}
	
	// function test_alphanumeric() {}
	// function test_alpha() {}
	// function test_ascii() {}
	
	function test_numeric() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			' '			=> false,
			'.'			=> false,			
			'$#!@'		=> false,
			"\n"		=> false,	// Unix EOL
			"\r\n"		=> false,	// Win EOL
			"\r"		=> false,	// Mac EOL
			'A'			=> false,
			// PASS -----------------------------------------------------------
			''			=> true,
			'0'			=> true,
			'.0'		=> true,	// decimal separator
			'-.0'		=> true,	// signed negative + decimal separator
			'+.0'		=> true,	// signed positive + decimal separator
			'+1'		=> true,	// signed positive	
			'-1'		=> true,	// signed negative
			'+1.0'		=> true,	// signed positive + decimal separator
			'-1.0'		=> true,	// signed negative + decimal separator
			'1e5'		=> true,	// power notation
			'-1e5'		=> true,	// power notation
			'1e-5'		=> true,	// power notation
			'-1e-5'		=> true		// power notation
			);
		
		$this->verify_characters( $expectations, '_numeric' );
	}
	
	
	function test_integer() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			' '			=> false,
			'.'			=> false,			
			'$#!@'		=> false,
			"\n"		=> false,	// Unix EOL
			"\r\n"		=> false,	// Win EOL
			"\r"		=> false,	// Mac EOL
			'A'			=> false,
			'.0'		=> false,	// decimal separator
			'-.0'		=> false,	// signed negative + decimal separator
			'+.0'		=> false,	// signed positive + decimal separator			
			'+1.0'		=> false,	// signed positive + decimal separator
			'-1.0'		=> false,	// signed negative + decimal separator			
			'+1'		=> false,	// signed positive
			'1e5'		=> false,	// power notation
			'-1e5'		=> false,	// power notation
			'1e-5'		=> false,	// power notation
			'-1e-5'		=> false,	// power notation			
			// PASS -----------------------------------------------------------
			''			=> true,
			'0'			=> true,
			'-1'		=> true		// signed negative
			);
		
		$this->verify_characters( $expectations, '_integer' );
	}
	
	
	function test_price() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			' '			=> false,
			'.'			=> false,			
			'$#!@'		=> false,
			"\n"		=> false,	// Unix EOL
			"\r\n"		=> false,	// Win EOL
			"\r"		=> false,	// Mac EOL
			'A'			=> false,
			'1e5'		=> false,	// power notation
			'-1e5'		=> false,	// power notation
			'1e-5'		=> false,	// power notation
			'-1e-5'		=> false,	// power notation
			'-.0'		=> false,	// signed negative + decimal separator
			'+.0'		=> false,	// signed positive + decimal separator
			'+1'		=> false,	// signed positive	
			'-1'		=> false,	// signed negative
			'+1.0'		=> false,	// signed positive + decimal separator
			'-1.0'		=> false,	// signed negative + decimal separator
			'1.000'		=> false,	// 3 decimal places
			// PASS -----------------------------------------------------------
			''			=> true,
			'0'			=> true,
			'.0'		=> true,	// decimal separator
			'1.0'		=> true,	// 1 decimal place
			'1.00'		=> true,	// 2 decimal places
			);
		
		$this->verify_characters( $expectations, '_price' );
	}


	function test_tax() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			' '			=> false,
			'.'			=> false,			
			'$#!@'		=> false,
			"\n"		=> false,	// Unix EOL
			"\r\n"		=> false,	// Win EOL
			"\r"		=> false,	// Mac EOL
			'A'			=> false,
			'1e5'		=> false,	// power notation
			'-1e5'		=> false,	// power notation
			'1e-5'		=> false,	// power notation
			'-1e-5'		=> false,	// power notation
			'-.0'		=> false,	// signed negative + decimal separator
			'+.0'		=> false,	// signed positive + decimal separator
			'+1'		=> false,	// signed positive	
			'-1'		=> false,	// signed negative
			'+1.0'		=> false,	// signed positive + decimal separator
			'-1.0'		=> false,	// signed negative + decimal separator
			'1.0000'	=> false,	// 4 decimal places
			// PASS -----------------------------------------------------------
			''			=> true,
			'0'			=> true,
			'.0'		=> true,	// decimal separator
			'1.0'		=> true,	// 1 decimal place
			'1.00'		=> true,	// 2 decimal places
			'1.000'		=> true,	// 3 decimal places
			);
		
		$this->verify_characters( $expectations, '_tax' );
	}
}

?>