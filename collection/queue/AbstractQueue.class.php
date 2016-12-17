<?php

namespace FunctionalPHP\collection\queue;

use FunctionalPHP\collection\AbstractCollection;
use FunctionalPHP\collection\Collection;
use FunctionalPHP\collection\queue\Queue;
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

}

?>