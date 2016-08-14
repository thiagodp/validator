<?php
namespace phputil;

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
	
	const MIN_VALUE		= 'min_value';
	const MAX_VALUE		= 'max_value';
	const VALUE_RANGE	= 'value_range';
	
	const MIN_COUNT		= 'min_count';
	const MAX_COUNT		= 'max_count';
	const COUNT_RANGE	= 'count_range';
	
	const IN			= 'in';
	const NOT_IN		= 'not_in';
	
	const REGEX			= 'regex';
	const FORMAT		= 'format';
	
	const WITH			= 'with';
	
    static function all() {
        return array_values( ( new \ReflectionClass( __CLASS__ ) )->getConstants() );
    }
}

?>