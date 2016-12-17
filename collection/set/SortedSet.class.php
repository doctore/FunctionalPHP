<?php

namespace FunctionalPHP\collection\set;

use FunctionalPHP\collection\Collection;
use FunctionalPHP\collection\set\Set;
use FunctionalPHP\collection\set\AbstractSet;
use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;
use FunctionalPHP\common\util\ArrayUtil;
use FunctionalPHP\exception\IllegalArgumentException;
use FunctionalPHP\exception\UnsupportedOperationException;

/**
 *    A set that further provides a total ordering on its elements. The elements are ordered using
 * their compareTo method or by a Comparator typically provided at sorted set creation time. The
 * set's iterator will traverse the set in ascending element order.
 */
class SortedSet extends AbstractSet {

	/**
	 *    If this property has a value then must be an instance of an object that implements
	 * Comparator interface and it will be used to compare the elements stored in this set.
	 * If this property has not a value then the method compareTo of the stored objects will
	 * be used to order them.
	 */
	protected $comparator;


	/**
	 * Constructs a set containing the elements of the specified set.
	 *
	 * @param Collection $collection
	 *    The collection whose elements are to be placed into this set
	 * @param Comparator $comparator
	 *    Class used to compare the elements stored in this set
	 */
	public function __construct (Collection $collection = NULL, Comparator $comparator = NULL) {

		parent::__construct (array());
		$this->comparator = $comparator;

		// Adds the given collection to the current set
		if (!is_null ($collection))
			$this->addAll ($collection);
	}


	/**
	 * Free the resources used by this set
	 */
	function __destruct() {

		unset ($this->internalData);
	}


	/**
	 *    Adds the specified element to this set if it is not already present. More formally, adds
	 * the specified element e to this set if the set contains no element e2 such that
	 *
	 *   e->equals(e2)
	 *
	 *    If this set already contains the element, the call leaves the set unchanged and returns
	 * FALSE.
	 *
	 * @param Object $element
	 *    Element whose presence in this set is to be ensured
	 *
	 * @return TRUE if this set changed as a result of the call, FALSE otherwise
	 */
	public function add (Object $element) : bool {

		$searchInformation = ArrayUtil::binaryObjectSearch ($this->internalData, $element, $this->comparator);

		// The given elements exists in this set
		if ($searchInformation->isElementFound)
			$this->internalData [$searchInformation->position] = $element;

		// The object will be added at the final position of this set
		elseif ($searchInformation->position == $this->size())
			$this->internalData[] = $element;

		else
			array_splice ($this->internalData, $searchInformation->position, 0, array ($element));

		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::contains()
	 */
	public function contains (Object $element) : bool {

		return ArrayUtil::binaryObjectSearch ($this->internalData, $element, $this->comparator)->isElementFound;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::equals()
	 */
	public function equals (Collection $collection) : bool {

		if ($this === $collection)
			return TRUE;

		if (!($collection instanceof Set))
			return FALSE;

		if ($this->size() != $collection->size())
			return FALSE;

		// Checks every element of the given collection (taking into account the ordination too)
		$currentIndex = 0;
		foreach ($collection->iterator() as $element) {

			if (!$this->internalData [$currentIndex++]->equals ($element))
				return FALSE;
		}
		return TRUE;
	}


	/**
	 *    Returns an Optional with the first (lowest) element currently in this set or with NULL
	 * if this set is empty.
	 *
	 * @return an Optional with the first (lowest) element currently in this set or with NULL if
	 *         this set is empty
	 */
	public function first() : Optional {

		return $this->isEmpty() ? new Optional (NULL) : new Optional ($this->internalData[0]);
	}


	/**
	 *    Returns a SortedSet with the portion of this set whose elements are strictly less than
	 * $element (if $inclusive = FALSE) or less than or equal than $element (if $inclusive = TRUE)
	 *
	 * @param Object $toElement
	 *    High endpoint of the returned set
	 * @param bool $inclusive
	 *    If TRUE the given $toElement is returned (if exists) in the set returned, if FALSE then only
	 *    whose elements are strictly less than $toElement will be returned.
	 *
	 * @return a view of the portion of this set
	 */
	public function headSet (Object $toElement, bool $inclusive = FALSE) : SortedSet {

		$searchInformation = ArrayUtil::binaryObjectSearch ($this->internalData, $toElement, $this->comparator);

		// Finds the high endpoint of the returned set
		$searchInformation->position = ($searchInformation->isElementFound && $inclusive)
				                           ? $searchInformation->position
				                           : $searchInformation->position - 1;

		$resultSortedSet = new SortedSet (new SortedSet(), $this->comparator);

		for ($i = 0; $i <= $searchInformation->position; $i++)
			$resultSortedSet->add ($this->internalData[$i]);

		return $resultSortedSet;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::iterator()
	 */
	public function iterator() : \Iterator {

		return new SortedSetIterator ($this->internalData);
	}


	/**
	 *    Returns an Optional with the last (highest) element currently in this set or with NULL
	 * if this set is empty.
	 *
	 * @return an Optional with the last (highest) element currently in this set or with NULL if
	 *         this set is empty.
	 */
	public function last() {

		return $this->isEmpty() ? new Optional (NULL) : new Optional ($this->internalData [$this->size() - 1]);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::remove()
	 */
	public function remove (Object $element) : bool {

		$searchInformation = ArrayUtil::binaryObjectSearch ($this->internalData, $element, $this->comparator);
		if ($searchInformation->isElementFound) {

			array_splice ($this->internalData, $searchInformation->position, 1);
			return TRUE;
		}
		return FALSE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::retainAll()
	 */
	public function retainAll (Collection $collection) : bool {

		$elementsInCommon    = array();
		$numOfElementsBefore = $this->size();

		foreach ($this->internalData as $element) {

			if ($collection->contains ($element))
				$elementsInCommon[] = $element;
		}
		$this->internalData = $elementsInCommon;
		return $numOfElementsBefore != $this->size();
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::size()
	 */
	public function size() : int {

		return count ($this->internalData);
	}


	/**
	 *    Returns a SortedSet with the portion of this set whose elements range from $fromElement,
	 * inclusive if $fromInclusive == TRUE, to $toElement, exclusive if $toInclusive == FALSE.
	 * If $fromElement and $toElement are equal, the returned set is empty.
	 *
	 * @param Object $fromElement
	 *    Low endpoint of the returned set
	 * @param Object $toElement
	 *    High endpoint of the returned set
	 * @param bool $fromInclusive
	 *    If TRUE the given $fromElement is returned (if exists) in the set returned, if FALSE then only
	 *    whose elements are strictly greater than $fromElement will be returned.	 *
	 * @param bool $toInclusive
	 *    If TRUE the given $toElement is returned (if exists) in the set returned, if FALSE then only
	 *    whose elements are strictly less than $toElement will be returned.
	 *
	 * @return a view of the portion of this set whose elements range from $fromElement, inclusive,
	 *         to $toElement, exclusive
	 *
	 * @throws IllegalArgumentException if $fromElement is greater than $toElement
	 */
	public function subSet (Object $fromElement, Object $toElement
			               ,bool $fromInclusive = FALSE, bool $toInclusive = FALSE) : SortedSet {

		$order = isset ($this->comparator) ? $this->comparator->compare ($fromElement, $toElement)
		                                   : $fromElement->compareTo ($toElement);
        if ($order > 0)
        	throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
		                                   		,"The given fromElement: {$fromElement} is greater than "
		                                   		 ."the given toElement: ".$toElement);

        $resultSortedSet = new SortedSet (new SortedSet(), $this->comparator);

        // Searches $fromElement in this set
        $searchInformationFromElement = ArrayUtil::binaryObjectSearch ($this->internalData, $fromElement, $this->comparator);
        $searchInformationFromElement->position = ($searchInformationFromElement->isElementFound && !$fromInclusive)
                                                      ? $searchInformationFromElement->position + 1
                                                      : $searchInformationFromElement->position;

        // The current (or potential) position of $fromElement is "inside" of this set
        if ($searchInformationFromElement->position < $this->size()) {

        	// Searches $toElement in this set
        	$searchInformationToElement = ArrayUtil::binaryObjectSearch ($this->internalData, $toElement, $this->comparator);
        	$searchInformationToElement->position = ($searchInformationToElement->isElementFound && $toInclusive)
        	                                            ? $searchInformationToElement->position
        	                                            : $searchInformationToElement->position - 1;

			for ($i = $searchInformationFromElement->position; $i <= $searchInformationToElement->position; $i++)
				$resultSortedSet->add ($this->internalData[$i]);
        }
    	return $resultSortedSet;
	}


	/**
	 *    Returns a SortedSet with the portion of this set whose elements are strictly greater than
	 * $element (if $inclusive = FALSE) or greater than or equal than $element (if $inclusive = TRUE)
	 *
	 * @param Object $fromElement
	 *    Low endpoint of the returned set
	 * @param bool $inclusive
	 *    If TRUE the given $fromElement is returned (if exists) in the set returned, if FALSE then only
	 *    whose elements are strictly greater than $fromElement will be returned.
	 */
	public function tailSet (Object $fromElement, bool $inclusive = FALSE) : SortedSet {

		$searchInformation = ArrayUtil::binaryObjectSearch ($this->internalData, $fromElement, $this->comparator);

		// Finds the low endpoint of the returned set
        $searchInformation->position = ($searchInformation->isElementFound && !$inclusive)
                                           ? $searchInformation->position + 1
                                           : $searchInformation->position;

		$resultSortedSet = new SortedSet (new SortedSet(), $this->comparator);
		$numOfElements   = $this->size();

		for ($i = $searchInformation->position; $i < $numOfElements; $i++)
			$resultSortedSet->add ($this->internalData[$i]);

		return $resultSortedSet;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::toArray()
	 */
	public function toArray() : array {

		return $this->internalData;
	}

}



/**
 * Class used to iterate over elements of an SortedSet
 */
class SortedSetIterator implements \Iterator {

	/**
	 * The array buffer into which the elements of the SortedSet are stored
	 */
	private $internalData;

	/**
	 * The array buffer into which the elements of the HashSet with the same hash code are stored
	 */
	private $internalDataSecondLevel;


	public function __construct (array $internalData) {

		$this->internalData = $internalData;
	}


	/**
	 * Returns the current element of the array.
	 *
	 * @return the current element of the array
	 *
	 * @see Iterator::current()
	 */
	public function current() {

		return current ($this->internalData);
	}


	/**
	 * This operation is not supported by this Iterator
	 *
	 * @see Iterator::key()
	 *
	 * @throws UnsupportedOperationException
	 */
	public function key() {
		throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
				                                ,"It is not possible to access to the key of a HashSet. "
				                                 ."The keys only have an 'internal purpose'");
	}


	/**
	 * Returns the next element of the internal array.
	 *
	 * @return the next element of the internal array
	 *
	 * @see Iterator::next()
	 */
	public function next() {

		return next ($this->internalData);
	}


	/**
	 * Returns the first element of the internal array.
	 *
	 * @return the first element of the internal array
	 *
	 * @see Iterator::rewind()
	 */
	public function rewind() {

		reset ($this->internalData);
	}


	/**
	 * Returns TRUE if the current element of the internal array is a valid object, FALSE otherwise.
	 *
	 * @return TRUE if the current element of the internal array is a valid object, FALSE otherwise
	 *
	 * @see Iterator::valid()
	 */
	public function valid() {

		return ($this->current() !== FALSE);
	}

}

?>