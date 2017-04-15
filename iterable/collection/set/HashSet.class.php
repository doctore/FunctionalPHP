<?php

namespace FunctionalPHP\iterable\collection\set;

use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\iterable\collection\set\Set;
use FunctionalPHP\iterable\collection\set\AbstractSet;

use FunctionalPHP\common\Object;
use FunctionalPHP\common\functional\Predicate;

use FunctionalPHP\exception\UnsupportedOperationException;
use FunctionalPHP\common\Optional;

/**
 *    This class implements the Set interface, backed by a hash table. It makes no guarantees as to
 * the iteration order of the set; in particular, it does not guarantee that the order will remain
 * constant over time.
 *
 *    The array buffer into which the elements of the HashSet are stored. It is important to know
 * that it is a matrix on which the first level stores the different hash codes of the saved
 * elements and the elements with the same hash code at the second one. For example:
 *
 *    $internalData[12][0] = object1  (object with a hash code = 12)
 *    $internalData[12][1] = object2  (object with a hash code = 12)
 *    $internalData[36][0] = object3  (object with a hash code = 36)
 */
class HashSet extends AbstractSet {

	/**
	 * Constructs a set containing the elements of the specified set.
	 *
	 * @param Collection $collection
	 *    The collection whose elements are to be placed into this set
	 */
	public function __construct (Collection $collection = NULL) {

		parent::__construct (array());

		// Adds the given collection to the current set
		if (!is_null ($collection) && !$collection->isEmpty())
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

		$hashCode = $element->hashCode();

		// If it is the first element with that hash code
		if (!isset ($this->internalData[$hashCode]))
			$this->internalData[$hashCode] = array ($element);

		else {
			foreach ($this->internalData[$hashCode] as &$internalElement) {

				// If this set contains an "equal element" => it won't be replaced
				if ($internalElement->equals ($element))
					return FALSE;
			}
			// The given element does not exists in this set
			$this->internalData[$hashCode][] = $element;
		}
		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::contains()
	 */
	public function contains (Object $element) : bool {

		return $this->getElementEqualsTo ($element)->isPresent();
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

		// Checks every element of the given collection
		foreach ($collection->iterator() as $element) {

			if (!$this->contains ($element))
				return FALSE;
		}
		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\iterable\collection\Collection::filter()
	 */
	public function filter (Predicate $predicate) : Collection {

		$filteredHashSet = new HashSet();

		foreach ($this->iterator() as $element) {

			if ($predicate->test ($element))
				$filteredHashSet->add ($element);
		}
		return $filteredHashSet;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\iterable\collection\Collection::filterByLambda()
	 */
	public function filterByLambda (\Closure $funtionToFilter) : Collection {

		$this->checkClosureFunctionOfFilterByLambda ($funtionToFilter);

		$filteredHashSet = new HashSet();

		foreach ($this->iterator() as $element) {

			if ($funtionToFilter ($element))
				$filteredHashSet->add ($element);
		}
		return $filteredHashSet;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\iterable\collection\set\Set::get()
	 */
	public function get (Object $element) : Optional {

		return $this->getElementEqualsTo ($element);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::iterator()
	 */
	public function iterator() : \Iterator {

		return new HashSetIterator ($this->internalData);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::remove()
	 */
	public function remove (Object $element) : bool {

		$hashCode = $element->hashCode();

		if (!isset ($this->internalData[$hashCode]))
			return FALSE;

		foreach ($this->internalData[$hashCode] as $key => $internalElement) {

			// The given element exists in the set
			if ($internalElement->equals ($element)) {

				array_splice ($this->internalData[$hashCode], $key, 1);

				// There are not more elements with the same hash code
				if (count ($this->internalData[$hashCode]) == 0)
					unset ($this->internalData[$hashCode]);

				return TRUE;
			}
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

		foreach ($collection->iterator() as $element) {

			$hashCode = $element->hashCode();

			// There are elements with the same has code
			if (isset ($this->internalData[$hashCode])) {

				foreach ($this->internalData[$hashCode] as $internalElement) {

					/**
					 *    If this set contains an "equal element" that the "current one"
					 * of the given collection
					 */
					if ($internalElement->equals ($element)) {

						/**
						 *    There are other elements with the same hash code in the array used
						 * to store the elements contained in both collections
						 */
						if (isset ($elementsInCommon[$hashCode]))
							$elementsInCommon[$hashCode][] = $element;
						else
							$elementsInCommon[$hashCode] = array ($element);

						break;
					}
				}
			}
		}
		$this->internalData = $elementsInCommon;
		return $numOfElementsBefore != $this->size();
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::size()
	 */
	public function size() : int {

		return (count ($this->internalData, COUNT_RECURSIVE) - count ($this->internalData));
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::toArray()
	 */
	public function toArray() : array {

		$result = array();

		// It is necessary to return only the "second level" of the internal data
		foreach ($this->iterator() as $element)
			$result[] = $element;

		return $result;
	}


	/**
	 * Returns an Optional with the Object in the current Set equals to the given one.
	 *
	 * @param Object $element
	 *    Element to search for
	 *
	 * @return an Optional with the Object in the current Set equals to the given one,
	 *         with NULL if $element does not exists.
	 */
	private function getElementEqualsTo (Object $element) : Optional {

		if ($this->isEmpty())
			return new Optional (NULL);

		$hashCode = $element->hashCode();

		// If it is the first element with that hash code
		if (!isset ($this->internalData[$hashCode]))
			return new Optional (NULL);

		foreach ($this->internalData[$hashCode] as $internalElement) {

			// If this set contains an "equal element" => it will be replaced by the given one
			if ($internalElement->equals ($element))
				return new Optional ($internalElement);
		}
		return new Optional (NULL);
	}

}



/**
 * Class used to iterate over elements of a HashSet
 */
class HashSetIterator implements \Iterator {

	/**
	 * The array buffer into which the elements of the HashSet are stored
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
	 * Returns the current element of the internal array.
	 *
	 * @return the current element of the internal array
	 *
	 * @see Iterator::current()
	 */
	public function current() {

		/**
		 *    If they are not elements with the "previous hash code" => gets the elements of "the
		 * current hash code"
		 */
		if (!isset ($this->internalDataSecondLevel)) {
			$this->internalDataSecondLevel = current ($this->internalData);

			/**
			 *    The internal pointer should point to the first element of the array used to store
			 * the objects with the same hash code.
			 */
			if (is_array ($this->internalDataSecondLevel))
				reset ($this->internalDataSecondLevel);
		}
		/**
		 *    If there are not elements with "the current hash code" => returns FALSE.  The current
		 * element otherwise
		 */
		return (!$this->internalDataSecondLevel ? $this->internalDataSecondLevel
				                                : current ($this->internalDataSecondLevel));
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

		$currentElement = next ($this->internalDataSecondLevel);

		// If there is no elements with the "current hash code" => get the "next hash code"
		if (!$currentElement) {

			$nextHashCode = next ($this->internalData);
			if (!$nextHashCode)
				return FALSE;

			/**
			 *    Now we need to move the internal pointer to the "first elements" of the
			 * "next hash code" (now "the current")
			 */
			$this->internalDataSecondLevel = current ($this->internalData);
			reset ($this->internalDataSecondLevel);

			return $this->current();
		}
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