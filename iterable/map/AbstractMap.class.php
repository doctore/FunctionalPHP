<?php

namespace FunctionalPHP\iterable\map;

use FunctionalPHP\iterable\AbstractIterable;
use FunctionalPHP\iterable\map\Map;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;
use FunctionalPHP\exception\IllegalArgumentException;

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
	 *    Type of the keys stored in this map: Map::KEY_BOOLEAN_TYPE, Map::KEY_NUMERIC_TYPE,
	 *    Map::KEY_STRING_TYPE or Map::KEY_OBJECT_TYPE
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