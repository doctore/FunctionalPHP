<?php

namespace FunctionalPHP\common\functional;

use FunctionalPHP\common\functional\Predicate;
use FunctionalPHP\exception\UnsupportedOperationException;

/**
 * Returns a complex Predicate on which we can add several logical conditions like: AND, OR, etc
 */
final class CompositePredicate implements Predicate {

	/**
	 * Final predicate
	 */
	private $predicate;


	/**
	 * Composed predicate that represents a complex Predicate.
	 *
	 * @param Predicate $predicate
	 *    Predicate to check
	 */
	public function __construct (Predicate $predicate) {
		$this->predicate = $predicate;
	}


	/**
	 *    Returns a CompositePredicate that represents a short-circuiting logical AND of the
	 * "current predicate" and the given predicates.
	 *
	 * @example
	 *	 (new CompositePredicate (new TrueForPairsNumbersCustomPredicate()))
	 *                               ->and (new TrueForNumberGreaterThanTenCustomPredicate())->test(1, 5, 10, 15)
	 *
	 * @param Predicate ...$predicates
	 *    Predicates "joined" to the current one using short-circuiting logical AND operation
	 *
	 * @return CompositePredicate
	 *
	 * @throws UnsupportedOperationException if there are not predicates to join
	 */
	public function and (Predicate ...$predicates) : CompositePredicate {

		if (count ($predicates) == 0)
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
				                                    ,"The operation AND requires at least one predicate to join");

		return new CompositePredicate (new AndPredicate ($this->predicate, ...$predicates));
	}


	/**
	 * Returns a Predicate that represents the logical negation of the current one.
	 *
	 * @example
	 *   (new CompositePredicate (new IsNumericCustomPredicate()))->not()->test (12, "AA")
	 *
	 * @return CompositePredicate
	 */
	public function not() : CompositePredicate {

		return new CompositePredicate (new NotPredicate ($this->predicate));
	}


	/**
	 *    Returns a CompositePredicate that represents a short-circuiting logical OR of the
	 * "current predicate" and the given predicates.
	 *
	 * @example
	 *	 (new CompositePredicate (new TrueForPairsNumbersCustomPredicate()))
	 *                               ->or (new TrueForNumberGreaterThanTenPredicate())->test(1, 5, 10, 15)
	 *
	 * @param Predicate ...$predicates
	 *    Predicates "joined" to the current one using short-circuiting logical OR operation
	 *
	 * @return CompositePredicate
	 *
	 * @throws UnsupportedOperationException if there are not predicates to join
	 */
	public function or (Predicate ...$predicates) : CompositePredicate {

		if (count ($predicates) == 0)
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The operation OR requires at least one predicate to join");

		return new CompositePredicate (new OrPredicate ($this->predicate, ...$predicates));
	}


	/**
	 *    Returns a CompositePredicate that represents a logical XOR of the "current predicate" and
	 * the given predicates.
	 *
	 * @example
	 *	 (new CompositePredicate (new TrueForPairsNumbersCustomPredicate()))
	 *                               ->xor (new TrueForNumberGreaterThanTenPredicate())->test(1, 5, 10, 15)
	 *
	 * @param Predicate ...$predicates
	 *    Predicates "joined" to the current one using the logical XOR operation
	 *
	 * @return CompositePredicate
	 *
	 * @throws UnsupportedOperationException if there are not predicates to join
	 */
	public function xor (Predicate ...$predicates) : CompositePredicate {

		if (count ($predicates) == 0)
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The operation XOR requires at least one predicate to join");

		return new CompositePredicate (new XorPredicate ($this->predicate, ...$predicates));
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::__invoke()
	 */
	public function test (...$args): bool {

		return $this->predicate->test (...$args);
	}

}


/**
 *    Returns a composed Predicate that represents a short-circuiting logical AND of this predicate
 * and others. When evaluating the composed predicate, if this predicate is FALSE, then the other
 * predicates is not evaluated.
 */
final class AndPredicate implements Predicate {

	/**
	 * Array of predicates "joined" by AND condition
	 */
	private $predicates;


	/**
	 * Composed predicate that represents a short-circuiting logical AND of this predicate and others.
	 *
	 * @param Predicate $predicate1
	 *    First predicate to check
	 * @param Predicate $predicate2
	 *    Second predicate to check
	 * @param Predicate[] ...$nextPredicates
	 *    Next predicates to check (using short-circuiting logical AND too)
	 */
	public function __construct (Predicate $predicate1, Predicate $predicate2, Predicate ...$nextPredicates) {
		$this->predicates = array_merge ([$predicate1, $predicate2], $nextPredicates);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args): bool {

		foreach ($this->predicates as $predicate) {

			if ($predicate->test (...$args) === FALSE)
				return FALSE;
		}
		return TRUE;
	}

}


/**
 *    Returns a composed Predicate that represents a short-circuiting logical OR of this predicate
 * and others. When evaluating the composed predicate, if this predicate is TRUE, then the other
 * predicates is not evaluated.
 */
final class OrPredicate implements Predicate {

	/**
	 * Array of predicates "joined" by OR condition
	 */
	private $predicates;


	/**
	 * Composed predicate that represents a short-circuiting logical OR of this predicate and others.
	 *
	 * @param Predicate $predicate1
	 *    First predicate to check
	 * @param Predicate $predicate2
	 *    Second predicate to check
	 * @param Predicate[] ...$nextPredicates
	 *    Next predicates to check (using short-circuiting logical OR too)
	 */
	public function __construct (Predicate $predicate1, Predicate $predicate2, Predicate ...$nextPredicates) {
		$this->predicates = array_merge ([$predicate1, $predicate2], $nextPredicates);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args): bool {

		foreach ($this->predicates as $predicate) {

			if ($predicate->test (...$args) === TRUE)
				return TRUE;
		}
		return FALSE;
	}

}


/**
 * Returns a Predicate that represents the logical negation of the given one.
 */
final class NotPredicate implements Predicate {

	/**
	 * Array of predicates "joined" by OR condition
	 */
	private $predicate;


	/**
	 * Composed predicate that represents the logical negation of the given predicate.
	 *
	 * @param Predicate $predicate
	 *    Predicate to negate
	 */
	public function __construct (Predicate $predicate) {
		$this->predicate = $predicate;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args): bool {

		return !$this->predicate->test (...$args);
	}

}


/**
 * Returns a composed Predicate that represents a logical XOR of this predicate and others.
 */
final class XorPredicate implements Predicate {

	/**
	 * Array of predicates "joined" by XOR condition
	 */
	private $predicates;


	/**
	 * Composed predicate that represents a logical XOR of this predicate and others.
	 *
	 * @param Predicate $predicate1
	 *    First predicate to check
	 * @param Predicate $predicate2
	 *    Second predicate to check
	 * @param Predicate[] ...$nextPredicates
	 *    Next predicates to check (using short-circuiting logical OR too)
	 */
	public function __construct (Predicate $predicate1, Predicate $predicate2, Predicate ...$nextPredicates) {
		$this->predicates = array_merge ([$predicate1, $predicate2], $nextPredicates);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args): bool {

		$result = intval ($this->predicates[0]->test (...$args));

		for ($i = 1, $j = count ($this->predicates); $i < $j; ++$i)
			$result = $result ^ intval ($this->predicates[$i]->test (...$args));

		return boolval ($result);
	}

}

?>