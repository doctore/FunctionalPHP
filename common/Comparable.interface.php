<?php

namespace FunctionalPHP\common;

use FunctionalPHP\common\Object;

/**
 *    This interface imposes a total ordering on the objects of each class that implements it.
 * The natural ordering for a class C is said to be consistent with equals if and only if
 *
 *   e1->compareTo (e2) == 0
 *
 * has the same boolean value as e1.equals(e2) for every e1 and e2 of class C.
 */
interface Comparable {


	/**
	 *    Compares this object with the specified object for order. Returns a negative integer,
	 * zero, or a positive integer as this object is less than, equal to, or greater than the
	 * specified object.
	 *
	 * @param Object $objeto
	 *    The object to be compared
	 *
	 * @return a negative integer, zero, or a positive integer as this object is less than,
	 *         equal to, or greater than the specified object.
	 */
	public function compareTo (Object $object) : int;

}

?>