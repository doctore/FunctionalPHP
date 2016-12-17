<?php

namespace FunctionalPHP\common;

use FunctionalPHP\common\Object;
use FunctionalPHP\exception\UnsupportedOperationException;

/**
 *    A container object which may or may not contain a non-null value. If a value is present,
 * isPresent() will return TRUE and get() will return the value.
 */
final class Optional {

	/**
	 * If non-null, the value; if null, indicates no value is present
	 */
	private $value;


	public function __construct ($value) {

		$this->value = $value;
	}


	/**
	 * Free the resources used by this class
	 */
	function __destruct() {

		unset ($this->value);
	}


	/**
	 * Indicates whether some other Optional object is "equal to" this one.
	 *
	 * @param Optional $optional
	 *    The reference object with which to compare
	 *
	 * @return true if this object is the same as the obj argument, false otherwise
	 */
	public function equals (Optional $optional) : bool {

		if ($this === $optional)
			return TRUE;

		if ($this->isPresent() && !$optional->isPresent())
			return FALSE;

		if (!$this->isPresent() && $optional->isPresent())
			return FALSE;

		if (!$this->isPresent() && !$optional->isPresent())
			return TRUE;

		if (is_subclass_of ($this->value, Object::class) && is_subclass_of ($optional->get(), Object::class))
			return $this->value->equals ($optional->get());

		return $this->value === $optional->get();
	}


	/**
	 * If a value is present in this object returns it, otherwise throws UnsupportedOperationException.
	 *
	 * @return contained value if it is not NULL, otherwise throws UnsupportedOperationException
	 *
	 * @throws UnsupportedOperationException if $this->value is null.
	 */
	public function get() {

		if (is_null ($this->value))
			throw new UnsupportedOperationException(__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                               ,"No value present");
		return $this->value;
	}


	/**
	 * Returns the hash code value of the present value, if any, or 0 if no value is present.
	 *
	 * @return hash code value of the present value or 0 if no value is present
	 */
	public function hashCode() : int {

		return $this->isPresent() ? $this->value->hashCode() : 0;
	}


	/**
	 * Returns TRUE if there is a value present, otherwise FALSE.
	 *
	 * @return TRUE if there is a value present, otherwise FALSE
	 */
	public function isPresent() : bool {

		return !is_null ($this->value);
	}


	/**
	 * Returns the value if present, otherwise return $other.
	 *
	 * @param $other
	 *    The value to be returned if there is no value present
	 *
	 * @return the value, if present, otherwise $other
	 */
	public function orElse ($other) {

		return !is_null ($this->value) ? $this->value : $other;
	}


	/**
	 * Returns the contained value, if present, otherwise throw the given Exception
	 *
	 * @param Exception $exceptionToThrow
	 *    Exception to be throw if contained value is null
	 *
	 * @return the present value, otherwise throw the given Exception
	 *
	 * @throws Exception
	 */
	public function orElseThrow (\Exception $exceptionToThrow) {

		if (!is_null ($this->value))
			return $this->value;

		throw $exceptionToThrow;
	}

}

?>