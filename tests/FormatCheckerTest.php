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
	
	
	function date_expectations( array $order, $separator ) {
		
		$tests = array(
			// FAILS ------------------------------------------------------------------------------
			array( 'value' => array( 'y' => 'a', 'm' => '1', 'd' => '1' ), 'expect' => false ),
			array( 'value' => array( 'y' => '1', 'm' => 'a', 'd' => '1' ), 'expect' => false ),
			array( 'value' => array( 'y' => '1234', 'm' => '13', 'd' => '1' ), 'expect' => false ),
			array( 'value' => array( 'y' => '1234', 'm' => '12', 'd' => '32' ), 'expect' => false ),
			array( 'value' => array( 'y' => '1234', 'm' => '02', 'd' => '31' ), 'expect' => false ),
			// PASS -------------------------------------------------------------------------------
			array( 'value' => array( 'y' => '1', 'm' => '1', 'd' => '1' ), 'expect' => true ),
			array( 'value' => array( 'y' => '12', 'm' => '1', 'd' => '1' ), 'expect' => true ),
			array( 'value' => array( 'y' => '123', 'm' => '1', 'd' => '1' ), 'expect' => true ),			
			array( 'value' => array( 'y' => '1234', 'm' => '1', 'd' => '1' ), 'expect' => true ),
			array( 'value' => array( 'y' => '1234', 'm' => '01', 'd' => '01' ), 'expect' => true ),
			array( 'value' => array( 'y' => '1234', 'm' => '02', 'd' => '28' ), 'expect' => true ),
			array( 'value' => array( 'y' => '1234', 'm' => '12', 'd' => '31' ), 'expect' => true ),
		);
		
		$expectations = array();
		
		foreach ( $tests as $test ) {
			$values = array();
			foreach ( $order as $o ) {
				$values []= $test[ 'value' ][ $o ];
			}
			$v = implode( $separator, $values );
			$expectations[ $v ] = $test[ 'expect' ];
		}
		
		return $expectations;
	}
	
	
	function test_date_ymd() {
		$expectations = $this->date_expectations( array( 'y', 'm', 'd' ),  '/' );
		$this->verify_characters( $expectations, '_date_ymd' );
	}
	
	function test_date_mdy() {
		$expectations = $this->date_expectations( array( 'm', 'd', 'y' ),  '/' );
		$this->verify_characters( $expectations, '_date_mdy' );
	}
	
	function test_date_dmy() {
		$expectations = $this->date_expectations( array( 'd', 'm', 'y' ),  '/' );
		$this->verify_characters( $expectations, '_date_dmy' );
	}
	
	
	function test_date_ymd_dotted() {
		$expectations = $this->date_expectations( array( 'y', 'm', 'd' ),  '.' );
		$this->verify_characters( $expectations, '_date_ymd_dotted' );
	}
	
	function test_date_mdy_dotted() {
		$expectations = $this->date_expectations( array( 'm', 'd', 'y' ),  '.' );
		$this->verify_characters( $expectations, '_date_mdy_dotted' );
	}
	
	function test_date_dmy_dotted() {
		$expectations = $this->date_expectations( array( 'd', 'm', 'y' ),  '.' );
		$this->verify_characters( $expectations, '_date_dmy_dotted' );
	}
	
	
	function test_date_ymd_dashed() {
		$expectations = $this->date_expectations( array( 'y', 'm', 'd' ),  '-' );
		$this->verify_characters( $expectations, '_date_ymd_dashed' );
	}
	
	function test_date_mdy_dashed() {
		$expectations = $this->date_expectations( array( 'm', 'd', 'y' ),  '-' );
		$this->verify_characters( $expectations, '_date_mdy_dashed' );
	}
	
	function test_date_dmy_dashed() {
		$expectations = $this->date_expectations( array( 'd', 'm', 'y' ),  '-' );
		$this->verify_characters( $expectations, '_date_dmy_dashed' );
	}
	
	function test_date() {
		$expectations = $this->date_expectations( array( 'y', 'm', 'd' ),  '-' );
		
		// PHP' DateTime ACCEPTS the following formats :( -----------------------------------------
		unset( $expectations[ 'a-1-1' ] );
		unset( $expectations[ '1-a-1' ] );
		unset( $expectations[ '1-1-1' ] );
		unset( $expectations[ '1234-13-1' ] );
		unset( $expectations[ '1234-12-32' ] );
		unset( $expectations[ '1234-02-31' ] );
		// ----------------------------------------------------------------------------------------
		
		$this->verify_characters( $expectations, '_date' );
	}
	
	// function test_time()
	// function test_longtime()
	// function test_datetime()
	// function test_longdatetime()

	function test_email() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			'a.c'				=> false,
			'a@.c'				=> false,
			'@b.c'				=> false,
			'5@b.c'				=> false,
			// PASS -----------------------------------------------------------
			'a@b.c'				=> true,
			'a@b.c.d'			=> true,
			'a1@b2.c3.d4'		=> true,
		);
		$this->verify_characters( $expectations, '_email' );
	}	

	
	function test_http() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			'.com'				=> false,
			'ftp://sth.com'		=> false,
			'any://sth.com'		=> false,
			// PASS -----------------------------------------------------------
			'ab.c'				=> true,
			'sth.com'			=> true,
			'www.sth.com'		=> true,
			'http://sth.com'	=> true,
			'https://sth.com'	=> true,
			'https://1.1.1.1'	=> true,
		);
		$this->verify_characters( $expectations, '_http' );		
	}
	

	function test_url() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			'.com'				=> false,
			'a.b'				=> false,
			// PASS -----------------------------------------------------------
			'ab.c'				=> true,
			'sth.com'			=> true,
			'www.sth.com'		=> true,
			'ftp://sth.com'		=> true,
			'https://sth.com'	=> true,
			'any://sth.com'		=> true,
			'any://1.1.1.1'		=> true,
		);
		$this->verify_characters( $expectations, '_url' );		
	}

	
	function test_ip() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			'256.0.0.0'									=> false,
			'0.256.0.0'									=> false,
			'0.0.256.0'									=> false,
			'0.0.0.256'									=> false,
			// PASS -----------------------------------------------------------
			'0.0.0.0'									=> true,
			'255.255.255.255'							=> true,
			'0:0:0:0:0:0:0:0'							=> true,
			'FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF'	=> true,
			'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff'	=> true,			
		);
		$this->verify_characters( $expectations, '_ip' );		
	}
	
	
	function test_ipv4() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			'256.0.0.0'			=> false,
			'0.256.0.0'			=> false,
			'0.0.256.0'			=> false,
			'0.0.0.256'			=> false,
			// PASS -----------------------------------------------------------
			'0.0.0.0'			=> true,
			'255.255.255.255'	=> true
		);
		$this->verify_characters( $expectations, '_ipv4' );
	}
	
	
	function test_ipv6() {
		$expectations = array(
			// FAILS ----------------------------------------------------------
			'0.0.0.0'									=> false,
			'255.255.255.255'							=> false,
			// PASS -----------------------------------------------------------
			'0:0:0:0:0:0:0:0'							=> true,
			'FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF:FFFF'	=> true,
			'ffff:ffff:ffff:ffff:ffff:ffff:ffff:ffff'	=> true,
		);
		$this->verify_characters( $expectations, '_ipv6' );
	}	
}

?>