<?php

namespace FunctionalPHP\collection;

use FunctionalPHP\collection\AbstractIterable;
use FunctionalPHP\collection\Collection;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\functional\Stream;
use FunctionalPHP\common\functional\BasicStream;

/**
 *    This class provides a skeletal implementation of the Collection interface, to minimize the effort
 * required to implement this interface.
 */
abstract class AbstractCollection extends AbstractIterable implements Collection {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::add()
	 */
	abstract public function add (Object $element) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::contains()
	 */
	abstract public function contains (Object $element) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::equals()
	 */
	abstract public function equals (Collection $collection) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::remove()
	 */
	abstract public function remove (Object $element) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::retainAll()
	 */
	abstract function retainAll (Collection $collection) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::toArray()
	 */
	abstract public function toArray() : array;


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::addAll()
	 */
	public function addAll (Collection $collection) : bool {

		if ($collection->isEmpty())
			return FALSE;

		foreach ($collection->iterator() as $element)
			$this->add ($element);

		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::containsAll()
	 */
	public function containsAll (Collection $collection) : bool {

		foreach ($collection->iterator() as $element) {

			if (!$this->contains ($element))
				return FALSE;
		}
		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::removeAll()
	 */
	public function removeAll (Collection $collection) : bool {

		$numOfElementsBefore = $this->size();

		foreach ($collection->iterator() as $element)
			$this->remove ($element);

		return $numOfElementsBefore != $this->size();
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::stream()
	 */
	public function stream() : Stream {

		return new BasicStream ($this);
	}

}

?>