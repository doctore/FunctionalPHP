<?php

namespace FunctionalPHP\iterable\collection\lists;

use FunctionalPHP\iterable\collection\AbstractCollection;
use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\iterable\collection\lists\Lists;
use FunctionalPHP\common\Object;

/**
 *    This class provides a skeletal implementation of the Lists interface, to minimize the effort
 * required to implement this interface.
 */
abstract class AbstractLists extends AbstractCollection implements Lists {

	/**
	 * The array buffer into which the elements of the Lists are stored
	 */
	protected $internalData;


	/**
	 * Constructs a skeletal implementation of a Lists.
	 *
	 * @param array $internalData
	 *    Array buffer into which the elements are stored
	 */
	public function __construct (array $internalData) {

		$this->internalData = $internalData;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::addByIndex()
	 */
	abstract public function addByIndex (int $index, Object $element) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::get()
	 */
	abstract public function get (int $index) : Object;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::indexOf()
	 */
	abstract public function indexOf (Object $element) : int;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::lastIndexOf()
	 */
	abstract public function lastIndexOf (Object $element) : int;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::removeByIndex()
	 */
	abstract function removeByIndex (int $index) : Object;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::set()
	 */
	abstract public function set (int $index, Object $element) : Object;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\lists\Lists::subList()
	 */
	abstract public function subList (int $fromIndex, int $toIndex) : Lists;


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::clear()
	 */
	public function clear() {

		$this->internalData = array();
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::contains()
	 */
	public function contains (Object $element) : bool {

		return $this->indexOf ($element) >= 0;
	}

}

?>