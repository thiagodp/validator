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
			' '							=> false,
			'.'							=> false,			
			'$#!@'						=> false,
			"\r\n"						=> false,	// Win EOL
			"\r"						=> false,	// Mac EOL
			'A '						=> false,
			'A. '						=> false,
			// PASS -----------------------------------------------------------
			"\n"						=> true,	// Unix EOL			
			''							=> true,
			'_'							=> true,
			'AÁÀÄaáàä'					=> true,			
			'019'						=> true,
			'A_'						=> true,
			'_A_'						=> true
			);
		
		$this->verify_characters( $expectations, '_word' );
	}
		
}

?>