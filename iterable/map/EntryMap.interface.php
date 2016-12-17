<?php

namespace FunctionalPHP\iterable\map;

use FunctionalPHP\common\Object;

/**
 *    A map entry (key-value pair). The Map.iterator method returns a collection-view of the map,
 * whose elements are of this class. Besides only four kinds of types are permitted as keys:
 * Map::KEY_BOOLEAN_TYPE, Map::KEY_NUMERIC_TYPE, Map::KEY_STRING_TYPE and Map::KEY_OBJECT_TYPE (Object
 * instances).
 *
 * @see \FunctionalPHP\common\Object
 */
interface EntryMap {

	/**
	 *    Compares the specified entryMap with this entry for equality. Returns true if the given
	 * object is also a map entry and the two entries represent the same mapping. More formally,
	 * two entries e1 and e2 represent the same mapping if
	 *
	 * (e1->getKey() == NULL ? e2->getKey() == NULL : e1->getKey().equals (e2->getKey())) &&
	 * (e1->getValue() == NULL ? e2->getValue() == NULL : e1->getValue().equals (e2->getValue()))
	 *
	 * @param EntryMap $entryMap
	 *    Object to be compared for equality with this map entry
	 *
	 * @return true if the specified object is equal to this map entry
	 */
	public function equals (EntryMap $entryMap) : bool;


	/**
	 *    Compares the specified entryMap with this entry for equality of the keys. Returns TRUE if
	 * the key of the given object is also the key of the map entry. More formally, two entries e1
	 * and e2 has the same key if
	 *
	 * (e1->getKey() == NULL ? e2->getKey() == NULL : e1->getKey().equals (e2->getKey()))
	 *
	 * @param EntryMap $entryMap
	 *    Object whose key will be compared with the key of this map entry
	 *
	 * @return TRUE if the key of specified object is equal to the key of this map entry
	 */
	public function equalsKey (EntryMap $entryMap) : bool;


	/**
	 * Returns the key corresponding to this entry.
	 *
	 * @return the key corresponding to this entry
	 */
	public function getKey();


	/**
	 * Replaces the value corresponding to this entry with the specified value.
	 *
	 * @param Object $value
	 *    New value to be stored in this entry
	 *
	 * @throws UnsupportedOperationException if this operation is not supported by this map
	 */
	public function setValue (Object $value);


	/**
	 * Returns the value corresponding to this entry
	 *
	 * @return the value corresponding to this entry
	 */
	public function getValue() : Object;


	/**
	 *    Returns the hash code value for this map entry. The hash code of a map entry e is defined
	 * to be:
	 *
	 *   (e->getKey() == NULL ? 0 : e->getKey()->hashCode()) +
	 *   (e->getValue() == NULL ? 0 : e->getValue()->hashCode())
	 *
	 * @return the hash code value for this map entry
	 */
	public function hashCode() : int;


	/**
	 *    Returns the hash code value for the key of this map entry. The hash code of the key of a
	 * map entry e is defined to be:
	 *
	 *   (e->getKey() == NULL ? 0 : e->getKey()->hashCode())
	 *
	 * @return the hash code value for the key of this map entry
	 */
	public function hashCodeKey() : int;

}

?>