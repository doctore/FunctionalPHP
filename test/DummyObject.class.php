<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\Object;

/**
 * Class used in the tests related with collections and maps
 */
class DummyObject extends Object {

	protected $intProperty;

	// Not used in hashCode method
	protected $stringProperty;

	// Not used neither equals nor hashCode
	protected $boolProperty;


	public function __construct (int $intValue, string $stringValue, bool $boolValue) {

		$this->intProperty    = $intValue;
		$this->stringProperty = $stringValue;
		$this->boolProperty   = $boolValue;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::equals()
	 */
	public function equals (DummyObject $object): bool {

		return $this->intProperty == $object->intProperty &&
			   $this->stringProperty == $object->stringProperty;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::hashCode()
	 */
	public function hashCode(): int {

		return $this->intProperty % 10;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::compareTo()
	 */
	public function compareTo (Object $object): int {

		return $this->intProperty - $object->intProperty;
	}

}

?>