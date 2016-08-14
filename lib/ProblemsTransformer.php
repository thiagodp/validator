<?php
namespace phputil\validator;

/**
 *  Transforms results of a validation.
 *  
 *  @author	Thiago Delgado Pinto
 */
class ProblemsTransformer {
	
	/**
	 *  Returns the problems without the rules.
	 *  The messages stay in their arrays.
	 *  
	 *  @param array $problems	Array of problems.
	 *  @return array			Array with the rules stripped.
	 */
	function stripRules( array $problems ) {
		$rules = Rule::all();
		$strippedProblems = $problems;
		$this->stripRulesOf( $strippedProblems, $rules );
		return $strippedProblems;
	}
	
	/**
	 *  Returns only the messages. Repeated messages are removed.
	 *  
	 *  @param array $problems	Array of problems.
	 *  @return array			Array of messages.
	 */	
	function justTheMessages( array $problems ) {
		$messages = array();
		$this->messagesOf( $problems, $messages );
		return array_unique( $messages );
	}

	// PRIVATE
	
	private function stripRulesOf( array &$problems, array &$rules ) {
		foreach ( $rules as $rule ) {
			foreach ( $problems as $k => $v ) {
				if ( $k === $rule ) {
					$problems []= $v;					
					unset( $problems[ $k ] );
				} else if ( is_array( $v ) ) {
					$this->stripRulesOf( $problems[ $k ], $rules );
				}
			}
		}
	}
	
	private function messagesOf( array &$problems, array &$messages ) {
		foreach ( $problems as $v ) {
			if ( is_array( $v ) ) {
				$this->messagesOf( $v, $messages );
			} else {
				$messages []= $v;
			}
		}
	}
	
}

?>