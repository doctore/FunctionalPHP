<?php

namespace FunctionalPHP\collection\map;

use FunctionalPHP\collection\Iterable;
use FunctionalPHP\collection\set\Set;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;

/**
 *    An object that maps keys to values. A map cannot contain duplicate keys; each key can map to
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
	 *    Returns true if this map contains a mapping for the specified key. More formally, returns
	 * true if and only if this map contains a mapping for a key k such that
	 *
	 *   (key == NULL ? k == NULL : key->equals(k))
	 *
	 * @param mixed $key
	 *    Key whose presence in this map is to be tested
	 *
	 * @return TRUE if this map contains a mapping for the specified key, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the map
	 */
	public function containsKey ($key) : bool;

	/**
	 *    Returns true if this map contains the specified value. More formally, returns TRUE if and
	 * only if this map contains a value v such that
	 *
	 *   value->equals(v)
	 *
	 * @param Object $value
	 *   Value whose presence in this map is to be tested
	 *
	 * @return TRUE if this map contains the specified value, FALSE otherwise
	 */
	public function containsValue (Object $value) : bool;

	/**
	 *    Compares the specified map with this map for equality. Returns TRUE if the two maps
	 * represent the same mappings. More formally, two maps m1 and m2 represent the same mappings
	 * if
	 *
	 *   m1->size() == m2->size()
	 *
	 * and all elements of m1 are contained in m2.
	 *
	 * @param Map $map
	 *    Map to be compared for equality with this map
	 *
	 * @return TRUE if the specified map is equal to this map, FALSE otherwise
	 */
	public function equals (Map $map) : bool;

	/**
	 *    Returns an Optional with the value to which the specified key is mapped or with NULL if
	 * this map contains no mapping for the key.
	 *
	 * @param mixed $key
	 *    Key to search in this map
	 *
	 * @return an Optional with the value to which the specified key is mapped or with NULL otherwise
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the map
	 */
	public function get ($key) : Optional;

	/**
	 * Returns an array set with the keys related with the given value.
	 *
	 * @param Object $value
	 *    Value to search in this map
	 *
	 * @return an array of the keys related with the given value
	 */
	public function getKeys (Object $value) : array;

	/**
	 *    Returns the type of the stored keys of this map. The possible return values are:
	 * Map.KEY_BOOLEAN_TYPE, Map.KEY_NUMERIC_TYPE, Map.KEY_STRING_TYPE or and Object instances
	 * (more concretely the name of the class of the Object instances saved as keys).
	 *
	 * @return the type of the stored keys of this map
	 */
	public function getTypeOfKeys() : string;

	/**
	 * Returns an array of the keys contained in this map.
	 *
	 * @return array of the keys contained in this map
	 */
	public function keys() : array;

	/**
	 *    Associates the specified value with the specified key in this map. If the map previously
	 * contained a mapping for the key, the old value is replaced by the specified value. A map
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
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the map
	 * @throws UnsupportedOperationException if this operation is not supported by this map
	 */
	public function put ($key, Object $value) : Optional;

	/**
	 *    Copies all of the mappings from the specified map to this map. The effect of this call is
	 * equivalent to that of calling put(k, v) on this map once for each mapping from key k to value
	 * v in the specified map.
	 *
	 * @param Map $map
	 *    Mappings to be stored in this map
	 *
	 * @return TRUE if this map changed as a result of the call, FALSE otherwise
	 *
	 * @throws IllegalArgumentException if the type of the keys in the given map is not of the same
	 *         type which is stored in the map
	 * @throws UnsupportedOperationException if this operation is not supported by this map
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
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the map
	 * @throws UnsupportedOperationException if this operation is not supported by this map
	 */
	public function remove ($key, Object $value) : bool;

	/**
	 *    Removes the mapping for a key from this map if it is present. More formally, if this map
	 * contains a mapping from key k to value v such that
	 *
	 * (key == NULL ? k == NULL : key->equals(k))
	 *
	 * that mapping is removed.
	 *
	 * @param mixed $key
	 *    Key whose mapping is to be removed from the map
	 *
	 * @return an Optional with the previous value associated with keyor with NULL if there was no
	 *         mapping for key
	 *
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the map
	 * @throws UnsupportedOperationException if this operation is not supported by this map
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
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the map
	 * @throws UnsupportedOperationException if this operation is not supported by this map
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
	 * @throws IllegalArgumentException if the key is not of the same type which is stored in the map
	 * @throws UnsupportedOperationException if this operation is not supported by this map
	 */
	public function replaceWithNewValue ($key, Object $oldValue, Object $newValue) : bool;

	/**
	 * Returns an array of the values contained in this map.
	 *
	 * @return an array of the values contained in this map
	 */
	public function values() : array;

}

?>