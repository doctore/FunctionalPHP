<?php

namespace FunctionalPHP\iterable\collection\queue;

use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\iterable\collection\queue\AbstractQueue;
use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;
use FunctionalPHP\common\util\ArrayUtil;

/**
 *    A queue that further provides a total ordering on its elements. The elements are ordered using
 * their compareTo method or by a Comparator typically provided at priority queue creation time. The
 * queue's iterator will traverse the queue in ascending element order.
 */
class PriorityQueue extends AbstractQueue {

	/**
	 *    If this property has a value then must be an instance of an object that implements
	 * Comparator interface and it will be used to compare the elements stored in this queue.
	 * If this property has not a value then the method compareTo of the stored objects will
	 * be used to order them.
	 */
	protected $comparator;


	/**
	 * Constructs a queue containing the elements of the specified queue.
	 *
	 * @param Collection $collection
	 *    The collection whose elements are to be placed into this queue
	 * @param Comparator $comparator
	 *    Class used to compare the elements stored in this queue
	 */
	public function __construct (Collection $collection = NULL, Comparator $comparator = NULL) {

		parent::__construct (array());
		$this->comparator = $comparator;

		// Adds the given collection to the current queue
		if (!is_null ($collection))
			$this->addAll ($collection);
	}


	/**
	 * Free the resources used by this queue
	 */
	function __destruct() {

		unset ($this->internalData);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::add()
	 */
	public function add (Object $element) : bool {

		$searchInformation = ArrayUtil::binaryObjectSearch ($this->internalData, $element, $this->comparator);

		if ($searchInformation->position == $this->size())
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

		if (!($collection instanceof Queue))
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
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::iterator()
	 */
	public function iterator() : \Iterator {

		return new PriorityQueueIterator ($this->internalData);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\queue\Queue::peek()
	 */
	public function peek() : Optional {

		if ($this->isEmpty())
			return new Optional (NULL);

		return new Optional ($this->internalData[0]);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\queue\Queue::poll()
	 */
	public function poll() : Optional {

		if ($this->isEmpty())
			return new Optional (NULL);

		$headElement = $this->internalData[0];
		array_splice ($this->internalData, 0, 1);

		return new Optional ($headElement);
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
	 * @see \FunctionalPHP\collection\Collection::removeAll()
	 */
	public function removeAll (Collection $collection) : bool {

		$numOfElementsBefore = $this->size();

		foreach ($collection->iterator() as $element) {
			for ($i = count($this->internalData) - 1; $i >= 0; $i--) {

				if ($element->equals ($this->internalData[$i]))
					array_splice ($this->internalData, $i, 1);
			}
		}
		return $numOfElementsBefore != $this->size();
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
	 * @see \FunctionalPHP\collection\Collection::size()
	 */
	public function size() : int {

		return count ($this->internalData);
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
 * Class used to iterate over elements of a PriorityQueue
 */
class PriorityQueueIterator implements \Iterator {

	/**
	 * The array buffer into which the elements of the PriorityQueue are stored
	 */
	private $internalData;


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
	 * Returns the key of the current element of the internal array.
	 *
	 * @return the key of the current element of the internal array
	 *
	 * @see Iterator::key()
	 */
	public function key() {

		return key ($this->internalData);
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