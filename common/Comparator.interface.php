<?php

namespace FunctionalPHP\common;

use FunctionalPHP\common\Object;

/**
 *    A comparison function, which imposes a total ordering on some collection of objects. The
 * ordering imposed by a comparator c on a set of elements S is said to be consistent with equals
 * if and only if
 *
 *   c->compare (e1, e2) == 0
 *
 * has the same boolean value as e1->equals(e2) for every e1 and e2 in S.
 */
interface Comparator {


	/**
	 *    Compares its two arguments for order. Returns a negative integer, zero, or a positive
	 * integer as the first argument is less than, equal to, or greater than the second. It is
	 * generally the case, but not strictly required that
	 *
	 *   (compare (x, y) == 0) == (x->equals(y))
	 *
	 * @param Object $object1
	 *    The first object to be compared
	 * @param Object $object2
	 *    The second object to be compared
	 *
	 * @return a negative integer, zero, or a positive integer as the first argument is less than,
	 *         equal to, or greater than the second.
	 */
	public function compare (Object $object1, Object $object2) : int;

}

?>