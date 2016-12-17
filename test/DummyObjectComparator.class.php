<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;

/**
 * Class used in the tests related with DummyObject
 */
class DummyObjectComparator implements Comparator {


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Comparator::compare()
	 */
	public function compare (Object $object1, Object $object2): int {

		/**
		 *    Assumes DummyObjects as parameters and returns the "inverse result" than
		 * $object1->compareTo ($object2)
		 */
		return $object2->intProperty - $object1->intProperty;
	}

}

?>