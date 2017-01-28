<?php

namespace FunctionalPHP\iterable\collection\lists;

use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\common\Object;

/**
 *    An ordered Collection (also known as a sequence). The user of this interface has precise
 * control over where in the Lists each element is inserted. The user can access elements by their
 * integer index (position in the list), and search for elements in the list.
 *
 *    Unlike sets, lists typically allow duplicate elements. More formally, lists typically allow
 * pairs of elements e1 and e2 such that
 *
 *   e1->equals(e2)
 */
interface Lists extends Collection {

	/**
	 *    Inserts the specified element at the specified position in this Lists. Shifts the element
	 * currently at that position (if any) and any subsequent elements to the right (adds one to their
	 * indices).
	 *
	 * @param int $index
	 *    Index at which the specified element is to be inserted
	 * @param Object $element
	 *    Element to be inserted
	 *
	 * @return TRUE if this Lists changed as a result of the call, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if some property of the specified element prevents it from
	 *         being added to this Lists or if the index is out of range (index < 0 || index > size())
	 * @throws UnsupportedOperationException if this operation is not supported by this Lists
	 */
	public function addByIndex (int $index, Object $element) : bool;

	/**
	 * Returns an Object with the element at the specified position in this Lists.
	 *
	 * @param int $index
	 *    Index of the element to return
	 *
	 * @return the element at the specified position in this Lists
	 *
	 * @throws IllegalArgumentException if the index is out of range (index < 0 || index >= size())
	 */
	public function get (int $index) : Object;


	/**
	 *    Returns the index of the first occurrence of the specified element in this Lists, or -1 if
	 * this Lists does not contain the element. More formally, returns the lowest index i such that
	 *
	 *   $element->equals (get(i))
	 *
	 * or -1 if there is no such index.
	 *
	 * @param Object $element
	 *    Element to search for
	 *
	 * @return the index of the first occurrence of the specified element in this Lists, or -1 if this
	 *         Lists does not contain the element
	 */
	public function indexOf (Object $element) : int;


	/**
	 *    Returns the index of the last occurrence of the specified element in this Lists, or -1 if
	 * this Lists does not contain the element. More formally, returns the highest index i such that
	 *
	 *   $element->equals (get(i))
	 *
	 * or -1 if there is no such index.
	 *
	 * @param Object $element
	 *    Element to search for
	 *
	 * @return the index of the last occurrence of the specified element in this Lists, or -1 if this
	 *         Lists does not contain the element
	 */
	public function lastIndexOf (Object $element) : int;


	/**
	 *    Removes the element at the specified position in this Lists.  Shifts any subsequent elements
	 * to the left (subtracts one from their indices). Returns the element that was removed from the
	 * Lists.
	 *
	 * @param int index
	 *    The index of the element to be removed
	 *
	 * @return the element previously at the specified position
	 *
	 * @throws IllegalArgumentException if the index is out of range (index < 0 || index >= size())
	 * @throws UnsupportedOperationException if this operation is not supported by this Lists
	 */
	public function removeByIndex (int $index) : Object;


	/**
	 * Replaces the element at the specified position in this Lists with the specified element.
	 *
	 * @param int $index
	 *    Index of the element to replace
	 * @param Object $element
	 *    Element to be stored at the specified position
	 *
	 * @return the element previously at the specified position
	 *
	 * @throws IllegalArgumentException if the index is out of range (index < 0 || index >= size())
	 * @throws UnsupportedOperationException if this operation is not supported by this Lists
	 */
	public function set (int $index, Object $element) : Object;


	/**
	 *    Returns a view of the portion of this Lists between the specified $fromIndex, inclusive,
	 * and $toIndex, exclusive. If $fromIndex and $toIndex are equal, the returned Lists is empty.
	 *
	 * @param int $fromIndex
	 *    Low endpoint (inclusive) of the subList
	 * @param int $toIndex
	 *    High endpoint (exclusive) of the subList
	 *
	 * @return a view of the specified range within this Lists
	 *
	 * @throws IllegalArgumentException for an illegal endpoint index value
	 *         ($fromIndex < 0 || $toIndex > size() || $fromIndex > $toIndex)
	 */
	public function subList (int $fromIndex, int $toIndex) : Lists;

}

?>