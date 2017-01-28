<?php

namespace FunctionalPHP\iterable\collection\queue;

use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\common\Optional;

/**
 *    A Collection designed for holding elements prior to processing (typically, but do not
 * necessarily, order elements in a FIFO manner).
 */
interface Queue extends Collection {


	/**
	 *    Retrieves an Optional with the head of this Queue or with NULL if this Queue is empty
	 * (does not remove the head of the Queue)
	 *
	 * @return an Optional with the head of this Queue or with NULL if this Queue is empty
	 */
	public function peek() : Optional;


	/**
	 *    Retrieves an Optional with the head of this Queue or with NULL if this Queue is empty
	 * (removes the head of the Queue).
	 *
	 * @return an Optional with the head of this Queue or with NULL if this Queue is empty
	 */
	public function poll() : Optional;

}

?>