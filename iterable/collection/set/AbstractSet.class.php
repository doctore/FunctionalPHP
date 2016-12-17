<?php

namespace FunctionalPHP\iterable\collection\set;

use FunctionalPHP\iterable\collection\AbstractCollection;
use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\iterable\collection\set\Set;

/**
 *    This class provides a skeletal implementation of the Set interface, to minimize the effort
 * required to implement this interface.
 */
abstract class AbstractSet extends AbstractCollection implements Set {

	/**
	 * The array buffer into which the elements of the Set are stored
	 */
	protected $internalData;


	/**
	 * Constructs a skeletal implementation of a Set.
	 *
	 * @param array $internalData
	 *    Array buffer into which the elements are stored
	 */
	public function __construct (array $internalData) {

		$this->internalData = $internalData;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::clear()
	 */
	public function clear() {

		$this->internalData = array();
	}

}

?>