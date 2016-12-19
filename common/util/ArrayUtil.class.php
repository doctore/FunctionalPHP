<?php

namespace FunctionalPHP\common\util;

use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;

final class ArrayUtil {

	/**
	 *    Returns the position in $sortedArrayOfObjects where the given $elementToSearch is stored or
	 * the position where it must be saved in this array.
	 *
	 * @param array $sortedArrayOfObjects
	 *    Sorted vector where search the given element
	 * @param Object $element
	 *    Object to search in $sortedArrayOfObjects
	 * @param Comparator $comparator
	 *
	 * @return BinarySearchResult with the following information:
	 *          - If the given object exists in this array => its position
	 *          - If not exists => the position where it should be stored.
	 */
	public static function binaryObjectSearch (array $sortedArrayOfObjects, Object $elementToSearch
			                                  ,Comparator $comparator = NULL) : BinarySearchResult {
		$lowerLimit  = 0;
		$upperLimit  = count ($sortedArrayOfObjects) - 1;
		$midPosition = 0;

		// Divides the $sortedArrayOfObjects into halves every time
		while ($lowerLimit <= $upperLimit) {

			$midPosition    = floor (($lowerLimit + $upperLimit) / 2);
			$elementToCheck = $sortedArrayOfObjects[$midPosition];

			/**
			 *    Uses the stored comparator if it is not null, the compareTo method of the
			 * current element otherwise
			 */
			$order = isset ($comparator) ? $comparator->compare ($elementToCheck, $elementToSearch)
			                             : $elementToCheck->compareTo ($elementToSearch);

			// Decides the "next half" of this $sortedArrayOfObjects that will be checked
			if ($order < 0)
				$lowerLimit = $midPosition + 1;

			elseif ($order > 0)
				$upperLimit = $midPosition - 1;

			// The position of the given object is found in the $sortedArrayOfObjects
			else
				return new BinarySearchResult (TRUE, $midPosition);
		}
		/**
		 *    The given element does not exists in this $sortedArrayOfObjects and it is greater
		 * than the last checked element of this $sortedArrayOfObjects
		 */
		if (isset ($order) && ($order < 0))
			return new BinarySearchResult (FALSE, $midPosition + 1);

		return new BinarySearchResult (FALSE, $midPosition);
	}

}



/**
 * Class used to return the result of binary search method
 */
final class BinarySearchResult {

	// TRUE if the element was found, FALSE otherwise
	public $isElementFound;

	// Position of the element
	public $position;


	public function __construct (bool $isElementFound, int $position) {

		$this->isElementFound = $isElementFound;
		$this->position       = $position;
	}

}

?>