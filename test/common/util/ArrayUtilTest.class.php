<?php

namespace FunctionalPHP\test\common\util;

require_once '../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\common\util\ArrayUtil;
use FunctionalPHP\test\Person;
use FunctionalPHP\test\PersonComparator;

/**
 * Class used to test FunctionalPHP\common\util\ArrayTest
 */
final class ArrayUtilTest extends TestCase {

	/**
	 * @covers FunctionalPHP\common\util\ArrayTest::binaryObjectSearch
	 */
	public function testBinaryObjectSearchInEmptyArray() {

		$person = new Person ("John", 18, TRUE);

		$binarySearchResult = ArrayUtil::binaryObjectSearch (array(), $person);
		$this->assertNotNull ($binarySearchResult);
		$this->assertFalse ($binarySearchResult->isElementFound);
		$this->assertEquals (0, $binarySearchResult->position);
	}


	/**
	 * @covers FunctionalPHP\common\util\ArrayTest::binaryObjectSearch
	 */
	public function testBinaryObjectSearchWithNotFoundElement() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Sara", 25, FALSE);
		$person3 = new Person ("Mary", 20, FALSE);
		$person4 = new Person ("Mark", 15, TRUE);

		// Adds elements to the array used to search
		$arrayOfObjects = array ($person1, $person3);

		// Using Person->compareTo (natural Person->name ordination)
		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $person2);
		$this->assertNotNull ($binarySearchResult);
		$this->assertFalse ($binarySearchResult->isElementFound);
		$this->assertEquals (2, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $person4);
		$this->assertNotNull ($binarySearchResult);
		$this->assertFalse ($binarySearchResult->isElementFound);
		$this->assertEquals (1, $binarySearchResult->position);

		// Using PersonComparator (reverse Person->name ordination)
		$arrayOfObjects = array ($person3, $person1);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $person2, new PersonComparator());
		$this->assertNotNull ($binarySearchResult);
		$this->assertFalse ($binarySearchResult->isElementFound);
		$this->assertEquals (0, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $person4, new PersonComparator());
		$this->assertNotNull ($binarySearchResult);
		$this->assertFalse ($binarySearchResult->isElementFound);
		$this->assertEquals (1, $binarySearchResult->position);
	}


	/**
	 * @covers FunctionalPHP\common\util\ArrayTest::binaryObjectSearch
	 */
	public function testBinaryObjectSearchWithFoundElement() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 25, FALSE);
		$person3 = new Person ("Sara", 20, FALSE);

		// Adds elements to the array used to search
		$arrayOfObjects = array ($person1, $person2, $person3);

		// Using Person->compareTo (natural Person->name ordination)
		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $person1);
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (0, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $person2);
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (1, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $person3);
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (2, $binarySearchResult->position);

		// Using PersonComparator (reverse Person->name ordination)
		$arrayOfObjects = array ($person3, $person2, $person1);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $person1, new PersonComparator());
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (2, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $person2, new PersonComparator());
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (1, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $person3, new PersonComparator());
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (0, $binarySearchResult->position);
	}

}

?>