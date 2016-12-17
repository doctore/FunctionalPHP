<?php

namespace FunctionalPHP\collection\map;

use FunctionalPHP\collection\map\EntryMap;
use FunctionalPHP\collection\map\AbstractEntryMap;
use FunctionalPHP\collection\map\AbstractMap;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;
use FunctionalPHP\exception\IllegalArgumentException;

/**
 * Hash table based implementation of the Map interface.
 *
 *    The array buffer into which the elements of the HashMap are stored. It is important to know
 * that it is a matrix on which the first level stores the different hash codes of the saved
 * elements and the elements with the same hash code at the second one. For example:
 *
 *    $internalData[12][0] = object1  (object with a hash code = 12)
 *    $internalData[12][1] = object2  (object with a hash code = 12)
 *    $internalData[36][0] = object3  (object with a hash code = 36)
 */
class HashMap extends AbstractMap {

	/**
	 * Constructs a map containing the elements of the specified map.
	 *
	 * @param string $typeOfKeys
	 *    Type of the keys stored in this map: Map::KEY_BOOLEAN_TYPE, Map::KEY_NUMERIC_TYPE,
	 *    Map::KEY_STRING_TYPE or Map::KEY_OBJECT_TYPE
	 * @param Map $map
	 *    The map whose elements are to be placed into this map
	 *
	 * @throws IllegalArgumentException if $typeOfKeys has not a valid value.
	 */
	public function __construct (string $typeOfKeys, Map $map = NULL) {

		parent::__construct (array(), $typeOfKeys);

		// Adds the given map to the current map
		if (!is_null ($map))
			$this->putAll ($map);
	}


	/**
	 * Free the resources used by this set
	 */
	function __destruct() {

		unset ($this->internalData);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::equals()
	 */
	public function equals (Map $map) : bool {

		if ($this === $map)
			return TRUE;

		if ($this->size() != $map->size())
			return FALSE;

		if ($this->typeOfKeys != $map->getTypeOfKeys())
			return FALSE;

		// Compares every key-value of this map in the given map
		foreach ($this->iterator() as $internalKey => $internalValue) {

			$externalValue = $map->get ($internalKey);
			if (!$externalValue->isPresent())
				return FALSE;

			if (!$internalValue->equals ($externalValue->get()))
				return FALSE;
		}
		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::get()
	 */
	public function get ($key) : Optional {

		// Checks the given key
		if ($this->typeOfKeys !== $this->getTypeOfKey ($key))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The type of the given key: ".$this->getTypeOfKey ($key)
					                            ." is not the same as the type of the keys stored in "
					                            ." this map: ".$this->typeOfKeys);
		$entryMap = new EntryHashMap ($key);
		$entryMapHashCodeKey = $entryMap->hashCodeKey();

		// If it does not the first element with that hash code
		if (isset ($this->internalData[$entryMapHashCodeKey])) {

			foreach ($this->internalData[$entryMapHashCodeKey] as $internalEntryMap) {


				// If this map contains an equal key => the related value will be returned
				if ($internalEntryMap->equalsKey ($entryMap))
					return new Optional ($internalEntryMap->getValue());
			}
		}
		return new Optional (NULL);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::hashCode()
	 */
	public function hashCode() : int {

		$result = 0;
		foreach ($this->iterator() as $internalKey => $internalValue) {

			$internalEntryMap = new EntryHashMap ($internalKey, $internalValue);
			$result += $internalEntryMap->hashCode();
		}
		return $result;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::iterator()
	 */
	public function iterator() : \Iterator {

		return new HashMapIterator ($this->internalData);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::put()
	 */
	public function put ($key, Object $value) : Optional {

		// Checks the given key
		if ($this->typeOfKeys !== $this->getTypeOfKey ($key))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The type of the given key: ".$this->getTypeOfKey ($key)
					                            ." is not the same as the type of the keys stored in "
					                            ." this map: ".$this->typeOfKeys);

		$entryMap = new EntryHashMap ($key, $value);
		$entryMapHashCodeKey = $entryMap->hashCodeKey();

		// If it is the first element with that hash code
		if (!isset ($this->internalData[$entryMapHashCodeKey]))
			$this->internalData[$entryMapHashCodeKey] = array ($entryMap);

		else {
			foreach ($this->internalData[$entryMapHashCodeKey] as &$internalEntryMap) {

				/**
				 *    If this map contains an equal key => the entire entrymap it will be replaced
				 * by the given one
				 */
				if ($internalEntryMap->equalsKey ($entryMap)) {

					$previousValue    = $internalEntryMap->getValue();
					$internalEntryMap = $entryMap;

					return new Optional ($previousValue);
				}
			}
			// The given element does not exists in this map
			$this->internalData[$entryMapHashCodeKey][] = $entryMap;
		}
		return new Optional (NULL);
	}



	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::remove()
	 */
	public function remove ($key, Object $value) : bool {

		$entryMap = new EntryHashMap ($key, $value);
		$entryMapHashCodeKey = $entryMap->hashCodeKey();

		// If it is the first element with that hash code
		if (!isset ($this->internalData[$entryMapHashCodeKey]))
			return FALSE;

		foreach ($this->internalData[$entryMapHashCodeKey] as $internalKey => $internalEntryMap) {

			// The given element exists in the map
			if ($internalEntryMap->equals ($entryMap)) {

				array_splice ($this->internalData[$entryMapHashCodeKey], $internalKey, 1);

				// There are not more elements with the same hash code
				if (count ($this->internalData[$entryMapHashCodeKey]) == 0)
					unset ($this->internalData[$entryMapHashCodeKey]);

				return TRUE;
			}
		}
		return FALSE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::removeByKey()
	 */
	public function removeByKey ($key) : Optional {

		// Checks the given key
		if ($this->typeOfKeys !== $this->getTypeOfKey ($key))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The type of the given key: ".$this->getTypeOfKey ($key)
					                            ." is not the same as the type of the keys stored in "
					                            ." this map: ".$this->typeOfKeys);

		$entryMap = new EntryHashMap ($key);
		$entryMapHashCodeKey = $entryMap->hashCodeKey();

		// If it does not the first element with that hash code
		if (isset ($this->internalData[$entryMapHashCodeKey])) {

			foreach ($this->internalData[$entryMapHashCodeKey] as $internalKey => $internalEntryMap) {

				// If this map contains an equal key => the entire entry map will be removed
				if ($internalEntryMap->equalsKey ($entryMap)) {

					array_splice ($this->internalData[$entryMapHashCodeKey], $internalKey, 1);

					// There are not more elements with the same hash code
					if (count ($this->internalData[$entryMapHashCodeKey]) == 0)
						unset ($this->internalData[$entryMapHashCodeKey]);

					return new Optional ($internalEntryMap->getValue());
				}
			}
		}
		return new Optional (NULL);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::replace()
	 */
	public function replace ($key, Object $value) : Optional {

		// Checks the given key
		if ($this->typeOfKeys !== $this->getTypeOfKey ($key))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The type of the given key: ".$this->getTypeOfKey ($key)
					                            ." is not the same as the type of the keys stored in "
					                            ." this map: ".$this->typeOfKeys);

		$entryMap = new EntryHashMap ($key, $value);
		$entryMapHashCodeKey = $entryMap->hashCodeKey();

		// If it does not the first element with that hash code
		if (isset ($this->internalData[$entryMapHashCodeKey])) {

			foreach ($this->internalData[$entryMapHashCodeKey] as &$internalEntryMap) {

				// If this map contains an equal key => the related value will be returned
				if ($internalEntryMap->equalsKey ($entryMap)) {

					$previousValue    = $internalEntryMap->getValue();
					$internalEntryMap = $entryMap;

					return new Optional ($previousValue);
				}
			}
		}
		return new Optional (NULL);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\Map::replaceWithNewValue()
	 */
	public function replaceWithNewValue ($key, Object $oldValue, Object $newValue) : bool {

		// Checks the given key
		if ($this->typeOfKeys !== $this->getTypeOfKey ($key))
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The type of the given key: ".$this->getTypeOfKey ($key)
					                            ." is not the same as the type of the keys stored in "
					                            ." this map: ".$this->typeOfKeys);

		$entryMap = new EntryHashMap ($key, $oldValue);
		$entryMapHashCodeKey = $entryMap->hashCodeKey();

		// If it does not the first element with that hash code
		if (isset ($this->internalData[$entryMapHashCodeKey])) {

			foreach ($this->internalData[$entryMapHashCodeKey] as &$internalEntryMap) {

				// If this map contains an equal key => the related value will be returned
				if ($internalEntryMap->equals ($entryMap)) {

					$internalEntryMap->setValue ($newValue);
					return TRUE;
				}
			}
		}
		return FALSE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Iterable::size()
	 */
	public function size() : int {

		return (count ($this->internalData, COUNT_RECURSIVE ) - count ($this->internalData));
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
	private function getTypeOfKey ($key) {

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

}



/**
 * Class used to iterate over elements of a HashMap
 */
class HashMapIterator implements \Iterator {

	/**
	 * The array buffer into which the elements of the HashMap are stored
	 */
	private $internalData;

	/**
	 * The array buffer into which the elements of the HashMap with the same hash code are stored
	 */
	private $internalDataSecondLevel;


	public function __construct (array $internalData) {

		$this->internalData = $internalData;
	}


	/**
	 * Returns the current element of the internal array.
	 *
	 * @return the current element of the internal array
	 *
	 * @see Iterator::current()
	 */
	public function current() {

		$entryMap = $this->currentEntryMap();
		return ($entryMap == NULL ? FALSE : $entryMap->getValue());
	}


	/**
	 * Returns the key of the current element of the internal array.
	 *
	 * @return the key of the current element of the internal array
	 *
	 * @see Iterator::key()
	 */
	public function key() {

		$entryMap = $this->currentEntryMap();
		return ($entryMap == NULL ? NULL : $entryMap->getKey());
	}


	/**
	 * Returns the next element of the internal array.
	 *
	 * @return the next element of the internal array
	 *
	 * @see Iterator::next()
	 */
	public function next() {

		$currentElement = next ($this->internalDataSecondLevel);

		// If there is no elements with the "current hash code" => get the "next hash code"
		if (!$currentElement) {

			$nextHashCode = next ($this->internalData);
			if (!$nextHashCode)
				return FALSE;

			/**
			 *    Now we need to move the internal pointer to the "first elements" of the
			 * "next hash code" (now "the current")
			 */
			$this->internalDataSecondLevel = current ($this->internalData);
			reset ($this->internalDataSecondLevel);

			return $this->current();
		}
	}


	/**
	 * Returns the first element of the internal array.
	 *
	 * @return the first element of the internal array
	 *
	 * @see Iterator::rewind()
	 */
	public function rewind() {

		reset ($this->internalData);
	}


	/**
	 * Returns TRUE if the current element of the internal array is a valid object, FALSE otherwise.
	 *
	 * @return TRUE if the current element of the internal array is a valid object, FALSE otherwise
	 *
	 * @see Iterator::valid()
	 */
	public function valid() {

		return ($this->currentEntryMap() != NULL);
	}


	/**
	 * Returns the current element in this HashMap on which we are iterating
	 *
	 * @return the current element of this HashMap
	 */
	private function currentEntryMap() {

		/**
		 *    If they are not elements with the "previous hash code" => gets the elements of "the
		 * current hash code"
		 */
		if (!isset ($this->internalDataSecondLevel)) {
			$this->internalDataSecondLevel = current ($this->internalData);

			/**
			 *    The internal pointer should point to the first element of the array used to store
			 * the objects with the same hash code.
			 */
			if (is_array ($this->internalDataSecondLevel))
				reset ($this->internalDataSecondLevel);
		}
		/**
		 *    If there are not elements with "the current hash code" => returns FALSE.  The current
		 * element otherwise
		 */
		return (!$this->internalDataSecondLevel ? NULL
				                                : current ($this->internalDataSecondLevel));
	}

}



/**
 *    A HashMap entry (key-value pair). The HashMap->iterator method returns a collection-view of this
 * HashMap whose elements are of this class. Besides only four kinds of types are permitted as keys:
 * Map::KEY_BOOLEAN_TYPE, Map::KEY_NUMERIC_TYPE, Map::KEY_STRING_TYPE and Map::KEY_OBJECT_TYPE (Object
 * instances).
 */
class EntryHashMap extends AbstractEntryMap {

	/**
	 * Invokes the parent's construct to store the given information
	 */
	public function __construct ($key, Object $value = NULL) {

		parent::__construct ($key, $value);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\EntryMap::equals()
	 */
	public function equals (EntryMap $entryMap) : bool {

		return ($this->equalsKey ($entryMap) && $this->value->equals ($entryMap->getValue()));
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\map\EntryMap::equalsKey()
	 */
	public function equalsKey (EntryMap $entryMap): bool {

		if ($this === $entryMap)
			return TRUE;

		if (__CLASS__ != get_class ($entryMap))
			return FALSE;

		if ($this->key instanceof Object && $entryMap->getKey() instanceof Object)
			return ($this->key->equals ($entryMap->getKey()));

		return ($this->key === $entryMap->getKey());
	}

}

?>