<?php

namespace FunctionalPHP\common\functional;

use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;
use FunctionalPHP\common\functional\Predicate;
use FunctionalPHP\exception\IllegalArgumentException;
use FunctionalPHP\exception\UnsupportedOperationException;
use FunctionalPHP\iterable\Iterable;

/**
 *    A sequence of elements supporting sequential aggregate operations. Collections and streams,
 * while bearing some superficial similarities, have different goals. Collections are primarily
 * concerned with the efficient management of, and access to, their elements. By contrast, streams
 * do not provide a means to directly access or manipulate their elements, and are instead concerned
 * with declaratively describing their source and the computational operations which will be
 * performed in aggregate on that source.
 */
interface Stream {

	// Permited "returned types" of the functions that receives a Closure function as parameter
	const VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_MAP = array ("bool", "float", "int", "string");
	const VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA = array ("bool");
	const VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH = array ("void");
	const VALID_RETURNED_TYPES_OF_CLOSURE_IN_SORTEDBYLAMBDA = array ("int");


	/**
	 *    Creates a concatenated Stream whose elements are all the elements of the first Stream followed
	 * by all the elements of the second Stream. The resulting Stream is ordered if both of the input
	 * streams are ordered.
	 *
	 * @param Stream $firstStream
	 *    The first Stream to concatenate
	 * @param Stream $secondStream
	 *    The second Stream to concatenate
	 *
	 * @return Stream with the concatenation of the two input streams
	 *
	 * @throws UnsupportedOperationException if the type of the elements stored in both streams are
	 *                                       incompatible (they are not equals or does not belong
	 *                                       to a subclass of Object).
	 */
	public static function concat (Stream $firstStream, Stream $secondStream) : Stream;

	/**
	 *    Returns whether all elements of this Stream match the provided predicate. May not evaluate
	 * the predicate on all elements if not necessary for determining the result. If the Stream is
	 * empty then TRUE is returned and the predicate is not evaluated.
	 *
	 * @param Predicate $predicate
	 *    Predicate to apply to elements of this Stream.
	 *
	 * @return TRUE if either all elements of the Stream match the provided predicate or the Stream
	 *         is empty, otherwise FALSE.
	 */
	public function allMatch (Predicate $predicate) : bool;

	/**
	 *    Returns whether any elements of this Stream match the provided predicate. May not evaluate
	 * the predicate on all elements if not necessary for determining the result. If the Stream is
	 * empty then FALSE is returned and the predicate is not evaluated.
	 *
	 * @param Predicate $predicate
	 *    Predicate to apply to elements of this Stream.
	 *
	 * @return TRUE if any elements of the Stream match the provided predicate, otherwise FALSE
	 */
	public function anyMatch (Predicate $predicate) : bool;

	/**
	 * Performs a collect operation on the elements of this Stream using a CollectorImpl.
	 *
	 * @example
	 *	 $basicStream->collect (Collectors::partitioningByUsingCollection (new MyPredicate(), new HashSet()));
	 *
	 * @param CollectorImpl $collector
	 *    Object with the information used to know how to accumulate the elements of the this Stream
	 *
	 * @return Iterable with the result to apply the given CollectorImpl to this Stream
	 *
	 * @throws UnsupportedOperationException if the type of the elements stored in the Stream is not
	 *                                       Object (or a subclass of it)
	 */
	public function collect (CollectorImpl $collector) : Iterable;

	/**
	 * Returns the count of elements in this Stream.
	 *
	 * @return the count of elements in this Stream
	 */
	public function count() : int;

	/**
	 *    Returns a Stream consisting of the distinct elements (according to
	 * equals method of the stored objects) of this Stream.
	 *
	 *    For ordered streams, the selection of distinct elements is stable
	 * (for duplicated elements, the element appearing first in the encounter
	 * order is preserved).
	 *
	 * @return a Stream with unique elements
	 */
	public function distinct() : Stream;

	/**
	 * Returns a Stream consisting of the elements of this Stream that match the given predicate.
	 *
	 * @param Predicate $predicate
	 *    Predicate to apply to each element to determine if it should be included.
	 *
	 * @return a Stream with filtered elements
	 */
	public function filter (Predicate $predicate) : Stream;

	/**
	 *    Returns a Stream consisting of the elements of this Stream that match the given Closure.
	 * The given function must satisfies the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter is equal to the type of the Stream's elements.
	 *      (or subclass of Object if the Stream stores Objects).
	 *   3. The returned type is not empty and valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA)
	 *
	 * @example
	 *	 $basicStream->filterByLambda (function (int $intValue) : bool {
	 *		                              return $intValue % 2 == 0;
	 *	                               });
	 *
	 * @param \Closure $funtionToFilter
	 *    Anonymous function to apply to each element to determine if it should be included.
	 *
	 * @return a Stream with filtered elements
	 *
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public function filterByLambda (\Closure $funtionToFilter) : Stream;

	/**
	 *    Returns an Optional describing the first element of the Stream, or an Optional with NULL
	 * as if the Stream is empty.
	 *
	 * @return an Optional describing the first element of the Stream, or an Optional with NULL as
	 *         content if the Stream is empty.
	 */
	public function findFirst() : Optional;

	/**
	 *    Returns a Stream consisting of the results of replacing each element of this Stream with
	 * the contents of a mapped Stream produced by applying the provided mapping function to each
	 * element. The given function must satisfies the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to the type of the Stream's elements.
	 *      (or subclass of Object if the Stream stores Objects).
	 *   3. The returned type must be an instance of Stream.
	 *
	 * @example
	 *   $basicStream->flatMap (function (MyObject $myObject) : Stream {
	 *	                           return $myObject->collectionProperty->stream();
	 *	                        });
	 *
	 * @param \Closure $functionToApply
	 *    Function to apply to each element which produces a Stream of new values.
	 *
	 * @return the new Stream
	 *
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public function flatMap (\Closure $funtionUsedToFlat) : Stream;

	/**
	 *    Applies the given function to the elements of this Stream. The given function must satisfies
	 * the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to the type of the Stream's elements.
	 *      (or subclass of Object if the Stream stores Objects).
	 *   3. The returned type is empty or valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH)
	 *
	 * @example
	 *   $basicStream->forEach (function (string $oldStringValue) {
	 *	                           $oldStringValue .= "newPrefix";
	 *	                        });
	 *
	 * @param \Closure $functionToApply
	 *    Function to apply to each element.
	 *
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public function forEach (\Closure $functionToApply);

	/**
	 * Returns the type of the stored elements in this Stream.
	 *
	 * @return the type of the stored elements in this Stream
	 */
	public function getCurrentTypeStoredByStream() : string;

	/**
	 *    Returns a Stream consisting of the elements of this Stream, truncated to be no longer
	 * than $maxSize in length.
	 *
	 * @param int $maxSize
	 *    The number of elements the Stream should be limited to.
	 *
	 * @return a Stream truncated to be no longer than $maxSize in length.
	 *
	 * @throws IllegalArgumentException if $maxSize < 0
	 */
	public function limit (int $maxSize) : Stream;

	/**
	 * Returns the minimum element of this Stream according to the provided Comparator.
	 *
	 * @param Comparator $comparator
	 *    Comparator to compare elements of this Stream
	 *
	 * @return an Optional describing the minimum element of this Stream
	 *
	 * @throws UnsupportedOperationException if the type of Stream's elements is not a subclass of Object
	 */
	public function min (Comparator $comparator) : Optional;

	/**
	 *    Returns a Stream consisting of the results of applying the given function to the elements
	 * of this Stream. The given function must satisfies the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to the type of the Stream's elements.
	 *      (or subclass of Object if the Stream stores Objects).
	 *   3. The returned type is not empty and valid, that is:
	 *        3.1 Equal to the type of Stream's elements
	 *        3.2 One of Stream::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_MAP
	 *        3.3 A subclass of Object.
	 *
	 * @example
	 *   $basicStream->map (function (string $stringValue) : int {
	 *	                       return strlen ($stringValue);
	 *	                    });
	 *
	 * @param \Closure $functionToApply
	 *    Function to apply to each element.
	 *
	 * @return a Stream after applying the given function to its elements
	 *
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public function map (\Closure $functionToApply) : Stream;

	/**
	 * Returns the maximum element of this Stream according to the provided Comparator.
	 *
	 * @param Comparator $comparator
	 *    Comparator to compare elements of this Stream
	 *
	 * @return an Optional describing the maximum element of this Stream
	 *
	 * @throws UnsupportedOperationException if the type of Stream's elements is not a subclass of Object
	 */
	public function max (Comparator $comparator) : Optional;

	/**
	 *    Returns whether no elements of this Stream match the provided predicate. May not evaluate
	 * the predicate on all elements if not necessary for determining the result. If the Stream is
	 * empty then TRUE is returned and the predicate is not evaluated.
	 *
	 * @param Predicate $predicate
	 *    Predicate to apply to elements of this Stream.
	 *
	 * @return TRUE if either no elements of the Stream match the provided predicate or the Stream
	 *         is empty, otherwise FALSE.
	 */
	public function noneMatch (Predicate $predicate) : bool;

	/**
	 *    Performs a reduction on the elements of this Stream, using an associative accumulation
	 * function, and returns an Optional describing the reduced value, if any. This is equivalent
	 * to:
	 *
	 *   <pre>
	 *      $result = $initialValue;
	 *
	 *      foreach (elements of Stream as $element)
	 *         $result = $accumulator ($result, $element);
	 *
	 *      return new Optional ($result);
	 *   </pre>
	 *
	 * The given function must satisfies the following rules:
	 *
	 *   1. Only has two parameters.
	 *   2. The type of the first parameter must be equal to the return type of this function.
	 *   3. The type of the second parameter must be equal to the type of the Stream's elements.
	 *      (or subclass of Object if the Stream stores Objects).
	 *
	 * @example
	 *   $basicStream->reduce (function (int $accumulatedValue, MyObject $myObject) : int {
	 *		                      return $myObject->intProperty + $accumulatedValue;
	 *	                       }
	 *                         ,0);
	 *
	 * @param \Closure $accumulator
	 *    An associative function for combining two values
	 * @param $initialValue
	 *    Initial value used to accumulate the values of the elements stored in the Stream
	 *
	 * @return Optional describing the result of the reduction
	 *
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public function reduce (\Closure $accumulator, $initialValue = NULL) : Optional;

	/**
	 *    Sorts the elements of the Stream according to natural order
	 * (@see \FunctionalPHP\common\Object::compareTo() for Objects).
	 *
	 * @return a Stream sorted according to natural order
	 */
	public function sorted() : Stream;

	/**
	 * Sorts the elements of the Stream according to the provided Comparator.
	 *
	 * @param Comparator $comparator
	 *    Comparator to be used to compare the Stream's elements.
	 *
	 * @return Stream sorted according to given Comparator.
	 *
	 * @throws UnsupportedOperationException if the type of Stream's elements is not a subclass of Object
	 */
	public function sortedByComparator (Comparator $comparator) : Stream;

	/**
	 *    Sorts the elements of the Stream according to the given function. The given function must
	 * satisfies the following rules:
	 *
	 *   1. Only has two parameters.
	 *   2. The type of the given parameters must be equal to the type of the Stream's elements.
	 *      (or subclass of Object if the Stream stores Objects).
	 *   3. The returned type is not empty and valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_SORTEDBYLAMBDA)
	 *
	 * @example
	 *   $basicStream->sortedByLambda (function (string $string1, string $string2) : int {
	 *		                              if ($string1 == $string2)
	 *			                              return 0;
     *
	 *		                              return ($string1 < $string2) ? -1 : 1;
	 *	                              });
	 *
	 * @param \Closure $functionToApply
	 *    Function used to sort the Stream's elements.
	 *
	 * @return Stream sorted according to given function
	 *
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public function sortedByLambda (\Closure $sortFunction) : Stream;

	/**
	 * Returns an array containing all of the elements in this Stream.
	 *
	 * @return an array containing all of the elements in this Stream
	 */
	public function toArray() : array;

}

?>