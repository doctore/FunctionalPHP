<?php

namespace FunctionalPHP\collection;

use FunctionalPHP\collection\Iterable;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\functional\Stream;

/**
 *    The root interface in the collection hierarchy. A collection represents a group of objects,
 * known as its elements. Some collections allow duplicate elements and others do not. Some are
 * ordered and others unordered.
 */
interface Collection extends Iterable {

	/**
	 * Adds the given object to this collection and returns TRUE if this collection changed.
	 *
	 *    If a collection refuses to add a particular element for any reason other than that
	 * it already contains the element, it must throw an exception (rather than returning
	 * FALSE). This preserves the invariant that a collection always contains the specified
	 * element after this call returns.
	 *
	 * @param Object $element
	 *    Element whose presence in this collection is to be ensured
	 *
	 * @return TRUE if this collection changed as a result of the call, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if some property of the element prevents it from being
	 *         added to this collection
     * @throws UnsupportedOperationException if this operation is not supported by this collection
	 */
	public function add (Object $element) : bool;

	/**
	 * Adds all of the elements in the specified collection to this collection.
	 *
	 * @param Collection $collection
	 *    Collection containing elements to be added to this collection
	 *
	 * @return TRUE if this collection changed as a result of the call, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if some property of an element of the specified collection
	 *         prevents it from being added to this collection
     * @throws UnsupportedOperationException if this operation is not supported by this collection
	 */
	public function addAll (Collection $collection) : bool;

	/**
	 *    Returns TRUE if this collection contains the specified element. More formally, returns TRUE
	 * if and only if this collection contains at least one element element such that:
	 *
	 *   $element.equals ($elementInCollection)
	 *
	 * @param Object $element
	 *    Element whose presence in this collection is to be tested
	 *
	 * @return TRUE if this collection contains the specified element, FALSE otherwise
	 */
	public function contains (Object $element) : bool;

	/**
	 * Returns TRUE if this collection contains all of the elements in the specified collection.
	 *
	 * @param Collection $collection
	 *    Collection to be checked for containment in this collection
	 *
	 * @return true if this collection contains all of the elements in the specified collection,
	 *         false otherwise
	 */
	public function containsAll (Collection $collection) : bool;

	/**
	 * Compares the specified collection with this collection for equality.
	 *
	 * @param Collection $collection
	 *    Collection to be compared for equality with this collection
	 *
	 * @return TRUE if the specified object is equal to this collection, FALSE otherwise
	 */
	public function equals (Collection $collection) : bool;

	/**
	 *    Removes a single instance of the specified element from this collection, if it is present.
	 * More formally, removes an element such that:
	 *
	 *   $element.equals ($elementInCollection)
	 *
	 * @param Object $element
	 *    Element to be removed from this collection, if present
	 *
	 * @return TRUE if an element was removed as a result of this call, FALSE otherwise
	 *
	 * @throws UnsupportedOperationException if this operation is not supported by this collection
	 */
	public function remove (Object $element) : bool;

	/**
	 *    Removes all of this collection's elements that are also contained in the specified collection.
	 * After this call returns, this collection will contain no elements in common with the specified
	 * collection.
	 *
	 * @param Collection $collection
	 *    Collection containing elements to be removed from this collection
	 *
	 * @return TRUE if this collection changed as a result of the call, FALSE otherwise
	 *
	 * @throws UnsupportedOperationException if this operation is not supported by this collection
	 */
	public function removeAll (Collection $collection) : bool;

	/**
	 *    Retains only the elements in this collection that are contained in the specified collection.
	 * In other words, removes from this collection all of its elements that are not contained in the
	 * specified collection.
	 *
	 * @param Collection $collection
	 *    Collection containing elements to be retained in this collection
	 *
	 * @return TRUE if this collection changed as a result of the call, FALSE otherwise
	 *
	 * @throws UnsupportedOperationException if this operation is not supported by this collection
	 */
	public function retainAll (Collection $collection) : bool;

	/**
	 * Returns a sequential Stream with this collection as its source.
	 *
	 * @return a sequential Stream over the elements in this collection
	 */
	public function stream() : Stream;

	/**
	 * Returns an array containing all of the elements in this collection.
	 *
	 * @return an array containing all of the elements in this collection
	 */
	public function toArray() : array;

}

?>