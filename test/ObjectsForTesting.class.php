<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\Object;
use FunctionalPHP\iterable\collection\lists\Lists;
use FunctionalPHP\iterable\collection\lists\ArrayList;


/**
 * Subclass of Object used only for testing purpose.
 */
class Car extends Object {

	protected $registration;
	protected $yearOfProduction;


	public function __construct (string $registration, int $yearOfProduction) {

		$this->registration = $registration;
		$this->yearOfProduction = $yearOfProduction;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::equals()
	 */
	public function equals (Object $otherCar): bool {
		return strcmp ($this->registration, $otherCar->registration) == 0;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::hashCode()
	 */
	public function hashCode(): int {
		return $this->yearOfProduction % 5;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::compareTo()
	 */
	public function compareTo (Object $otherCar): int {
		return strcmp ($this->registration, $otherCar->registration);
	}

}


/**
 * Subclass of Object used only for testing purpose.
 */
class Person extends Object {

	protected $name;
	protected $age;
	protected $isMale;
	protected $cars;

	public function __construct (string $name, int $age, bool $isMale, Lists $cars = NULL) {
		$this->name   = $name;
		$this->age    = $age;
		$this->isMale = $isMale;

		$this->cars = (is_null ($cars) ? new ArrayList() : $cars);
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::equals()
	 */
	public function equals (Object $otherPerson): bool {

		return (strcmp ($this->name, $otherPerson->name) == 0) &&
		       $this->age == $otherPerson->age;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::hashCode()
	 */
	public function hashCode(): int {
		return $this->age % 5;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::compareTo()
	 */
	public function compareTo (Object $otherPerson): int {
		return strcmp ($this->name, $otherPerson->name);
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::__toString()
	 */
	public function __toString() {

		$resultString = "{".get_class ($this).": ";

		$properties = get_object_vars ($this);
		if (is_array ($properties)) {

			// Adds property name and property value to the result
			foreach ($properties as $name => $value) {

				$resultString .= "\n {$name} = ";

				if (strcmp ($name, "cars") == 0) {

					$resultString .= "[";
					if (!is_null ($value)) {
						foreach ($value as $car)
							$resultString .= $car."\n";
					}
					$resultString .= "]";
				}
				else
					$resultString .= $value;
			}
		}
		return $resultString."}";
	}

}

?>