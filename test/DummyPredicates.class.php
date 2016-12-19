<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\functional\Predicate;
use FunctionalPHP\exception\IllegalArgumentException;
use FunctionalPHP\test\DummyObject;


/**
 * Uses to test if the given arguments are integer values
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
 * Uses to test if the given arguments are float values
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
 * Uses to test if the given arguments are numeric values
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
 * Uses to test if the given integer is a prime number
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
 * Uses to test if the intProperty of the given DummyObject is odd or not
 */
final class HasDummyObjectOddIntPropertyPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export($args));
		$dummyObject = $args[0];
		if ($dummyObject instanceof \DummyObject)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of ".DummyObject::class
					                               ." Its type is: ".gettype ($dummyObject));

		return ($dummyObject->intProperty % 2 != 0);
	}

}


/**
 * Uses to test if the stringProperty of the given DummyObject has two characters
 */
final class HasDummyObjectStringPropertyOfTwoCharactersPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export($args));
		$dummyObject = $args[0];
		if ($dummyObject instanceof \DummyObject)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of ".DummyObject::class
					                               ." Its type is: ".gettype ($dummyObject));

		return (strlen ($dummyObject->stringProperty)  == 2);
	}

}


/**
 * Uses to test if the intProperty of the given DummyObject is an integer value
 */
final class IsIntTheIntPropertyOfDummyObjectPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export ($args));
		$dummyObject = $args[0];
		if ($dummyObject instanceof \DummyObject)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of ".DummyObject::class
					                               ." Its type is: ".gettype ($dummyObject));

		return is_int ($dummyObject->intProperty);
	}

}


/**
 * Uses to test if the intProperty of the given DummyObject is a float value
 */
final class IsFloatTheIntPropertyOfDummyObjectPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                          ,"The method has received more than one argument: "
					                              .var_export ($args));
		$dummyObject = $args[0];
		if ($dummyObject instanceof \DummyObject)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of ".DummyObject::class
					                               ." Its type is: ".gettype ($dummyObject));

		return is_float ($dummyObject->intProperty);
	}

}

?>