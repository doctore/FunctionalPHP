<?php

namespace FunctionalPHP\iterable\collection\lists;

use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\iterable\collection\lists\Lists;
use FunctionalPHP\iterable\collection\lists\AbstractLists;
use FunctionalPHP\common\Object;
use FunctionalPHP\exception\IllegalArgumentException;

/**
 * Resizable-array implementation of the Lists interface that permits all elements, including NULL.
 */
class ArrayList extends AbstractLists {

	/**
	 *    Constructs a list containing the elements of the specified list, in the order they are
	 * returned by the collection's iterator.
	 *
	 * @param Collection $collection
	 *    The collection whose elements are to be placed into this list
	 */
	public function __construct (Collection $collection = NULL) {

		parent::__construct (array());

		// Adds the given collection to the current list
		if (!is_null ($collection))
			$this->addAll ($collection);
	}


	/**
	 * Free the resources used by this list
	 */
	function __destruct() {

		unset ($this->internalData);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::add()
	 */
	public function add (Object $element) : bool {

		$this->internalData[] = $element;
		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::addByIndex()
	 */
	public function addByIndex (int $index, Object $element) : bool {

		// Checks if the given index is in range
		if (!$this->isIndexOutOfBoundsForAdd ($index))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given index: {$index} is not allowed in a list "
					                            ."with a size: ".count ($this->internalData));
		/**
		 *    Moves one position all elements with a greater index than the
		 * given one and inserts the given element in the desired index
		 */
		array_splice ($this->internalData, $index, 0, array ($element));
		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::equals()
	 */
	public function equals (Collection $collection) : bool {

		if ($this === $collection)
			return TRUE;

        if (!($collection instanceof Lists))
        	return FALSE;

        $numOfElements = $this->size();
        if ($numOfElements != $collection->size())
        	return FALSE;

        // Checks every element of the given collection (taking into account the index value too)
        for ($i = 0; $i < $numOfElements; $i++) {

        	if (!$this->internalData[$i]->equals ($collection->get($i)))
        		return FALSE;
        }
        return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::get()
	 */
	public function get (int $index) : Object {

		// Checks if the given index is in range
		if (!$this->isIndexOutOfBoundsForAccess ($index))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given index: {$index} is not accessible in a list "
					                           	."with a size: ".count ($this->internalData));
		return $this->internalData[$index];
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::indexOf()
	 */
	public function indexOf (Object $element) : int {

		$numOfElements = count ($this->internalData);

		// Searches the first object in the internal array equals to the given one
		for ($i = 0; $i < $numOfElements; $i++) {
			if ($element->equals ($this->internalData[$i]))
				return $i;
		}
		return -1;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::iterator()
	 */
	public function iterator() : \Iterator {

		return new ArrayListIterator ($this->internalData);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::lastIndexOf()
	 */
	public function lastIndexOf (Object $element) : int {

		$numOfElements = count ($this->internalData);

		// Searches the last object in the internal array equals to the given one
		for ($i = $numOfElements-1; $i >= 0; $i--) {
			if ($element->equals ($this->internalData[$i]))
				return $i;
		}
		return -1;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::remove()
	 */
	public function remove (Object $element) : bool {

		$positionOfElement = $this->indexOf ($element);
		if ($positionOfElement == -1)
			return FALSE;

			array_splice ($this->internalData, $positionOfElement, 1);
			return TRUE;
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
	 * @see \FunctionalPHP\collection\lists\Lists::remove()
	 */
	public function removeByIndex (int $index) : Object {

		// Checks if the given index is in range
		if (!$this->isIndexOutOfBoundsForAccess ($index))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given index: {$index} is not accessible in a list "
					                            ."with a size: ".count ($this->internalData));

		$oldElement = $this->internalData[$index];
		array_splice ($this->internalData, $index, 1);

		return $oldElement;
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
	 * @see \FunctionalPHP\collection\lists\Lists::set()
	 */
	public function set (int $index, Object $element) : Object {

		// Checks if the given index is in range
		if (!$this->isIndexOutOfBoundsForAccess ($index))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given index: {$index} is not accessible in a list "
					                            ."with a size: ".count ($this->internalData));

		$oldElement = $this->internalData[$index];
		$this->internalData[$index] = $element;

		return $oldElement;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::size()
	 */
	public function size() : int {

		return count ($this->internalData);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::subList()
	 */
	public function subList (int $fromIndex, int $toIndex) : Lists {

		if ($fromIndex < 0)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given fromIndex: {$fromIndex} is less than 0");

		if ($toIndex > count ($this->internalData))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
						                       ,"The given toIndex: {$toIndex} is greater than the number "
						                       	."of elements in the list: ".count ($this->internalData));
		if ($fromIndex > $toIndex)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
						                       ,"The given fromIndex: {$fromIndex} is greater than the "
						                       	."given toIndex: ".$toIndex);
		$subList = new ArrayList();
		for ($i = $fromIndex; $i < $toIndex; $i++)
			$subList->add ($this->internalData[$i]);

		return $subList;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::toArray()
	 */
	public function toArray() : array {

		return $this->internalData;
	}


	/**
	 * Checks if the given index is in range for an "accessing operation", that is, if and only if
	 *
	 *   (index >= 0 && index < size())
	 *
	 * @param int $index
	 *    Index to check
	 *
	 * @return TRUE if the given index is in range, FALSE otherwise
	 */
	private function isIndexOutOfBoundsForAccess (int $index) : bool {

		return ($index >= 0 && $index < count ($this->internalData));
	}


	/**
	 * Checks if the given index is in range for an "adding operation", that is, if and only if
	 *
	 *   (index >= 0 && index <= size())
	 *
	 * @param int $index
	 *    Index to check
	 *
	 * @return TRUE if the given index is in range, FALSE otherwise
	 */
	private function isIndexOutOfBoundsForAdd (int $index) : bool {

		return ($index >= 0 && $index <= count ($this->internalData));
	}

}



/**
 * Class used to iterate over elements of an ArrayList
 */
class ArrayListIterator implements \Iterator {

	/**
	 * The array buffer into which the elements of the ArrayList are stored
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