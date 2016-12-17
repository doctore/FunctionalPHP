<?php

namespace FunctionalPHP\common\functional;

/**
 * Represents a predicate (boolean-valued function) of one argument.
 */
interface Predicate {

	/**
	 * Evaluates this predicate on the given arguments.
	 *
	 * @param ...$args
	 *    Params used to evaluate the desired condition
	 *
	 * @return TRUE if the input arguments matches the predicate, otherwise FALSE
	 */
	public function test (...$args) : bool;

}

?>