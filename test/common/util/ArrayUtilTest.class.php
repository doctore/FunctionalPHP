<?php

namespace FunctionalPHP\test\common\util;

require_once '../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\common\util\ArrayUtil;
use FunctionalPHP\test\DummyObject;
use FunctionalPHP\test\DummyObjectComparator;

/**
 * Class used to test FunctionalPHP\common\util\ArrayTest
 */
final class ArrayUtilTest extends TestCase {

	/**
	 * @covers FunctionalPHP\common\util\ArrayTest::binaryObjectSearch
	 */
	public function testBinaryObjectSearchInEmptyArray() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$binarySearchResult = ArrayUtil::binaryObjectSearch (array(), $dummyObject);
		$this->assertNotNull ($binarySearchResult);
		$this->assertFalse ($binarySearchResult->isElementFound);
		$this->assertEquals (0, $binarySearchResult->position);
	}


	/**
	 * @covers FunctionalPHP\common\util\ArrayTest::binaryObjectSearch
	 */
	public function testBinaryObjectSearchWithNotFoundElement() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "b", FALSE);
		$dummyObject4 = new DummyObject (4, "b", FALSE);

		// Adds elements to the array used to search
		$arrayOfObjects = array ($dummyObject1, $dummyObject3);

		// Using DummyObject->compareTo (natural DummyObject->intProperty ordination)
		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $dummyObject2);
		$this->assertNotNull ($binarySearchResult);
		$this->assertFalse ($binarySearchResult->isElementFound);
		$this->assertEquals (1, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $dummyObject4);
		$this->assertNotNull ($binarySearchResult);
		$this->assertFalse ($binarySearchResult->isElementFound);
		$this->assertEquals (2, $binarySearchResult->position);

		// Using DummyObjectComparator (reverse DummyObject->intProperty ordination)
		$arrayOfObjects = array ($dummyObject3, $dummyObject1);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $dummyObject2, new DummyObjectComparator());
		$this->assertNotNull ($binarySearchResult);
		$this->assertFalse ($binarySearchResult->isElementFound);
		$this->assertEquals (1, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $dummyObject4, new DummyObjectComparator());
		$this->assertNotNull ($binarySearchResult);
		$this->assertFalse ($binarySearchResult->isElementFound);
		$this->assertEquals (0, $binarySearchResult->position);
	}


	/**
	 * @covers FunctionalPHP\common\util\ArrayTest::binaryObjectSearch
	 */
	public function testBinaryObjectSearchWithFoundElement() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "b", FALSE);

		// Adds elements to the array used to search
		$arrayOfObjects = array ($dummyObject1, $dummyObject2, $dummyObject3);

		// Using DummyObject->compareTo (natural DummyObject->intProperty ordination)
		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $dummyObject1);
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (0, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $dummyObject2);
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (1, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $dummyObject3);
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (2, $binarySearchResult->position);

		// Using DummyObjectComparator (reverse DummyObject->intProperty ordination)
		$arrayOfObjects = array ($dummyObject3, $dummyObject2, $dummyObject1);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $dummyObject1, new DummyObjectComparator());
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (2, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $dummyObject2, new DummyObjectComparator());
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (1, $binarySearchResult->position);

		$binarySearchResult = ArrayUtil::binaryObjectSearch ($arrayOfObjects, $dummyObject3, new DummyObjectComparator());
		$this->assertNotNull ($binarySearchResult);
		$this->assertTrue ($binarySearchResult->isElementFound);
		$this->assertEquals (0, $binarySearchResult->position);
	}

}

?>