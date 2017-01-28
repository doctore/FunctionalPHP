<?php

namespace FunctionalPHP\iterable\collection;

use FunctionalPHP\iterable\Iterable;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\functional\Stream;
use FunctionalPHP\common\functional\Predicate;

/**
 *    The root interface in the Collection hierarchy. A Collection represents a group of objects,
 * known as its elements. Some collections allow duplicate elements and others do not. Some are
 * ordered and others unordered.
 */
interface Collection extends Iterable {

	// Permited "returned types" of the functions that receives a Closure function as parameter
	const VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA = array ("bool");
	const VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH = array ("void");


	/**
	 * Adds the given object to this Collection and returns TRUE if this Collection changed.
	 *
	 *    If a Collection refuses to add a particular element for any reason other than that
	 * it already contains the element, it must throw an exception (rather than returning
	 * FALSE). This preserves the invariant that a Collection always contains the specified
	 * element after this call returns.
	 *
	 * @param Object $element
	 *    Element whose presence in this Collection is to be ensured
	 *
	 * @return TRUE if this Collection changed as a result of the call, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if some property of the element prevents it from being
	 *         added to this Collection
	 * @throws UnsupportedOperationException if this operation is not supported by this Collection
	 */
	public function add (Object $element) : bool;

	/**
	 * Adds all of the elements in the specified Collection to this Collection.
	 *
	 * @param Collection $collection
	 *    Collection containing elements to be added to this Collection
	 *
	 * @return TRUE if this Collection changed as a result of the call, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if some property of an element of the specified Collection
	 *         prevents it from being added to this Collection
	 * @throws UnsupportedOperationException if this operation is not supported by this Collection
	 */
	public function addAll (Collection $collection) : bool;

	/**
	 *    Returns TRUE if this Collection contains the specified element. More formally, returns TRUE
	 * if and only if this Collection contains at least one element element such that:
	 *
	 *   $element.equals ($elementInCollection)
	 *
	 * @param Object $element
	 *    Element whose presence in this Collection is to be tested
	 *
	 * @return TRUE if this Collection contains the specified element, FALSE otherwise
	 */
	public function contains (Object $element) : bool;

	/**
	 * Returns TRUE if this Collection contains all of the elements in the specified Collection.
	 *
	 * @param Collection $collection
	 *    Collection to be checked for containment in this Collection
	 *
	 * @return true if this Collection contains all of the elements in the specified collection,
	 *         false otherwise
	 */
	public function containsAll (Collection $collection) : bool;

	/**
	 * Compares the specified Collection with this Collection for equality.
	 *
	 * @param Collection $collection
	 *    Collection to be compared for equality with this Collection
	 *
	 * @return TRUE if the specified object is equal to this Collection, FALSE otherwise
	 */
	public function equals (Collection $collection) : bool;

	/**
	 * Returns a Collection consisting of the elements of this Collection that match the given predicate.
	 *
	 * @param Predicate $predicate
	 *    Predicate to apply to each element to determine if it should be included.
	 *
	 * @return a Collection with filtered elements
	 */
	public function filter (Predicate $predicate) : Collection;

	/**
	 *    Returns a Collection consisting of the elements of this Collection that match the given Closure.
	 * The given function must satisfies the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal (or subclass) of Object.
	 *   3. The returned type is not empty and valid (Collection::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA)
	 *
	 * @example
	 *	 $filteredList = $arrayList->filterByLambda (function (MyObject $myObject) : bool {
	 *		                                            return $myObject->intProperty % 2 == 0;
	 *	                                             });
	 *
	 * @param \Closure $funtionToFilter
	 *    Anonymous function to apply to each element to determine if it should be included.
	 *
	 * @return a Collection with filtered elements
	 *
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public function filterByLambda (\Closure $funtionToFilter) : Collection;

	/**
	 *    Applies the given function to the elements of this Collection. The given function must satisfies
	 * the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal (or subclass) of Object.
	 *   3. The returned type is empty or valid (Collection::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH)
	 *
	 * @example
	 *   $arrayList->forEach (function (MyObject $myObject) {
	 *	                         $myObject->intProperty++;
	 *	                      });
	 *
	 * @param \Closure $functionToApply
	 *    Function to apply to each element.
	 *
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public function forEach (\Closure $functionToApply);

	/**
	 *    Removes a single instance of the specified element from this Collection, if it is present.
	 * More formally, removes an element such that:
	 *
	 *   $element.equals ($elementInCollection)
	 *
	 * @param Object $element
	 *    Element to be removed from this Collection, if present
	 *
	 * @return TRUE if an element was removed as a result of this call, FALSE otherwise
	 *
	 * @throws UnsupportedOperationException if this operation is not supported by this Collection
	 */
	public function remove (Object $element) : bool;

	/**
	 *    Removes all of this Collection's elements that are also contained in the specified Collection.
	 * After this call returns, this Collection will contain no elements in common with the specified
	 * Collection.
	 *
	 * @param Collection $collection
	 *    Collection containing elements to be removed from this Collection
	 *
	 * @return TRUE if this Collection changed as a result of the call, FALSE otherwise
	 *
	 * @throws UnsupportedOperationException if this operation is not supported by this Collection
	 */
	public function removeAll (Collection $collection) : bool;

	/**
	 *    Retains only the elements in this Collection that are contained in the specified Collection.
	 * In other words, removes from this Collection all of its elements that are not contained in the
	 * specified Collection.
	 *
	 * @param Collection $collection
	 *    Collection containing elements to be retained in this Collection
	 *
	 * @return TRUE if this Collection changed as a result of the call, FALSE otherwise
	 *
	 * @throws UnsupportedOperationException if this operation is not supported by this Collection
	 */
	public function retainAll (Collection $collection) : bool;

	/**
	 * Returns a sequential Stream with this Collection as its source.
	 *
	 * @return a sequential Stream over the elements in this Collection
	 */
	public function stream() : Stream;

	/**
	 * Returns an array containing all of the elements in this Collection.
	 *
	 * @return an array containing all of the elements in this Collection
	 */
	public function toArray() : array;

}

?>