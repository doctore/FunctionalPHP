<?php

namespace FunctionalPHP\common;

use FunctionalPHP\common\Comparable;
use FunctionalPHP\exception\IllegalArgumentException;

/**
 * Root class of the hierarchy, that is, every new class should have this as superclass
 */
abstract class Object implements Comparable {


	/**
	 * Gets the value of the given property name.
	 *
	 * IMPORTANT:
	 * In classes that inherit of this class, this method does not work for private properties.
	 *
	 * @param string $propertyName
	 *    Property name to search
	 *
	 * @return the value of the given property if exists, an exception otherwise
	 *
	 * @throws IllegalArgumentException if the property does not exists in the class
	 */
	public function __get ($propertyName) {

		if ($this->isAValidProperty ($propertyName))
			return $this->$propertyName;

		throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
				                           ,"The class: ".get_class ($this)." has not contain the "
				                            ."property: ".$propertyName);
	}


	/**
	 * Used to know if the given property is set and is not NULL.
	 *
	 * IMPORTANT:
	 * In classes that inherit of this class, this method does not work for private properties.
	 *
	 * @param string $propertyName
	 *    Property name to check
	 *
	 * @return TRUE if a the property is set and is not NULL, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if the property does not exists in the class
	 */
	public function __isset ($propertyName) : bool {

		if ($this->isAValidProperty ($propertyName))
			return isset ($this->$propertyName);

		throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
				                           ,"The class: ".get_class ($this)." has not contain the "
				                            ."property: ".$propertyName);
	}


	/**
	 * Saves the given value in the property passed as parameter.
	 *
	 * IMPORTANT:
	 * In classes that inherit of this class, this method does not work for private properties.
	 *
	 * @param string $propertyName
	 *    Property name to update
	 * @param $value New
	 *    Value to the given property
	 *
	 * @throws IllegalArgumentException if the property does not exists in the class
	 */
	public function __set ($propertyName, $value) {

		if ($this->isAValidProperty ($propertyName))
			$this->$propertyName = $value;
		else
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
				                               ,"The class: ".get_class ($this)." has not contain the "
				                                ."property: ".$propertyName);
	}


	/**
	 * Returns the "string representation" of the current object.
	 *
	 * IMPORTANT:
	 * In classes that inherit of this class, this method does not work for private properties.
	 *
	 * @return "string representation" of the current object.
	 */
	public function __toString() {

		$resultString = "{".get_class ($this).": ";

		$properties = get_object_vars ($this);
		if (is_array ($properties)) {

			// Adds property name and property value to the result
			foreach ($properties as $name => $value)
				$resultString .= "\n {$name} = ".$value;
		}
		return $resultString."}";
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Comparable::compareTo()
	 */
	public function compareTo (Object $object) : int {

		if (get_class ($this) != get_class ($object))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The class of the current object: ".get_class ($this)
					                           ." is not comparable with the class of the given "
					                           ."object: ".get_class ($object));
		if ($this->equals ($object))
			return 0;

		return $this->hashCode() > $object->hashCode() ? 1 : -1;
	}


	/**
	 * Indicates whether some other object is "equal to" this one.
	 *
	 * @param Object $object
	 *    The reference object with which to compare
	 *
	 * @return TRUE if this object is the same as the obj argument, FALSE otherwise
	 */
	public function equals (Object $object) : bool {

		return $this === $object;
	}


	/**
	 *    Returns a hash code value for the object. This method is supported for the benefit of hash
	 * structures.
	 *
	 * @return the hash code value for this object
	 */
	public function hashCode() : int {

		return crc32 (spl_object_hash ($this));
	}


	/**
	 * Checks if the given property name exists in the class.
	 *
	 * @param string $propertyName
	 *    Property name to search
	 *
	 * @return TRUE if the given property exists, FALSE otherwise
	 */
	protected function isAValidProperty ($propertyName) : bool {

		return property_exists (get_class ($this), $propertyName) == TRUE ? TRUE : FALSE;
	}

}

?>