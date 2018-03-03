<?php

namespace FunctionalPHP\iterable\map;

use FunctionalPHP\iterable\Iterable;
use FunctionalPHP\iterable\collection\set\Set;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;

/**
 *    An object that maps keys to values. A Map cannot contain duplicate keys; each key can map to
 * at most one value. Besides only four kinds of types are permitted as keys: Map.KEY_BOOLEAN_TYPE,
 * Map.KEY_NUMERIC_TYPE, Map.KEY_STRING_TYPE and Map.KEY_OBJECT_TYPE (Object instances).
 *
 * @see \FunctionalPHP\common\Object
 */
interface Map extends Iterable {

	/**
	 * "Valid types" of the keys
	 */
	const KEY_BOOLEAN_TYPE = "boolean";
	const KEY_NUMERIC_TYPE = "numeric";
	const KEY_OBJECT_TYPE  = "object";
	const KEY_STRING_TYPE  = "string";
	const VALID_KEY_TYPES  = array (Map::KEY_BOOLEAN_TYPE, Map::KEY_NUMERIC_TYPE
			                       ,Map::KEY_OBJECT_TYPE, Map::KEY_STRING_TYPE);


	/**
	 *    If the specified key is not already associated with a value (or is mapped to NULL, attempts
	 * to compute its value using the given mapping function and enters it into this map unless NULL.
	 *
	 * @param mixed $key
	 *    Key to search in this Map
	 * @param \Closure $mappingFunction
	 *    Anonymous function used to calculate the new value related with the given key
	 *
 	 * @return an Optional with the current (existing or computed) value associated with
	 *         the specified key, or with NULL if the computed value is NULL
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the Map
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public function computeIfAbsent ($key, \Closure $mappingFunction) : Optional;

	/**
	 *    If the value for the specified key is present and non-null, attempts to compute a new mapping
	 * given the key and its current mapped value. If the function returns NULL, the mapping is removed
	 *
	 * @param mixed $key
	 *    Key to search in this Map
	 * @param \Closure $remappingFunction
	 *    Anonymous function used to calculate the new value related with the given key
	 *
	 * @return an Optional with the current (existing or computed) value associated with
	 *         the specified key, or with NULL if the computed value is NULL
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the Map
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public function computeIfPresent ($key, \Closure $remappingFunction) : Optional;

	/**
	 *    Returns true if this Map contains a mapping for the specified key. More formally, returns
	 * true if and only if this Map contains a mapping for a key k such that
	 *
	 *   (key == NULL ? k == NULL : key->equals(k))
	 *
	 * @param mixed $key
	 *    Key whose presence in this Map is to be tested
	 *
	 * @return TRUE if this Map contains a mapping for the specified key, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the Map
	 */
	public function containsKey ($key) : bool;

	/**
	 *    Returns true if this Map contains the specified value. More formally, returns TRUE if and
	 * only if this Map contains a value v such that
	 *
	 *   value->equals(v)
	 *
	 * @param Object $value
	 *   Value whose presence in this Map is to be tested
	 *
	 * @return TRUE if this Map contains the specified value, FALSE otherwise
	 */
	public function containsValue (Object $value) : bool;

	/**
	 *    Compares the specified Map with this Map for equality. Returns TRUE if the two maps
	 * represent the same mappings. More formally, two maps m1 and m2 represent the same mappings
	 * if
	 *
	 *   m1->size() == m2->size()
	 *
	 * and all elements of m1 are contained in m2.
	 *
	 * @param Map $map
	 *    Map to be compared for equality with this Map
	 *
	 * @return TRUE if the specified Map is equal to this Map, FALSE otherwise
	 */
	public function equals (Map $map) : bool;

	/**
	 *    Returns an Optional with the value to which the specified key is mapped or with NULL if
	 * this Map contains no mapping for the key.
	 *
	 * @param mixed $key
	 *    Key to search in this Map
	 *
	 * @return an Optional with the value to which the specified key is mapped or with NULL otherwise
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the Map
	 */
	public function get ($key) : Optional;

	/**
	 * Returns an array set with the keys related with the given value.
	 *
	 * @param Object $value
	 *    Value to search in this Map
	 *
	 * @return an array of the keys related with the given value
	 */
	public function getKeys (Object $value) : array;

	/**
	 *    Returns the type of the stored keys of this Map. The possible return values are:
	 * Map.KEY_BOOLEAN_TYPE, Map.KEY_NUMERIC_TYPE, Map.KEY_STRING_TYPE or and Object instances
	 * (more concretely the name of the class of the Object instances saved as keys).
	 *
	 * @return the type of the stored keys of this Map
	 */
	public function getTypeOfKeys() : string;

	/**
	 * Returns an array of the keys contained in this Map.
	 *
	 * @return array of the keys contained in this Map
	 */
	public function keys() : array;

	/**
	 *    Associates the specified value with the specified key in this Map. If the Map previously
	 * contained a mapping for the key, the old value is replaced by the specified value. A Map
	 * m is said to contain a mapping for a key k if and only if
	 *
	 *   m->containsKey(k) == TRUE
	 *
	 * @param mixed $key
	 *   Key with which the specified value is to be associated
	 * @param Object $value
	 *   Value to be associated with the specified key
	 *
	 * @return an Optional with the previous value associated with key or with NULL if there was no
	 *         mapping for key
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the Map
	 * @throws UnsupportedOperationException if this operation is not supported by this Map
	 */
	public function put ($key, Object $value) : Optional;

	/**
	 *    Copies all of the mappings from the specified Map to this Map. The effect of this call is
	 * equivalent to that of calling put(k, v) on this Map once for each mapping from key k to value
	 * v in the specified Map.
	 *
	 * @param Map $map
	 *    Mappings to be stored in this Map
	 *
	 * @return TRUE if this Map changed as a result of the call, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if the type of the keys in the given Map is not of the same
	 *         type which is stored in the Map
	 * @throws UnsupportedOperationException if this operation is not supported by this Map
	 */
	public function putAll (Map $map) : bool;

	/**
	 * Removes the entry for the specified key only if it is currently mapped to the specified value.
	 *
	 * @param mixed $key
	 *    Key with which the specified value is associated
	 * @param Object $value
	 *    Value expected to be associated with the specified key
	 *
	 * @return TRUE if the value was removed, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the Map
	 * @throws UnsupportedOperationException if this operation is not supported by this Map
	 */
	public function remove ($key, Object $value) : bool;

	/**
	 *    Removes the mapping for a key from this Map if it is present. More formally, if this Map
	 * contains a mapping from key k to value v such that
	 *
	 * (key == NULL ? k == NULL : key->equals(k))
	 *
	 * that mapping is removed.
	 *
	 * @param mixed $key
	 *    Key whose mapping is to be removed from the Map
	 *
	 * @return an Optional with the previous value associated with keyor with NULL if there was no
	 *         mapping for key
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the Map
	 * @throws UnsupportedOperationException if this operation is not supported by this Map
	 */
	public function removeByKey ($key) : Optional;

	/**
	 * Replaces the entry for the specified key only if it is currently mapped to some value.
	 *
	 * @param mixed $key
	 *    Key with which the specified value is associated
	 * @param Object $value
	 *    Value to be associated with the specified key
	 *
	 * @return an Optional with the previous value associated with the specified key or with NULL if
	 *         there was no mapping for the key
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the Map
	 * @throws UnsupportedOperationException if this operation is not supported by this Map
	 */
	public function replace ($key, Object $value) : Optional;

	/**
	 * Replaces the entry for the specified key only if currently mapped to the specified old value.
	 *
	 * @param mixed $key
	 *    Key with which the specified value is associated
	 * @param Object $oldValue
	 *    Value expected to be associated with the specified key
	 * @param Object $newValue
	 *    Value to be associated with the specified key
	 *
	 * @return TRUE if the value was replaced, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the Map
	 * @throws UnsupportedOperationException if this operation is not supported by this Map
	 */
	public function replaceWithNewValue ($key, Object $oldValue, Object $newValue) : bool;

	/**
	 * Returns an array of the values contained in this Map.
	 *
	 * @return an array of the values contained in this Map
	 */
	public function values() : array;

}

?>