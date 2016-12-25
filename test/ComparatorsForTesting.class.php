<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;


/**
 * Comparator instance used to compare two different Person objects, used only for testing purpose.
 */
class PersonComparator implements Comparator {


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Comparator::compare()
	 */
	public function compare (Object $person1, Object $person2): int {

		/**
		 *    Assumes Person as parameters and returns the "inverse result" that
		 * $person1->compareTo ($person2)
		 */
		return strcmp ($person2->name, $person1->name);
	}

}


/**
 * Comparator instance used to compare two different Car objects, used only for testing purpose.
 */
class CarComparator implements Comparator {


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Comparator::compare()
	 */
	public function compare (Object $person1, Object $person2): int {

		/**
		 *    Assumes Car as parameters and returns the "inverse result" that
		 * $car1->compareTo ($car2)
		 */
		return strcmp ($car2->name, $car1->name);
	}

}

?>