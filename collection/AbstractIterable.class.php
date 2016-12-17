<?php

namespace FunctionalPHP\collection;

use FunctionalPHP\collection\Iterable;

/**
 *    This class provides a skeletal implementation of the Iterable interface, to minimize the effort
 * required to implement this interface.
 */
abstract class AbstractIterable implements Iterable {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::clear()
	 */
	abstract public function clear();

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::iterator()
	 */
	abstract public function iterator() : \Iterator;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::size()
	 */
	abstract public function size() : int;


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::hashCode()
	 */
	public function hashCode() : int {

		$result = 0;
		foreach ($this->iterator() as $element)
			$result += $element->hashCode();

			return $result;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::clear()
	 */
	public function isEmpty() : bool {

		return $this->size() == 0;
	}

}

?>