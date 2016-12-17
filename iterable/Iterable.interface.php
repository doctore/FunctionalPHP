<?php

namespace FunctionalPHP\collection;

/**
 * Implementing this interface allows an object to be the target of the "for-each loop" statement.
 */
interface Iterable {

	/**
	 * Removes all of the elements from this object (this will be empty after this method returns).
	 *
	 * @throws UnsupportedOperationException if this operation is not supported by this object
	 */
	public function clear();

	/**
	 * Returns TRUE if this object contains no elements.
	 *
	 * @return TRUE if this object contains no elements
	 */
	public function isEmpty() : bool;

	/**
	 * Returns the hash code value for this object
	 *
	 * @return the hash code value for this object
	 */
	public function hashCode() : int;

	/**
	 * Returns an iterator over elements of this object
	 *
	 * @return an Iterator
	 */
	public function iterator() : \Iterator;

	/**
	 * Returns the number of elements in this object.
	 *
	 * @return the number of elements in this object
	 */
	public function size() : int;

}

?>