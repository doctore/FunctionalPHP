<?php

namespace FunctionalPHP\iterable\collection\queue;

use FunctionalPHP\iterable\collection\AbstractCollection;
use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\iterable\collection\queue\Queue;
use FunctionalPHP\common\Optional;


/**
 *    This class provides a skeletal implementation of the Queue interface, to minimize the effort
 * required to implement this interface.
 */
abstract class AbstractQueue extends AbstractCollection implements Queue {

	/**
	 * The array buffer into which the elements of the Queue are stored
	 */
	protected $internalData;

	/**
	 * Constructs a skeletal implementation of a Queue.
	 *
	 * @param array $internalData
	 *    Array buffer into which the elements are stored
	 */
	public function __construct (array $internalData) {

		$this->internalData = $internalData;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\queue\Queue::peek()
	 */
	abstract public function peek() : Optional;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\queue\Queue::poll()
	 */
	abstract public function poll() : Optional;


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::clear()
	 */
	public function clear() {

		$this->internalData = array();
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\iterable\collection\AbstractCollection::forEach()
	 */
	public function forEach (\Closure $functionToApply) {

		$this->checkClosureFunctionOfForeach ($functionToApply);

		array_map ($functionToApply, $this->internalData);
	}

}

?>