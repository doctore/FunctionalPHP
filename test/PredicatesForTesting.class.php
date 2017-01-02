<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\functional\Predicate;
use FunctionalPHP\exception\IllegalArgumentException;
use FunctionalPHP\test\Person;


/**
 * Uses to test if the given arguments are integer values (used only for testing purpose).
 */
final class IsIntPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) == 0)
			return FALSE;

		foreach ($args as $a) {

			if (is_int ($a) === FALSE)
				return FALSE;
		}
		return TRUE;
	}
}


/**
 * Uses to test if the given arguments are integer values (used only for testing purpose).
 */
final class IsIntAndPairPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) == 0)
			return FALSE;

			foreach ($args as $a) {

				if (is_int ($a) === FALSE || ($a % 2 != 0))
					return FALSE;
			}
			return TRUE;
	}
}


/**
 * Uses to test if the given arguments are float values (used only for testing purpose).
 */
final class IsFloatPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) == 0)
			return FALSE;

		foreach ($args as $a) {

			if (is_float ($a) === FALSE)
				return FALSE;
		}
		return TRUE;
	}
}


/**
 * Uses to test if the given arguments are numeric values (used only for testing purpose).
 */
final class IsNumericPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) == 0)
			return FALSE;

		foreach ($args as $a) {

			if (is_numeric ($a) === FALSE)
				return FALSE;
		}
		return TRUE;
	}
}


/**
 * Uses to test if the given integer is a prime number (used only for testing purpose).
 */
final class IsPrimePredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) == 0)
			return FALSE;

		foreach ($args as $number) {

			if ($number < 2)
				return FALSE;

			/**
			 * Checks the odd numbers. If any of them is a factor, then it returns false.
			 * The sqrt can be an aproximation, hence just for the sake of
			 * security, one rounds it to the next highest integer value.
			 */
			if ($number != 2) {

				if ($number % 2 == 0)
					return FALSE;

				$ceil = ceil (sqrt ($number));
				for ($i = 3; $i <= $ceil; $i = $i + 2) {
					if ($number % $i == 0)
						return FALSE;
				}
			}
		}
		return TRUE;
	}

}


/**
 * Uses to test if the name of the given Person has more than one word (used only for testing purpose).
 */
final class HasPersonMoreThanOneWordAsNamePredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export ($args));
		$person = $args[0];
		if (!$person instanceof Person)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of ".Person::class
					                               ." Its type is: ".gettype ($person));

		return (str_word_count ($person->name) > 1);
	}
}


/**
 * Uses to test if the name of the given Person is not null and not empty (used only for testing purpose).
 */
final class HasPersonNameWithValuePredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export ($args));
		$person = $args[0];
		if (!$person instanceof Person)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of ".Person::class
					                               ." Its type is: ".gettype ($person));

		return (!is_null ($person->name) && !empty ($person->name));
	}
}


/**
 * Uses to test if the given Person has no age value (used only for testing purpose).
 */
final class HasPersonNoAgeValuePredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export ($args));

		$person = $args[0];
		if (!$person instanceof Person)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
						                       ,"The given parameter is not an instance of ".Person::class
						                           ." Its type is: ".gettype ($person));
		return (is_null ($person->age));
	}
}


/**
 * Uses to test if the age of the given Person is odd or not (used only for testing purpose).
 */
final class HasPersonOddAgePredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export ($args));
		$person = $args[0];
		if (!$person instanceof Person)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of ".Person::class
					                               ." Its type is: ".gettype ($person));

		return ($person->age % 2 != 0);
	}
}

?>