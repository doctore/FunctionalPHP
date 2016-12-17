<?php

namespace FunctionalPHP\collection\queue;

use FunctionalPHP\collection\Collection;
use FunctionalPHP\common\Optional;

/**
 *    A collection designed for holding elements prior to processing (typically, but do not
 * necessarily, order elements in a FIFO manner).
 */
interface Queue extends Collection {


	/**
	 *    Retrieves an Optional with the head of this queue or with NULL if this queue is empty
	 * (does not remove the head of the queue)
	 *
	 * @return an Optional with the head of this queue or with NULL if this queue is empty
	 */
	public function peek() : Optional;


	/**
	 *    Retrieves an Optional with the head of this queue or with NULL if this queue is empty
	 * (removes the head of the queue).
	 *
	 * @return an Optional with the head of this queue or with NULL if this queue is empty
	 */
	public function poll() : Optional;

}

?>