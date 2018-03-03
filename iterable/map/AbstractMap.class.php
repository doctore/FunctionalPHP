<?php

namespace FunctionalPHP\iterable\map;

use FunctionalPHP\iterable\AbstractIterable;
use FunctionalPHP\iterable\map\Map;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;
use FunctionalPHP\common\util\ReflectionUtil;
use FunctionalPHP\exception\IllegalArgumentException;
use FunctionalPHP\exception\UnsupportedOperationException;

/**
 *    This class provides a skeletal implementation of the Map interface, to minimize the effort
 * required to implement this interface.
 */
abstract class AbstractMap extends AbstractIterable implements Map {

	/**
	 * The array buffer into which the elements of the Map are stored
	 */
	protected $internalData;

	/**
	 *    Type of the keys stored in this map. Only four kinds of types are permitted as keys:
	 * Map.KEY_BOOLEAN_TYPE, Map.KEY_NUMERIC_TYPE, Map.KEY_STRING_TYPE and Map.KEY_OBJECT_TYPE (Object
	 * instances).
	 */
	protected $typeOfKeys;


	/**
	 * Constructs a skeletal implementation of a Map.
	 *
	 * @param array $internalData
	 *    Array buffer into which the elements are stored
	 * @param string $typeOfKeys
	 *    Type of the keys stored in this Map: Map::KEY_BOOLEAN_TYPE, Map::KEY_NUMERIC_TYPE,
	 *                                         Map::KEY_STRING_TYPE or Map::KEY_OBJECT_TYPE
	 *
	 * @throws IllegalArgumentException if $typeOfKeys has not a valid value.
	 */
	public function __construct (array $internalData, string $typeOfKeys) {

		if (!in_array ($typeOfKeys, Map::VALID_KEY_TYPES))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given type of keys: {$typeOfKeys} is not valid. "
					                               ."Please use one of the following: "
					                               .var_export (Map::VALID_KEY_TYPES, TRUE));
		$this->internalData = $internalData;
		$this->typeOfKeys   = $typeOfKeys;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::equals()
	 */
	abstract public function equals (Map $map) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::get()
	 */
	abstract public function get ($key) : Optional;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::put()
	 */
	abstract public function put ($key, Object $value) : Optional;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::remove()
	 */
	abstract public function remove ($key, Object $value) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::removeByKey()
	 */
	abstract public function removeByKey ($key) : Optional;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::replace()
	 */
	abstract public function replace ($key, Object $value) : Optional;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::replaceWithNewValue()
	 */
	abstract public function replaceWithNewValue ($key, Object $oldValue, Object $newValue) : bool;


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\iterable\map\Map::computeIfAbsent()
	 */
	public function computeIfAbsent ($key, \Closure $mappingFunction) : Optional {

		// Checks the given key
		if ($this->typeOfKeys !== $this->getTypeOfKey ($key))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The type of the given key: ".$this->getTypeOfKey ($key)
					                            ." is not the same as the type of the keys stored in "
					                            ." this map: ".$this->typeOfKeys);

		$this->checkClosureFunctionOfComputeIf ($mappingFunction);

		$existingValue = $this->get ($key);
		if (!$existingValue->isPresent()) {

			$newValue = $mappingFunction ($key);
			if ($newValue != NULL)
				return $this->put ($key, $newValue);
		}
		return $existingValue;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\iterable\map\Map::computeIfPresent()
	 */
	public function computeIfPresent ($key, \Closure $remappingFunction) : Optional {

		// Checks the given key
		if ($this->typeOfKeys !== $this->getTypeOfKey ($key))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The type of the given key: ".$this->getTypeOfKey ($key)
					                            ." is not the same as the type of the keys stored in "
					                            ." this map: ".$this->typeOfKeys);

		$this->checkClosureFunctionOfComputeIf ($remappingFunction);

		$existingValue = $this->get ($key);
		if ($existingValue->isPresent()) {

			$newValue = $remappingFunction ($key);
			if ($newValue != NULL)
				return $this->put ($key, $newValue);
			else
				return $this->removeByKey ($key);
		}
		return $existingValue;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::clear()
	 */
	public function clear() {

		$this->internalData = array();
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::containsKey()
	 */
	public function containsKey ($key) : bool {

		return $this->get($key)->isPresent();
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::containsValue()
	 */
	public function containsValue (Object $value) : bool {

		foreach ($this->iterator() as $internalValue) {

			if ($internalValue->equals ($value))
				return TRUE;
		}
		return FALSE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::getKeys()
	 */
	public function getKeys (Object $value) : array {

		$arrayOfKeys = array();
		foreach ($this->iterator() as $internalKey => $internalValue) {

			if ($internalValue->equals ($value))
				$arrayOfKeys[] = $internalKey;
		}
		return $arrayOfKeys;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::getTypeOfKeys()
	 */
	public function getTypeOfKeys() : string {

		return $this->typeOfKeys;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::keys()
	 */
	public function keys() : array {

		$arrayOfKeys = array();
		foreach ($this->iterator() as $internalKey => $internalValue)
			$arrayOfKeys[] = $internalKey;

		return $arrayOfKeys;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::putAll()
	 */
	public function putAll (Map $map) : bool {

		// Checks the given key
		if ($this->typeOfKeys != $map->getTypeOfKeys())
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The type of this map: {$this->typeOfKeys} is not "
					                               ."the same as the type of the keys of the given map: "
					                               .$map->getTypeOfKeys());
		if ($map->isEmpty())
			return FALSE;

		foreach ($map->iterator() as $externalKey => $externalValue)
			$this->put ($externalKey, $externalValue);

		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::values()
	 */
	public function values() : array {

		$arrayOfValues = array();
		foreach ($this->iterator() as $internalValue)
			$arrayOfValues[] = $internalValue;

		return $arrayOfValues;
	}


	/**
	 *    Returns the type of the given key. It must be one of the following: Map::KEY_BOOLEAN_TYPE,
	 * Map::KEY_NUMERIC_TYPE, Map::KEY_STRING_TYPE and Map::KEY_OBJECT_TYPE (Object instances).
	 *
	 * @param mixed $key
	 *    Key to check
	 *
	 * @return the type of the given key, NULL if it does not belongs to the permitted types
	 */
	protected function getTypeOfKey ($key) {

		if (is_int ($key) || is_float ($key))
			return Map::KEY_NUMERIC_TYPE;

		if (is_string ($key))
			return Map::KEY_STRING_TYPE;

		if ($key instanceof Object)
			return Map::KEY_OBJECT_TYPE;

		if (is_bool ($key))
			return Map::KEY_BOOLEAN_TYPE;

		return NULL;
	}


	/**
	 * Checks if the given string representation of a type is equivalent with the key's type stored in the Map
	 *
	 * @param string $typeToCheck
	 *    Type to check
	 *
	 * @return TRUE is the given type is equivalent to the key's type, FALSE otherwise.
	 */
	protected function isGivenTypeEquivalentToTypeOfKey (string $typeToCheck) : bool {

		if (empty ($typeToCheck))
			return FALSE;

		if ($this->typeOfKeys == Map::KEY_NUMERIC_TYPE)
			return (strcmp ($typeToCheck, "int") == 0 || strcmp ($typeToCheck, "integer") == 0 ||
					strcmp ($typeToCheck, "float") == 0 || strcmp ($typeToCheck, "double") == 0);

		if ($this->typeOfKeys == Map::KEY_STRING_TYPE)
			return (strcmp ($typeToCheck, "string") == 0);

		if ($this->typeOfKeys == Map::KEY_OBJECT_TYPE)
			return (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($typeToCheck, Object::class));

		if ($this->typeOfKeys == Map::KEY_BOOLEAN_TYPE)
			return (strcmp ($typeToCheck, "bool") == 0 || strcmp ($typeToCheck, "boolean") == 0);

		return TRUE;
	}


	/**
	 *    Checks if the given closure used in computeIfAbsent and computeIfPresent functions verify
	 * the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to the type of keys stored in the map
	 *   3. The returned type is not empty and valid (a subclass of Object).
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureFunctionOfComputeIf (\Closure $closureFunction) {

		// Gets information about the given closure (returned type, types of the parameters, etc)
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure ($closureFunction);

		// 1. Only has one parameter
		if ($reflectionFunctionInformation->numberOfParameters != 1)
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The given closure function has ".$reflectionFunctionInformation->numberOfParameters
					                                 ." parameters, however only one is allowed");

		// 2. The type of this unique parameter must be equal to the type of keys stored in the map
		$parameterType = $reflectionFunctionInformation->typesOfParameters[0];

		if (!$this->isGivenTypeEquivalentToTypeOfKey ($parameterType))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
							                        ,"In the given closure function, the type of its parameter: ".$parameterType
					                                  ." is not equal (or a subclass) to the elements stored in the Map: ".$this->typeOfKeys);

		// 3. The returned type is not empty and valid (a subclass of Object)
		$returnType = $reflectionFunctionInformation->typeOfReturnedValue;

		if (empty ($returnType))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The returned type of the given closure function can not be null or empty");
		// Test if it is a subclass of Object
		if (!ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($returnType, Object::class))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
									                ,"The returned type of the given closure function: ".$returnType." is not "
									                 ."valid. Please use a subclass of ".Object::class);
	}

}


/**
 *    A Map entry (key-value pair). The Map->iterator method returns a collection-view of every Map
 * whose elements are of this class. Besides only four kinds of types are permitted as keys:
 * Map::KEY_BOOLEAN_TYPE, Map::KEY_NUMERIC_TYPE, Map::KEY_STRING_TYPE and Map::KEY_OBJECT_TYPE (Object
 * instances).
 */
abstract class AbstractEntryMap implements EntryMap {

	// Key of the current EntryMap
	protected $key;

	// Value of the current EntryMap
	protected $value;


	public function __construct ($key, Object $value = NULL) {

		$this->key = $key;
		$this->value = $value;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\EntryMap::equals()
	 */
	abstract public function equals (EntryMap $entryMap) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\EntryMap::equalsKey()
	 */
	abstract public function equalsKey (EntryMap $entryMap) : bool;


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\EntryMap::getKey()
	 */
	public function getKey() {

		return $this->key;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\EntryMap::getValue()
	 */
	public function getValue(): Object {

		return $this->value;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\EntryMap::setValue()
	 */
	public function setValue (Object $value) {

		$this->value = $value;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\EntryMap::hashCode()
	 */
	public function hashCode(): int {

		return $this->hashCodeKey() + $this->value->hashCode();
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\EntryMap::hashCodeKey()
	 *
	 * @throws IllegalArgumentException if the type of the current $key is not valid.
	 */
	public function hashCodeKey(): int {

		if (is_int ($this->key) || is_float ($this->key) || is_bool ($this->key))
			return (int) $this->key;

		if (is_string ($this->key))
			return (strlen ($this->key) + ord ($this->key[0]));

		if ($this->key instanceof Object)
			return $this->key->hashCode();

		throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
							               ,"The type of the current key: ".gettype ($this->key)
							                   ." is not valid. Please use one of the following: "
							                   .var_export (Map::VALID_KEY_TYPES, TRUE));
	}

}


?>