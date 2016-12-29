<?php

namespace FunctionalPHP\common;

use FunctionalPHP\common\Object;
use FunctionalPHP\iterable\Iterable;
use FunctionalPHP\exception\UnsupportedOperationException;

/**
 * A container object with an Iterable property used when we want to use Collectors functionality.
 */
final class ObjectWithIterable extends Object {

	/**
	 * If non-null, the value; if null, indicates no value is present
	 */
	private $innerIterable;


	public function __construct (Iterable $iterable) {

		$this->innerIterable = $iterable;
	}


	/**
	 * Free the resources used by this class
	 */
	function __destruct() {

		unset ($this->iterable);
	}


	/**
	 * Returns the Iterable stored in this Object
	 *
	 * @return Iterable
	 */
	public function getIterable() : Iterable {

		return $this->innerIterable;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::equals()
	 */
	public function equals (Object $otherIterable): bool {

		if ($this->iterable === $otherIterable)
			return TRUE;

		if (!($otherIterable instanceof Iterable))
			return FALSE;

		return $this->iterable->equals ($otherIterable);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::hashCode()
	 */
	public function hashCode(): int {

		return $this->iterable->hashCode();
	}


	/**
	 * @throws UnsupportedOperationException
	 */
	public function compareTo (Object $otherCar): int {

		throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
				                                ,"This method is not supported");
	}

}

?>