<?php
namespace phputil\validator;

/**
 *  Default rules.
 *  
 *  @author	Thiago Delgado Pinto
 */
class Rule {
	
	const REQUIRED			= 'required';
	
	const MIN_LENGTH		= 'min_length';
	const MAX_LENGTH		= 'max_length';
	const LENGTH_RANGE		= 'length_range';
	
	const MIN_VALUE			= 'min_value';
	const MAX_VALUE			= 'max_value';
	const VALUE_RANGE		= 'value_range';
	
	const MIN_COUNT			= 'min_count';
	const MAX_COUNT			= 'max_count';
	const COUNT_RANGE		= 'count_range';
	
	const IN				= 'in';
	const NOT_IN			= 'not_in';
	
	const START_WITH		= 'start_with';
	const NOT_START_WITH	= 'not_start_with';	
	const END_WITH			= 'end_with';
	const NOT_END_WITH		= 'not_end_with';
	const CONTAINS			= 'contains';
	
	const REGEX				= 'regex';
	const FORMAT			= 'format';
	
	const WITH				= 'with';
	
    static function all() {
        return array_values( ( new \ReflectionClass( __CLASS__ ) )->getConstants() );
    }
}

?>