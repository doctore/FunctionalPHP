<?php

namespace FunctionalPHP\test\iterable\map;

require_once '../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;
use FunctionalPHP\iterable\map\EntryHashMap;
use FunctionalPHP\iterable\map\HashMap;
use FunctionalPHP\iterable\map\Map;
use FunctionalPHP\test\DummyObject;

/**
 * Class used to test FunctionalPHP\collection\map\HashMap
 */
final class HashMapTest extends TestCase {


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::__construct
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testConstructorWithInvalidTypeOfKeys() {

		$hashMap = new HashMap ("InvalidTypeOfKeys");
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::__construct
	 */
	public function testCreateEmptyHashMap() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::__construct
	 */
	public function testCreateNotEmptyHashMap() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap1 = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap1->put ($dummyObject1->stringProperty, $dummyObject1);
		$hashMap1->put ($dummyObject2->stringProperty, $dummyObject2);
		$hashMap1->put ($dummyObject3->stringProperty, $dummyObject3);

		$hashMap2 = new HashMap (Map::KEY_STRING_TYPE, $hashMap1);
		$this->assertFalse ($hashMap2->isEmpty());
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::clear
	 */
	public function testClearHashMap() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());

		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (3, $hashMap->size());

		$hashMap->clear();
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithNumericKeysStoredAndGivenStringKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($dummyObject->intProperty, $dummyObject);

		$hashMap->containsKey ($dummyObject->stringProperty);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithNumericKeysStoredAndGivenBoolKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($dummyObject->intProperty, $dummyObject);

		$hashMap->containsKey ($dummyObject->boolProperty);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithNumericKeysStoredAndGivenObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($dummyObject->intProperty, $dummyObject);

		$hashMap->containsKey ($dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithStringKeysStoredAndGivenBoolKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->put ($dummyObject->stringProperty, $dummyObject);

		$hashMap->containsKey ($dummyObject->boolProperty);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithStringKeysStoredAndGivenObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->put ($dummyObject->stringProperty, $dummyObject);

		$hashMap->containsKey ($dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithBooleanKeysStoredAndGivenObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap->put ($dummyObject->boolProperty, $dummyObject);

		$hashMap->containsKey ($dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsKey
	 */
	public function testContainsNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->intProperty));

		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->intProperty));

		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->intProperty));

		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->intProperty));

		// Checks keys with float keys
		$floatConverter = 0.1;

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->containsKey ($dummyObject1->intProperty * $floatConverter));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->intProperty * $floatConverter));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->intProperty * $floatConverter));

		$hashMap->put ($dummyObject1->intProperty * $floatConverter, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->intProperty * $floatConverter));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->intProperty * $floatConverter));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->intProperty * $floatConverter));

		$hashMap->put ($dummyObject2->intProperty * $floatConverter, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->intProperty * $floatConverter));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->intProperty * $floatConverter));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->intProperty * $floatConverter));

		$hashMap->put ($dummyObject3->intProperty * $floatConverter, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->intProperty * $floatConverter));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->intProperty * $floatConverter));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->intProperty * $floatConverter));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsKey
	 */
	public function testContainsStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->stringProperty));

		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->stringProperty));

		$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->stringProperty));

		$hashMap->put ($dummyObject3->stringProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->stringProperty));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsKey
	 */
	public function testContainsBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->containsKey ($dummyObject1->boolProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->boolProperty));

		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->boolProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->boolProperty));

		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->boolProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->boolProperty));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsKey
	 */
	public function testContainsObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->containsKey ($dummyObject1));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3));

		$hashMap->put ($dummyObject1, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3));

		$hashMap->put ($dummyObject2, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3));

		$hashMap->put ($dummyObject3, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::containsValue
	 */
	public function testContainsValue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertFalse ($hashMap->containsValue ($dummyObject3));

		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertFalse ($hashMap->containsValue ($dummyObject3));

		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertFalse ($hashMap->containsValue ($dummyObject3));

		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::equals
	 */
	public function testCheckEqualityWithHashMapOfDifferentTypeOfKeys() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		// Map::KEY_NUMERIC_TYPE vs Map::KEY_STRING_TYPE
		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2 = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($dummyObject->intProperty, $dummyObject);
		$hashMap2->put ($dummyObject->stringProperty, $dummyObject);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Map::KEY_NUMERIC_TYPE vs Map::KEY_BOOLEAN_TYPE
		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($dummyObject->intProperty, $dummyObject);
		$hashMap2->put ($dummyObject->boolProperty, $dummyObject);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Map::KEY_NUMERIC_TYPE vs Map::KEY_OBJECT_TYPE
		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2 = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($dummyObject->intProperty, $dummyObject);
		$hashMap2->put ($dummyObject, $dummyObject);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Map::KEY_STRING_TYPE vs Map::KEY_BOOLEAN_TYPE
		$hashMap1 = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap2 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($dummyObject->stringProperty, $dummyObject);
		$hashMap2->put ($dummyObject->boolProperty, $dummyObject);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Map::KEY_STRING_TYPE vs Map::KEY_OBJECT_TYPE
		$hashMap1 = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap2 = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($dummyObject->stringProperty, $dummyObject);
		$hashMap2->put ($dummyObject, $dummyObject);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Map::KEY_BOOLEAN_TYPE vs Map::KEY_OBJECT_TYPE
		$hashMap1 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap2 = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($dummyObject->boolProperty, $dummyObject);
		$hashMap2->put ($dummyObject, $dummyObject);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::equals
	 */
	public function testCheckEqualityWithHashMapWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($dummyObject1->intProperty, $dummyObject1);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap2->put ($dummyObject2->intProperty, $dummyObject2);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Both map have the same elements
		$hashMap1->put ($dummyObject2->intProperty, $dummyObject2);
		$hashMap2->put ($dummyObject1->intProperty, $dummyObject1);
		$this->assertEquals (2, $hashMap1->size());
		$this->assertEquals (2, $hashMap2->size());

		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds $dummyObject3 with different key
		$hashMap1->put ($dummyObject3->intProperty + $dummyObject3->intProperty, $dummyObject3);
		$hashMap2->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::equals
	 */
	public function testCheckEqualityWithHashMapWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap1 = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap2 = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($dummyObject1->stringProperty, $dummyObject1);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap2->put ($dummyObject2->stringProperty, $dummyObject2);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Both map have the same elements
		$hashMap1->put ($dummyObject2->stringProperty, $dummyObject2);
		$hashMap2->put ($dummyObject1->stringProperty, $dummyObject1);
		$this->assertEquals (2, $hashMap1->size());
		$this->assertEquals (2, $hashMap2->size());

		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds $dummyObject3 with different key
		$hashMap1->put ($dummyObject3->stringProperty.$dummyObject3->stringProperty, $dummyObject3);
		$hashMap2->put ($dummyObject3->stringProperty, $dummyObject3);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::equals
	 */
	public function testCheckEqualityWithHashMapWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap1 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap2 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($dummyObject1->boolProperty, $dummyObject1);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap2->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Both map have the same elements
		$hashMap1->put ($dummyObject2->boolProperty, $dummyObject2);
		$hashMap2->put ($dummyObject1->boolProperty, $dummyObject1);
		$this->assertEquals (2, $hashMap1->size());
		$this->assertEquals (2, $hashMap2->size());

		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds $dummyObject3 with different key
		$hashMap1->put ($dummyObject3->boolProperty, $dummyObject3);
		$hashMap2->put (!$dummyObject3->boolProperty, $dummyObject3);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::equals
	 */
	public function testCheckEqualityWithHashMapWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap1 = new HashMap (Map::KEY_OBJECT_TYPE);
		$hashMap2 = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($dummyObject1, $dummyObject1);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap2->put ($dummyObject2, $dummyObject2);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Both map have the same elements
		$hashMap1->put ($dummyObject2, $dummyObject2);
		$hashMap2->put ($dummyObject1, $dummyObject1);
		$this->assertEquals (2, $hashMap1->size());
		$this->assertEquals (2, $hashMap2->size());

		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds $dummyObject3 with different key
		$hashMap1->put ($dummyObject3, $dummyObject3);
		$hashMap2->put ($dummyObject2, $dummyObject3);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithNumericKeysStoredAndGivenStringKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($dummyObject->intProperty, $dummyObject);

		$hashMap->get ($dummyObject->stringProperty);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithNumericKeysStoredAndGivenBoolKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($dummyObject->intProperty, $dummyObject);

		$hashMap->get ($dummyObject->boolProperty);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithNumericKeysStoredAndGivenObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($dummyObject->intProperty, $dummyObject);

		$hashMap->get ($dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithStringKeysStoredAndGivenBoolKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->put ($dummyObject->stringProperty, $dummyObject);

		$hashMap->get ($dummyObject->boolProperty);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithStringKeysStoredAndGivenObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->put ($dummyObject->stringProperty, $dummyObject);

		$hashMap->get ($dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithBooleanKeysStoredAndGivenObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap->put ($dummyObject->boolProperty, $dummyObject);

		$hashMap->get ($dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::get
	 */
	public function testGetWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->get ($dummyObject1->intProperty)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject2->intProperty)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject3->intProperty)->isPresent());

		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->intProperty)->get());
		$this->assertFalse ($hashMap->get ($dummyObject2->intProperty)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject3->intProperty)->isPresent());

		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->intProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->intProperty)->get());
		$this->assertFalse ($hashMap->get ($dummyObject3->intProperty)->isPresent());

		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->intProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->intProperty)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject3->intProperty)->get());

		// Checks keys with float keys
		$floatConverter = 0.1;

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->get ($dummyObject1->intProperty * $floatConverter)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject2->intProperty * $floatConverter)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject3->intProperty * $floatConverter)->isPresent());

		$hashMap->put ($dummyObject1->intProperty * $floatConverter, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->intProperty * $floatConverter)->get());
		$this->assertFalse ($hashMap->get ($dummyObject2->intProperty * $floatConverter)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject3->intProperty * $floatConverter)->isPresent());

		$hashMap->put ($dummyObject2->intProperty * $floatConverter, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->intProperty * $floatConverter)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->intProperty * $floatConverter)->get());
		$this->assertFalse ($hashMap->get ($dummyObject3->intProperty * $floatConverter)->isPresent());

		$hashMap->put ($dummyObject3->intProperty * $floatConverter, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->intProperty * $floatConverter)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->intProperty * $floatConverter)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject3->intProperty * $floatConverter)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::get
	 */
	public function testGetWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->get ($dummyObject1->stringProperty)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject2->stringProperty)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject3->stringProperty)->isPresent());

		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->stringProperty)->get());
		$this->assertFalse ($hashMap->get ($dummyObject2->stringProperty)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject3->stringProperty)->isPresent());

		$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->stringProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->stringProperty)->get());
		$this->assertFalse ($hashMap->get ($dummyObject3->stringProperty)->isPresent());

		$hashMap->put ($dummyObject3->stringProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->stringProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->stringProperty)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject3->stringProperty)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::get
	 */
	public function testGetWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->get ($dummyObject1->boolProperty)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject2->boolProperty)->isPresent());

		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->boolProperty)->get());
		$this->assertFalse ($hashMap->get ($dummyObject2->boolProperty)->isPresent());

		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->boolProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->boolProperty)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::get
	 */
	public function testGetWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->get ($dummyObject1)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject2)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject3)->isPresent());

		$hashMap->put ($dummyObject1, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1)->get());
		$this->assertFalse ($hashMap->get ($dummyObject2)->isPresent());
		$this->assertFalse ($hashMap->get ($dummyObject3)->isPresent());

		$hashMap->put ($dummyObject2, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2)->get());
		$this->assertFalse ($hashMap->get ($dummyObject3)->isPresent());

		$hashMap->put ($dummyObject3, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject3)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::getKeys
	 */
	public function testGetKeysWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEmpty ($hashMap->getKeys ($dummyObject1));
		$this->assertEmpty ($hashMap->getKeys ($dummyObject2));
		$this->assertEmpty ($hashMap->getKeys ($dummyObject3));

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->intProperty, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($dummyObject2));
		$this->assertEmpty ($hashMap->getKeys ($dummyObject3));

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->intProperty, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject2->intProperty, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($dummyObject3));

		// Adds $dummyObject3
		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->intProperty, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject2->intProperty, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($dummyObject3);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject3->intProperty, $arrayOfKeys[0]);

		// Adds $dummyObject2 with different keys
		$hashMap->put ($dummyObject2->intProperty*2, $dummyObject2);
		$hashMap->put ($dummyObject2->intProperty*3, $dummyObject2);
		$this->assertEquals (5, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject2->intProperty, $dummyObject2->intProperty*2
				                                            ,$dummyObject2->intProperty*3)));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::getKeys
	 */
	public function testGetKeysWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertEmpty ($hashMap->getKeys ($dummyObject1));
		$this->assertEmpty ($hashMap->getKeys ($dummyObject2));
		$this->assertEmpty ($hashMap->getKeys ($dummyObject3));

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->stringProperty, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($dummyObject2));
		$this->assertEmpty ($hashMap->getKeys ($dummyObject3));

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->stringProperty, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject2->stringProperty, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($dummyObject3));

		// Adds $dummyObject3
		$hashMap->put ($dummyObject3->stringProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->stringProperty, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject2->stringProperty, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($dummyObject3);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject3->stringProperty, $arrayOfKeys[0]);

		// Adds $dummyObject2 with different keys
		$hashMap->put ($dummyObject2->stringProperty."2", $dummyObject2);
		$hashMap->put ($dummyObject2->stringProperty."3", $dummyObject2);
		$this->assertEquals (5, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject2->stringProperty, $dummyObject2->stringProperty."2"
				                                            ,$dummyObject2->stringProperty."3")));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::getKeys
	 */
	public function testGetKeysWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertEmpty ($hashMap->getKeys ($dummyObject1));
		$this->assertEmpty ($hashMap->getKeys ($dummyObject2));

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->boolProperty, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($dummyObject2));

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->boolProperty, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject2->boolProperty, $arrayOfKeys[0]);

		// Adds $dummyObject2 with FALSE key
		$hashMap->put ($dummyObject1->boolProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject1->boolProperty, $dummyObject2->boolProperty)));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::getKeys
	 */
	public function testGetKeysWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (4, "d", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertEmpty ($hashMap->getKeys ($dummyObject1));
		$this->assertEmpty ($hashMap->getKeys ($dummyObject2));
		$this->assertEmpty ($hashMap->getKeys ($dummyObject3));

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($dummyObject2));
		$this->assertEmpty ($hashMap->getKeys ($dummyObject3));

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject2, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($dummyObject3));

		// Adds $dummyObject3
		$hashMap->put ($dummyObject3, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject2, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($dummyObject3);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject3, $arrayOfKeys[0]);

		// Adds $dummyObject2 with different keys
		$hashMap->put ($dummyObject4, $dummyObject2);
		$this->assertEquals (4, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($dummyObject2);
		$this->assertCount (2, $arrayOfKeys);

		$isDummyObjectsInArray = array (2 => FALSE, 4 => FALSE);
		foreach ($arrayOfKeys as $element) {

			if ($element->equals ($dummyObject2))
				$isDummyObjectsInArray[2] = TRUE;

			elseif ($element->equals ($dummyObject4))
				$isDummyObjectsInArray[4] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInArray[2]);
		$this->assertTrue ($isDummyObjectsInArray[4]);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::getTypeOfKeys
	 */
	public function testGetTypeOfKeys() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEquals (Map::KEY_NUMERIC_TYPE, $hashMap->getTypeOfKeys());

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertEquals (Map::KEY_STRING_TYPE, $hashMap->getTypeOfKeys());

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertEquals (Map::KEY_BOOLEAN_TYPE, $hashMap->getTypeOfKeys());

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertEquals (Map::KEY_OBJECT_TYPE, $hashMap->getTypeOfKeys());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::hashCode
	 */
	public function testHashCodeWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$entryMap1 = new EntryHashMap ($dummyObject1->intProperty, $dummyObject1);
		$entryMap2 = new EntryHashMap ($dummyObject2->intProperty, $dummyObject2);
		$entryMap3 = new EntryHashMap ($dummyObject3->intProperty, $dummyObject3);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEquals (0, $hashMap->hashCode());

		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$this->assertEquals ($entryMap1->hashCode(), $hashMap->hashCode());

		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode(), $hashMap->hashCode());

		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode() + $entryMap3->hashCode()
				            ,$hashMap->hashCode());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::hashCode
	 */
	public function testHashCodeWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$entryMap1 = new EntryHashMap ($dummyObject1->stringProperty, $dummyObject1);
		$entryMap2 = new EntryHashMap ($dummyObject2->stringProperty, $dummyObject2);
		$entryMap3 = new EntryHashMap ($dummyObject3->stringProperty, $dummyObject3);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertEquals (0, $hashMap->hashCode());

		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
		$this->assertEquals ($entryMap1->hashCode(), $hashMap->hashCode());

		$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode(), $hashMap->hashCode());

		$hashMap->put ($dummyObject3->stringProperty, $dummyObject3);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode() + $entryMap3->hashCode()
				            ,$hashMap->hashCode());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::hashCode
	 */
	public function testHashCodeWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$entryMap1 = new EntryHashMap ($dummyObject1->boolProperty, $dummyObject1);
		$entryMap2 = new EntryHashMap ($dummyObject2->boolProperty, $dummyObject2);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertEquals (0, $hashMap->hashCode());

		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);
		$this->assertEquals ($entryMap1->hashCode(), $hashMap->hashCode());

		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode(), $hashMap->hashCode());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::hashCode
	 */
	public function testHashCodeWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$entryMap1 = new EntryHashMap ($dummyObject1, $dummyObject1);
		$entryMap2 = new EntryHashMap ($dummyObject2, $dummyObject2);
		$entryMap3 = new EntryHashMap ($dummyObject3, $dummyObject3);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertEquals (0, $hashMap->hashCode());

		$hashMap->put ($dummyObject1, $dummyObject1);
		$this->assertEquals ($entryMap1->hashCode(), $hashMap->hashCode());

		$hashMap->put ($dummyObject2, $dummyObject2);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode(), $hashMap->hashCode());

		$hashMap->put ($dummyObject3, $dummyObject3);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode() + $entryMap3->hashCode()
				            ,$hashMap->hashCode());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::isEmpty
	 */
	public function testIsEmptyWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
    	$this->assertEquals (0, $hashMap->size());

    	$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (1, $hashMap->size());

    	$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (2, $hashMap->size());

    	$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (3, $hashMap->size());

    	// Does not accept duplicate elements
    	$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (3, $hashMap->size());

    	// Removes every element
    	$hashMap->removeByKey ($dummyObject3->intProperty);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (2, $hashMap->size());

    	$hashMap->removeByKey ($dummyObject2->intProperty);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (1, $hashMap->size());

    	$hashMap->removeByKey ($dummyObject1->intProperty);
    	$this->assertTrue ($hashMap->isEmpty());
    	$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::isEmpty
	 */
	public function testIsEmptyWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());

		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		$hashMap->put ($dummyObject3->stringProperty, $dummyObject3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (3, $hashMap->size());

		// Does not accept duplicate elements
		$hashMap->put ($dummyObject3->stringProperty, $dummyObject3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (3, $hashMap->size());

		// Removes every element
		$hashMap->removeByKey ($dummyObject3->stringProperty);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		$hashMap->removeByKey ($dummyObject2->stringProperty);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->removeByKey ($dummyObject1->stringProperty);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::isEmpty
	 */
	public function testIsEmptyWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());

		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		// Does not accept duplicate elements
		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		// Removes every element
		$hashMap->removeByKey ($dummyObject2->boolProperty);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->removeByKey ($dummyObject1->boolProperty);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::isEmpty
	 */
	public function testIsEmptyWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());

		$hashMap->put ($dummyObject1, $dummyObject1);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->put ($dummyObject2, $dummyObject2);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		$hashMap->put ($dummyObject3, $dummyObject3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (3, $hashMap->size());

		// Does not accept duplicate elements
		$hashMap->put ($dummyObject3, $dummyObject3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (3, $hashMap->size());

		// Removes every element
		$hashMap->removeByKey ($dummyObject3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		$hashMap->removeByKey ($dummyObject2);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->removeByKey ($dummyObject1);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::iterator
	 */
	public function testIteratorWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		foreach ($hashMap->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->intProperty, $internalKey);
			$this->assertEquals ($dummyObject1, $internalValue);
		}

		// Adds another dummy objects
		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$isDummyObjectsInHashSet = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			if ($internalKey === $dummyObject1->intProperty && $internalValue->equals ($dummyObject1))
				$isDummyObjectsInHashSet[1] = TRUE;

			elseif ($internalKey === $dummyObject2->intProperty && $internalValue->equals ($dummyObject2))
				$isDummyObjectsInHashSet[2] = TRUE;

			elseif ($internalKey === $dummyObject3->intProperty && $internalValue->equals ($dummyObject3))
				$isDummyObjectsInHashSet[3] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInHashSet[1]);
		$this->assertTrue ($isDummyObjectsInHashSet[2]);
		$this->assertTrue ($isDummyObjectsInHashSet[3]);

		// Checks keys with float keys
		$floatConverter = 0.1;
		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		foreach ($hashMap->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->intProperty * $floatConverter, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->intProperty * $floatConverter, $internalKey);
			$this->assertEquals ($dummyObject1, $internalValue);
		}

		// Adds another dummy objects
		$hashMap->put ($dummyObject2->intProperty * $floatConverter, $dummyObject2);
		$hashMap->put ($dummyObject3->intProperty * $floatConverter, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$isDummyObjectsInHashSet = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			if ($internalKey === ($dummyObject1->intProperty * $floatConverter) &&
					$internalValue->equals ($dummyObject1))
				$isDummyObjectsInHashSet[1] = TRUE;

			elseif ($internalKey === ($dummyObject2->intProperty * $floatConverter) &&
					$internalValue->equals ($dummyObject2))
				$isDummyObjectsInHashSet[2] = TRUE;

			elseif ($internalKey === ($dummyObject3->intProperty * $floatConverter) &&
					$internalValue->equals ($dummyObject3))
				$isDummyObjectsInHashSet[3] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInHashSet[1]);
		$this->assertTrue ($isDummyObjectsInHashSet[2]);
		$this->assertTrue ($isDummyObjectsInHashSet[3]);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::iterator
	 */
	public function testIteratorWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);

		foreach ($hashMap->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->stringProperty, $internalKey);
			$this->assertEquals ($dummyObject1, $internalValue);
		}

		// Adds another dummy objects
		$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);
		$hashMap->put ($dummyObject3->stringProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$isDummyObjectsInHashSet = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			if ($internalKey === $dummyObject1->stringProperty && $internalValue->equals ($dummyObject1))
				$isDummyObjectsInHashSet[1] = TRUE;

			elseif ($internalKey === $dummyObject2->stringProperty && $internalValue->equals ($dummyObject2))
				$isDummyObjectsInHashSet[2] = TRUE;

			elseif ($internalKey === $dummyObject3->stringProperty && $internalValue->equals ($dummyObject3))
				$isDummyObjectsInHashSet[3] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInHashSet[1]);
		$this->assertTrue ($isDummyObjectsInHashSet[2]);
		$this->assertTrue ($isDummyObjectsInHashSet[3]);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::iterator
	 */
	public function testIteratorWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);

		foreach ($hashMap->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->boolProperty, $internalKey);
			$this->assertEquals ($dummyObject1, $internalValue);
		}

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$isDummyObjectsInHashSet = array (1 => FALSE, 2 => FALSE);
		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			if ($internalKey === $dummyObject1->boolProperty && $internalValue->equals ($dummyObject1))
				$isDummyObjectsInHashSet[1] = TRUE;

			elseif ($internalKey === $dummyObject2->boolProperty && $internalValue->equals ($dummyObject2))
				$isDummyObjectsInHashSet[2] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInHashSet[1]);
		$this->assertTrue ($isDummyObjectsInHashSet[2]);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::iterator
	 */
	public function testIteratorWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);

		foreach ($hashMap->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1, $internalKey);
			$this->assertEquals ($dummyObject1, $internalValue);
		}

		// Adds another dummy objects
		$hashMap->put ($dummyObject2, $dummyObject2);
		$hashMap->put ($dummyObject3, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());

		$isDummyObjectsInHashSet = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			if ($internalKey === $dummyObject1 && $internalValue->equals ($dummyObject1))
				$isDummyObjectsInHashSet[1] = TRUE;

			elseif ($internalKey === $dummyObject2 && $internalValue->equals ($dummyObject2))
				$isDummyObjectsInHashSet[2] = TRUE;

			elseif ($internalKey === $dummyObject3 && $internalValue->equals ($dummyObject3))
				$isDummyObjectsInHashSet[3] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInHashSet[1]);
		$this->assertTrue ($isDummyObjectsInHashSet[2]);
		$this->assertTrue ($isDummyObjectsInHashSet[3]);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::keys
	 */
	public function testKeysWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->intProperty, $arrayOfKeys[0]);

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject1->intProperty, $dummyObject2->intProperty)));

		// Adds $dummyObject3
		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject1->intProperty, $dummyObject2->intProperty
				                                            ,$dummyObject3->intProperty)));
		// Checks keys with float keys
		$floatConverter = 0.1;
		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->intProperty * $floatConverter, $dummyObject1);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->intProperty * $floatConverter, $arrayOfKeys[0]);

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2->intProperty * $floatConverter, $dummyObject2);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject1->intProperty * $floatConverter
				                                            ,$dummyObject2->intProperty * $floatConverter)));

		// Adds $dummyObject3
		$hashMap->put ($dummyObject3->intProperty * $floatConverter, $dummyObject3);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject1->intProperty * $floatConverter
				                                            ,$dummyObject2->intProperty * $floatConverter
				                                            ,$dummyObject3->intProperty * $floatConverter)));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::keys
	 */
	public function testKeysWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->stringProperty, $arrayOfKeys[0]);

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject1->stringProperty
				                                            ,$dummyObject2->stringProperty)));
		// Adds $dummyObject3
		$hashMap->put ($dummyObject3->stringProperty, $dummyObject3);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject1->stringProperty
				                                            ,$dummyObject2->stringProperty
				                                            ,$dummyObject3->stringProperty)));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::keys
	 */
	public function testKeysWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1->boolProperty, $arrayOfKeys[0]);

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject1->boolProperty, $dummyObject2->boolProperty)));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::keys
	 */
	public function testKeysWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1, $dummyObject1);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($dummyObject1, $arrayOfKeys[0]);

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2, $dummyObject2);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject1, $dummyObject2)));

		// Adds $dummyObject3
		$hashMap->put ($dummyObject3, $dummyObject3);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($dummyObject1, $dummyObject2, $dummyObject3)));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::put
	 */
	public function testPutWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (4, "d", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $dummyObject1
		$this->assertFalse ($hashMap->put ($dummyObject1->intProperty, $dummyObject1)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->intProperty, $internalKey);
			$this->assertEquals ($dummyObject1, $internalValue);
		}

		// Overwrites $dummyObject1->intProperty with $dummyObject2
		$this->assertEquals ($dummyObject1, $hashMap->put ($dummyObject1->intProperty, $dummyObject2)->get());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->intProperty, $internalKey);
			$this->assertEquals ($dummyObject2, $internalValue);
		}

		// Adds $dummyObject3 and $dummyObject4
		$this->assertFalse ($hashMap->put ($dummyObject3->intProperty, $dummyObject3)->isPresent());
		$this->assertFalse ($hashMap->put ($dummyObject4->intProperty, $dummyObject4)->isPresent());
		$this->assertEquals (3, $hashMap->size());

		// Checks the content of the map
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject1->intProperty)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject3->intProperty)->get());
		$this->assertEquals ($dummyObject4, $hashMap->get ($dummyObject4->intProperty)->get());

		// Checks keys with float keys
		$floatConverter = 0.1;

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		// Adds $dummyObject1
		$this->assertFalse ($hashMap->put ($dummyObject1->intProperty * $floatConverter, $dummyObject1)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->intProperty * $floatConverter, $internalKey);
			$this->assertEquals ($dummyObject1, $internalValue);
		}

		// Overwrites $dummyObject1->intProperty with $dummyObject2
		$this->assertEquals ($dummyObject1, $hashMap->put ($dummyObject1->intProperty * $floatConverter, $dummyObject2)->get());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->intProperty * $floatConverter, $internalKey);
			$this->assertEquals ($dummyObject2, $internalValue);
		}

		// Adds $dummyObject3 and $dummyObject4
		$this->assertFalse ($hashMap->put ($dummyObject3->intProperty * $floatConverter, $dummyObject3)->isPresent());
		$this->assertFalse ($hashMap->put ($dummyObject4->intProperty * $floatConverter, $dummyObject4)->isPresent());
		$this->assertEquals (3, $hashMap->size());

		// Checks the content of the map
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject1->intProperty * $floatConverter)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject3->intProperty * $floatConverter)->get());
		$this->assertEquals ($dummyObject4, $hashMap->get ($dummyObject4->intProperty * $floatConverter)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::put
	 */
	public function testPutWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (4, "d", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $dummyObject1
		$this->assertFalse ($hashMap->put ($dummyObject1->stringProperty, $dummyObject1)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->stringProperty, $internalKey);
			$this->assertEquals ($dummyObject1, $internalValue);
		}

		// Overwrites $dummyObject1->stringProperty with $dummyObject2
		$this->assertEquals ($dummyObject1, $hashMap->put ($dummyObject1->stringProperty, $dummyObject2)->get());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->stringProperty, $internalKey);
			$this->assertEquals ($dummyObject2, $internalValue);
		}

		// Adds $dummyObject3 and $dummyObject4
		$this->assertFalse ($hashMap->put ($dummyObject3->stringProperty, $dummyObject3)->isPresent());
		$this->assertFalse ($hashMap->put ($dummyObject4->stringProperty, $dummyObject4)->isPresent());
		$this->assertEquals (3, $hashMap->size());

		// Checks the content of the map
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject1->stringProperty)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject3->stringProperty)->get());
		$this->assertEquals ($dummyObject4, $hashMap->get ($dummyObject4->stringProperty)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::put
	 */
	public function testPutWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $dummyObject1
		$this->assertFalse ($hashMap->put ($dummyObject1->boolProperty, $dummyObject1)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->boolProperty, $internalKey);
			$this->assertEquals ($dummyObject1, $internalValue);
		}

		// Overwrites $dummyObject1->boolProperty with $dummyObject2
		$this->assertEquals ($dummyObject1, $hashMap->put ($dummyObject1->boolProperty, $dummyObject2)->get());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1->boolProperty, $internalKey);
			$this->assertEquals ($dummyObject2, $internalValue);
		}

		// Adds $dummyObject3
		$this->assertFalse ($hashMap->put ($dummyObject3->boolProperty, $dummyObject3)->isPresent());
		$this->assertEquals (2, $hashMap->size());

		// Checks the content of the map
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject1->boolProperty)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject3->boolProperty)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::put
	 */
	public function testPutWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (4, "d", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $dummyObject1
		$this->assertFalse ($hashMap->put ($dummyObject1, $dummyObject1)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1, $internalKey);
			$this->assertEquals ($dummyObject1, $internalValue);
		}

		// Overwrites $dummyObject1 with $dummyObject2
		$this->assertEquals ($dummyObject1, $hashMap->put ($dummyObject1, $dummyObject2)->get());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($dummyObject1, $internalKey);
			$this->assertEquals ($dummyObject2, $internalValue);
		}

		// Adds $dummyObject3 and $dummyObject4
		$this->assertFalse ($hashMap->put ($dummyObject3, $dummyObject3)->isPresent());
		$this->assertFalse ($hashMap->put ($dummyObject4, $dummyObject4)->isPresent());
		$this->assertEquals (3, $hashMap->size());

		// Checks the content of the map
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject1)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject3)->get());
		$this->assertEquals ($dummyObject4, $hashMap->get ($dummyObject4)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithNumericKeysStoredAndGivenMapWithStringKey() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_STRING_TYPE));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithNumericKeysStoredAndGivenMapWithBooleanKey() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_BOOLEAN_TYPE));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithNumericKeysStoredAndGivenMapWithObjectKey() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_OBJECT_TYPE));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithStringKeysStoredAndGivenMapWithBooleanKey() {

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_BOOLEAN_TYPE));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithStringKeysStoredAndGivenMapWithObjectKey() {

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_OBJECT_TYPE));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithBooleanKeysStoredAndGivenMapWithObjectKey() {

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_OBJECT_TYPE));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 */
	public function testPutAllElementsOfEmptyMap() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->putAll (new HashMap (Map::KEY_NUMERIC_TYPE)));
		$this->assertTrue ($hashMap->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 */
	public function testPutAllWithNumericKeysAndHashMap() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap1->isEmpty());

		$hashMap2 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2->put ($dummyObject1->intProperty, $dummyObject1);
		$hashMap2->put ($dummyObject2->intProperty, $dummyObject2);
		$hashMap2->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap2->size());

		// Adds elements of $hashMap2 inside $hashMap1
		$this->assertTrue ($hashMap1->putAll ($hashMap2));
		$this->assertFalse ($hashMap1->isEmpty());
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds again all elements of $hashMap2 inside $hashMap1
		$this->assertTrue ($hashMap1->putAll ($hashMap2));
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Checks keys with float keys
		$floatConverter = 0.1;

		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap1->isEmpty());

		$hashMap2 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2->put ($dummyObject1->intProperty * $floatConverter, $dummyObject1);
		$hashMap2->put ($dummyObject2->intProperty * $floatConverter, $dummyObject2);
		$hashMap2->put ($dummyObject3->intProperty * $floatConverter, $dummyObject3);
		$this->assertEquals (3, $hashMap2->size());

		// Adds elements of $hashMap2 inside $hashMap1
		$this->assertTrue ($hashMap1->putAll ($hashMap2));
		$this->assertFalse ($hashMap1->isEmpty());
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds again all elements of $hashMap2 inside $hashMap1
		$this->assertTrue ($hashMap1->putAll ($hashMap2));
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 */
	public function testPutAllWithStringKeysAndHashMap() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap1 = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertTrue ($hashMap1->isEmpty());

		$hashMap2 = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap2->put ($dummyObject1->stringProperty, $dummyObject1);
		$hashMap2->put ($dummyObject2->stringProperty, $dummyObject2);
		$hashMap2->put ($dummyObject3->stringProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap2->size());

		// Adds elements of $hashMap2 inside $hashMap1
		$this->assertTrue ($hashMap1->putAll ($hashMap2));
		$this->assertFalse ($hashMap1->isEmpty());
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds again all elements of $hashMap2 inside $hashMap1
		$this->assertTrue ($hashMap1->putAll ($hashMap2));
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 */
	public function testPutAllWithBooleanKeysAndHashMap() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$hashMap1 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertTrue ($hashMap1->isEmpty());

		$hashMap2 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap2->put ($dummyObject1->boolProperty, $dummyObject1);
		$hashMap2->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap2->size());

		// Adds elements of $hashMap2 inside $hashMap1
		$this->assertTrue ($hashMap1->putAll ($hashMap2));
		$this->assertFalse ($hashMap1->isEmpty());
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds again all elements of $hashMap2 inside $hashMap1
		$this->assertTrue ($hashMap1->putAll ($hashMap2));
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::putAll
	 */
	public function testPutAllWithObjectKeysAndHashMap() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap1 = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap1->isEmpty());

		$hashMap2 = new HashMap (Map::KEY_OBJECT_TYPE);
		$hashMap2->put ($dummyObject1, $dummyObject1);
		$hashMap2->put ($dummyObject2, $dummyObject2);
		$hashMap2->put ($dummyObject3, $dummyObject3);
		$this->assertEquals (3, $hashMap2->size());

		// Adds elements of $hashMap2 inside $hashMap1
		$this->assertTrue ($hashMap1->putAll ($hashMap2));
		$this->assertFalse ($hashMap1->isEmpty());
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds again all elements of $hashMap2 inside $hashMap1
		$this->assertTrue ($hashMap1->putAll ($hashMap2));
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::remove
	 */
	public function testRemoveWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (11, "k", FALSE);
		$dummyObject5 = new DummyObject (12, "l", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->remove ($dummyObject1->intProperty, $dummyObject1));
		$this->assertFalse ($hashMap->remove ($dummyObject2->intProperty, $dummyObject2));
		$this->assertFalse ($hashMap->remove ($dummyObject3->intProperty, $dummyObject3));
		$this->assertFalse ($hashMap->remove ($dummyObject4->intProperty, $dummyObject4));
		$this->assertFalse ($hashMap->remove ($dummyObject5->intProperty, $dummyObject5));

		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$hashMap->put ($dummyObject4->intProperty, $dummyObject4);
		$hashMap->put ($dummyObject5->intProperty, $dummyObject5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5->intProperty));

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject1
		$this->assertTrue ($hashMap->remove ($dummyObject1->intProperty, $dummyObject1));
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5->intProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject2 and $dummyObject5
		$this->assertTrue ($hashMap->remove ($dummyObject2->intProperty, $dummyObject2));
		$this->assertTrue ($hashMap->remove ($dummyObject5->intProperty, $dummyObject5));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5->intProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Tries to remove $dummyObject2 again and an incorrect pair key-value
		$this->assertFalse ($hashMap->remove ($dummyObject2->intProperty, $dummyObject2));
		$this->assertFalse ($hashMap->remove ($dummyObject3->intProperty, $dummyObject4));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5->intProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject3 and $dummyObject4
		$this->assertTrue ($hashMap->remove ($dummyObject3->intProperty, $dummyObject3));
		$this->assertTrue ($hashMap->remove ($dummyObject4->intProperty, $dummyObject4));
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::remove
	 */
	public function testRemoveWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (11, "k", FALSE);
		$dummyObject5 = new DummyObject (12, "l", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->remove ($dummyObject1->stringProperty, $dummyObject1));
		$this->assertFalse ($hashMap->remove ($dummyObject2->stringProperty, $dummyObject2));
		$this->assertFalse ($hashMap->remove ($dummyObject3->stringProperty, $dummyObject3));
		$this->assertFalse ($hashMap->remove ($dummyObject4->stringProperty, $dummyObject4));
		$this->assertFalse ($hashMap->remove ($dummyObject5->stringProperty, $dummyObject5));

		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
		$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);
		$hashMap->put ($dummyObject3->stringProperty, $dummyObject3);
		$hashMap->put ($dummyObject4->stringProperty, $dummyObject4);
		$hashMap->put ($dummyObject5->stringProperty, $dummyObject5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5->stringProperty));

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject1
		$this->assertTrue ($hashMap->remove ($dummyObject1->stringProperty, $dummyObject1));
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5->stringProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject2 and $dummyObject5
		$this->assertTrue ($hashMap->remove ($dummyObject2->stringProperty, $dummyObject2));
		$this->assertTrue ($hashMap->remove ($dummyObject5->stringProperty, $dummyObject5));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5->stringProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Tries to remove $dummyObject2 again and an incorrect pair key-value
		$this->assertFalse ($hashMap->remove ($dummyObject2->stringProperty, $dummyObject2));
		$this->assertFalse ($hashMap->remove ($dummyObject3->stringProperty, $dummyObject4));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5->stringProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject3 and $dummyObject4
		$this->assertTrue ($hashMap->remove ($dummyObject3->stringProperty, $dummyObject3));
		$this->assertTrue ($hashMap->remove ($dummyObject4->stringProperty, $dummyObject4));
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::remove
	 */
	public function testRemoveWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->remove ($dummyObject1->boolProperty, $dummyObject1));
		$this->assertFalse ($hashMap->remove ($dummyObject2->boolProperty, $dummyObject2));

		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);
		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->boolProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->boolProperty));

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));

		// Removes $dummyObject1
		$this->assertTrue ($hashMap->remove ($dummyObject1->boolProperty, $dummyObject1));
		$this->assertEquals (1, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->boolProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->boolProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));

		// Tries to remove $dummyObject1 again and an incorrect pair key-value
		$this->assertFalse ($hashMap->remove ($dummyObject1->boolProperty, $dummyObject1));
		$this->assertFalse ($hashMap->remove ($dummyObject2->boolProperty, $dummyObject1));
		$this->assertEquals (1, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->boolProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->boolProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));

		// Removes $dummyObject2
		$this->assertTrue ($hashMap->remove ($dummyObject2->boolProperty, $dummyObject2));
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::remove
	 */
	public function testRemoveWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (11, "k", FALSE);
		$dummyObject5 = new DummyObject (12, "l", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->remove ($dummyObject1, $dummyObject1));
		$this->assertFalse ($hashMap->remove ($dummyObject2, $dummyObject2));
		$this->assertFalse ($hashMap->remove ($dummyObject3, $dummyObject3));
		$this->assertFalse ($hashMap->remove ($dummyObject4, $dummyObject4));
		$this->assertFalse ($hashMap->remove ($dummyObject5, $dummyObject5));

		$hashMap->put ($dummyObject1, $dummyObject1);
		$hashMap->put ($dummyObject2, $dummyObject2);
		$hashMap->put ($dummyObject3, $dummyObject3);
		$hashMap->put ($dummyObject4, $dummyObject4);
		$hashMap->put ($dummyObject5, $dummyObject5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5));

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject1
		$this->assertTrue ($hashMap->remove ($dummyObject1, $dummyObject1));
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject2 and $dummyObject5
		$this->assertTrue ($hashMap->remove ($dummyObject2, $dummyObject2));
		$this->assertTrue ($hashMap->remove ($dummyObject5, $dummyObject5));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Tries to remove $dummyObject2 again and an incorrect pair key-value
		$this->assertFalse ($hashMap->remove ($dummyObject2, $dummyObject2));
		$this->assertFalse ($hashMap->remove ($dummyObject3, $dummyObject4));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject3 and $dummyObject4
		$this->assertTrue ($hashMap->remove ($dummyObject3, $dummyObject3));
		$this->assertTrue ($hashMap->remove ($dummyObject4, $dummyObject4));
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::removeByKey
	 */
	public function testRemoveByKeyWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (11, "k", FALSE);
		$dummyObject5 = new DummyObject (12, "l", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->removeByKey ($dummyObject1->intProperty)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject2->intProperty)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject3->intProperty)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject4->intProperty)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject5->intProperty)->isPresent());

		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$hashMap->put ($dummyObject4->intProperty, $dummyObject4);
		$hashMap->put ($dummyObject5->intProperty, $dummyObject5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5->intProperty));

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject1
		$this->assertEquals ($dummyObject1, $hashMap->removeByKey ($dummyObject1->intProperty)->get());
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5->intProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject2 and $dummyObject5
		$this->assertEquals ($dummyObject2, $hashMap->removeByKey ($dummyObject2->intProperty)->get());
		$this->assertEquals ($dummyObject5, $hashMap->removeByKey ($dummyObject5->intProperty)->get());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5->intProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Tries to remove $dummyObject2 again
		$this->assertFalse ($hashMap->removeByKey ($dummyObject2->intProperty)->isPresent());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->intProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5->intProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject3 and $dummyObject4
		$this->assertEquals ($dummyObject3, $hashMap->removeByKey ($dummyObject3->intProperty)->get());
		$this->assertEquals ($dummyObject4, $hashMap->removeByKey ($dummyObject4->intProperty)->get());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::removeByKey
	 */
	public function testRemoveByKeyWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (11, "k", FALSE);
		$dummyObject5 = new DummyObject (12, "l", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->removeByKey ($dummyObject1->stringProperty)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject2->stringProperty)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject3->stringProperty)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject4->stringProperty)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject5->stringProperty)->isPresent());

		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
		$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);
		$hashMap->put ($dummyObject3->stringProperty, $dummyObject3);
		$hashMap->put ($dummyObject4->stringProperty, $dummyObject4);
		$hashMap->put ($dummyObject5->stringProperty, $dummyObject5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5->stringProperty));

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject1
		$this->assertEquals ($dummyObject1, $hashMap->removeByKey ($dummyObject1->stringProperty)->get());
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5->stringProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject2 and $dummyObject5
		$this->assertEquals ($dummyObject2, $hashMap->removeByKey ($dummyObject2->stringProperty)->get());
		$this->assertEquals ($dummyObject5, $hashMap->removeByKey ($dummyObject5->stringProperty)->get());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5->stringProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Tries to remove $dummyObject2 again
		$this->assertFalse ($hashMap->removeByKey ($dummyObject2->stringProperty)->isPresent());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3->stringProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5->stringProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject3 and $dummyObject4
		$this->assertEquals ($dummyObject3, $hashMap->removeByKey ($dummyObject3->stringProperty)->get());
		$this->assertEquals ($dummyObject4, $hashMap->removeByKey ($dummyObject4->stringProperty)->get());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::removeByKey
	 */
	public function testRemoveByKeyWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->removeByKey ($dummyObject1->boolProperty)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject2->boolProperty)->isPresent());

		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);
		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->boolProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->boolProperty));

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));

		// Removes $dummyObject1
		$this->assertEquals ($dummyObject1, $hashMap->removeByKey ($dummyObject1->boolProperty)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->boolProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->boolProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));

		// Tries to remove $dummyObject1 again
		$this->assertFalse ($hashMap->removeByKey ($dummyObject1->boolProperty)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1->boolProperty));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2->boolProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));

		// Removes $dummyObject2
		$this->assertEquals ($dummyObject2, $hashMap->removeByKey ($dummyObject2->boolProperty)->get());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::removeByKey
	 */
	public function testRemoveByKeyWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (11, "k", FALSE);
		$dummyObject5 = new DummyObject (12, "l", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->removeByKey ($dummyObject1)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject2)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject3)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject4)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($dummyObject5)->isPresent());

		$hashMap->put ($dummyObject1, $dummyObject1);
		$hashMap->put ($dummyObject2, $dummyObject2);
		$hashMap->put ($dummyObject3, $dummyObject3);
		$hashMap->put ($dummyObject4, $dummyObject4);
		$hashMap->put ($dummyObject5, $dummyObject5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5));

		$this->assertTrue ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject1
		$this->assertEquals ($dummyObject1, $hashMap->removeByKey ($dummyObject1)->get());
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1));
		$this->assertTrue ($hashMap->containsKey ($dummyObject2));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4));
		$this->assertTrue ($hashMap->containsKey ($dummyObject5));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertTrue ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject2 and $dummyObject5
		$this->assertEquals ($dummyObject2, $hashMap->removeByKey ($dummyObject2)->get());
		$this->assertEquals ($dummyObject5, $hashMap->removeByKey ($dummyObject5)->get());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Tries to remove $dummyObject2 again
		$this->assertFalse ($hashMap->removeByKey ($dummyObject2)->isPresent());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($dummyObject1));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2));
		$this->assertTrue ($hashMap->containsKey ($dummyObject3));
		$this->assertTrue ($hashMap->containsKey ($dummyObject4));
		$this->assertFalse ($hashMap->containsKey ($dummyObject5));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
		$this->assertTrue ($hashMap->containsValue ($dummyObject4));
		$this->assertFalse ($hashMap->containsValue ($dummyObject5));

		// Removes $dummyObject3 and $dummyObject4
		$this->assertEquals ($dummyObject3, $hashMap->removeByKey ($dummyObject3)->get());
		$this->assertEquals ($dummyObject4, $hashMap->removeByKey ($dummyObject4)->get());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNumericKeysStoredAndGivenMapWithStringKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replace ($dummyObject->stringProperty, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNumericKeysStoredAndGivenMapWithBooleanKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replace ($dummyObject->boolProperty, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNumericKeysStoredAndGivenMapWithObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replace ($dummyObject, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithStringKeysStoredAndGivenMapWithBooleanKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->replace ($dummyObject->boolProperty, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithStringKeysStoredAndGivenMapWithObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->replace ($dummyObject, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithBooleanKeysStoredAndGivenMapWithObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap->replace ($dummyObject, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replace
	 */
	public function testReplaceWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->replace ($dummyObject1->intProperty, $dummyObject1)->isPresent());
		$this->assertFalse ($hashMap->replace ($dummyObject2->intProperty, $dummyObject2)->isPresent());
		$this->assertFalse ($hashMap->replace ($dummyObject3->intProperty, $dummyObject3)->isPresent());

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		// Replaces $dummyObject1 by $dummyObject2
		$this->assertEquals ($dummyObject1, $hashMap->replace ($dummyObject1->intProperty, $dummyObject2)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->intProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));

		// Replaces $dummyObject2 by $dummyObject3
		$this->assertEquals ($dummyObject2, $hashMap->replace ($dummyObject1->intProperty, $dummyObject3)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->intProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->intProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replace
	 */
	public function testReplaceWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->replace ($dummyObject1->stringProperty, $dummyObject1)->isPresent());
		$this->assertFalse ($hashMap->replace ($dummyObject2->stringProperty, $dummyObject2)->isPresent());
		$this->assertFalse ($hashMap->replace ($dummyObject3->stringProperty, $dummyObject3)->isPresent());

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		// Replaces $dummyObject1 by $dummyObject2
		$this->assertEquals ($dummyObject1, $hashMap->replace ($dummyObject1->stringProperty, $dummyObject2)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->stringProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));

		// Replaces $dummyObject2 by $dummyObject3
		$this->assertEquals ($dummyObject2, $hashMap->replace ($dummyObject1->stringProperty, $dummyObject3)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->stringProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3->stringProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replace
	 */
	public function testReplaceWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->replace ($dummyObject1->boolProperty, $dummyObject1)->isPresent());
		$this->assertFalse ($hashMap->replace ($dummyObject2->boolProperty, $dummyObject2)->isPresent());

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		// Replaces $dummyObject1 by $dummyObject2
		$this->assertEquals ($dummyObject1, $hashMap->replace ($dummyObject1->boolProperty, $dummyObject2)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1->boolProperty));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2->boolProperty));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replace
	 */
	public function testReplaceWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->replace ($dummyObject1, $dummyObject1)->isPresent());
		$this->assertFalse ($hashMap->replace ($dummyObject2, $dummyObject2)->isPresent());
		$this->assertFalse ($hashMap->replace ($dummyObject3, $dummyObject3)->isPresent());

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());

		// Replaces $dummyObject1 by $dummyObject2
		$this->assertEquals ($dummyObject1, $hashMap->replace ($dummyObject1, $dummyObject2)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertTrue ($hashMap->containsValue ($dummyObject2));

		// Replaces $dummyObject2 by $dummyObject3
		$this->assertEquals ($dummyObject2, $hashMap->replace ($dummyObject1, $dummyObject3)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($dummyObject1));
		$this->assertFalse ($hashMap->containsKey ($dummyObject2));
		$this->assertFalse ($hashMap->containsKey ($dummyObject3));

		$this->assertFalse ($hashMap->containsValue ($dummyObject1));
		$this->assertFalse ($hashMap->containsValue ($dummyObject2));
		$this->assertTrue ($hashMap->containsValue ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithNumericKeysStoredAndGivenMapWithStringKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replaceWithNewValue ($dummyObject->stringProperty, $dummyObject, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithNumericKeysStoredAndGivenMapWithBooleanKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replaceWithNewValue ($dummyObject->boolProperty, $dummyObject, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithNumericKeysStoredAndGivenMapWithObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replaceWithNewValue ($dummyObject, $dummyObject, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithStringKeysStoredAndGivenMapWithBooleanKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->replaceWithNewValue ($dummyObject->boolProperty, $dummyObject, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithStringKeysStoredAndGivenMapWithObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->replaceWithNewValue ($dummyObject, $dummyObject, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithBooleanKeysStoredAndGivenMapWithObjectKey() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap->replaceWithNewValue ($dummyObject, $dummyObject, $dummyObject);
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replaceWithNewValue
	 */
	public function testReplaceWithNewValueWithNumericKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject1->intProperty, $dummyObject1, $dummyObject2));
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject2->intProperty, $dummyObject2, $dummyObject3));
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject3->intProperty, $dummyObject3, $dummyObject1));

		// Adds $dummyObject1 and $dummyObject2
		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->intProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->intProperty)->get());

		// Tries to replace a non existing pair
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject1->intProperty, $dummyObject2, $dummyObject3));

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->intProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->intProperty)->get());

		// Replaces $dummyObject1 by $dummyObject2 and $dummyObject2 by $dummyObject3
		$this->assertTrue ($hashMap->replaceWithNewValue ($dummyObject1->intProperty, $dummyObject1, $dummyObject2));
		$this->assertTrue ($hashMap->replaceWithNewValue ($dummyObject2->intProperty, $dummyObject2, $dummyObject3));
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject1->intProperty)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject2->intProperty)->get());

		// Checks keys with float keys
		$floatConverter = 0.1;

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject1->intProperty * $floatConverter, $dummyObject1, $dummyObject2));
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject2->intProperty * $floatConverter, $dummyObject2, $dummyObject3));
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject3->intProperty * $floatConverter, $dummyObject3, $dummyObject1));

		// Adds $dummyObject1 and $dummyObject2
		$hashMap->put ($dummyObject1->intProperty * $floatConverter, $dummyObject1);
		$hashMap->put ($dummyObject2->intProperty * $floatConverter, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->intProperty * $floatConverter)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->intProperty * $floatConverter)->get());

		// Tries to replace a non existing pair
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject1->intProperty * $floatConverter, $dummyObject2, $dummyObject3));

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->intProperty * $floatConverter)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->intProperty * $floatConverter)->get());

		// Replaces $dummyObject1 by $dummyObject2 and $dummyObject2 by $dummyObject3
		$this->assertTrue ($hashMap->replaceWithNewValue ($dummyObject1->intProperty * $floatConverter, $dummyObject1, $dummyObject2));
		$this->assertTrue ($hashMap->replaceWithNewValue ($dummyObject2->intProperty * $floatConverter, $dummyObject2, $dummyObject3));
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject1->intProperty * $floatConverter)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject2->intProperty * $floatConverter)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replaceWithNewValue
	 */
	public function testReplaceWithNewValueWithStringKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject1->stringProperty, $dummyObject1, $dummyObject2));
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject2->stringProperty, $dummyObject2, $dummyObject3));
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject3->stringProperty, $dummyObject3, $dummyObject1));

		// Adds $dummyObject1 and $dummyObject2
		$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
		$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->stringProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->stringProperty)->get());

		// Tries to replace a non existing pair
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject1->stringProperty, $dummyObject2, $dummyObject3));

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->stringProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->stringProperty)->get());

		// Replaces $dummyObject1 by $dummyObject2 and $dummyObject2 by $dummyObject3
		$this->assertTrue ($hashMap->replaceWithNewValue ($dummyObject1->stringProperty, $dummyObject1, $dummyObject2));
		$this->assertTrue ($hashMap->replaceWithNewValue ($dummyObject2->stringProperty, $dummyObject2, $dummyObject3));
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject1->stringProperty)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject2->stringProperty)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replaceWithNewValue
	 */
	public function testReplaceWithNewValueWithBooleanKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", TRUE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject1->boolProperty, $dummyObject1, $dummyObject2));
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject2->boolProperty, $dummyObject2, $dummyObject3));
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject3->boolProperty, $dummyObject3, $dummyObject1));

		// Adds $dummyObject1 and $dummyObject2
		$hashMap->put ($dummyObject1->boolProperty, $dummyObject1);
		$hashMap->put ($dummyObject2->boolProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->boolProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->boolProperty)->get());

		// Tries to replace a non existing pair
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject1->boolProperty, $dummyObject2, $dummyObject3));

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1->boolProperty)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2->boolProperty)->get());

		// Replaces $dummyObject1 by $dummyObject2 and $dummyObject2 by $dummyObject3
		$this->assertTrue ($hashMap->replaceWithNewValue ($dummyObject1->boolProperty, $dummyObject1, $dummyObject2));
		$this->assertTrue ($hashMap->replaceWithNewValue ($dummyObject2->boolProperty, $dummyObject2, $dummyObject3));
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject1->boolProperty)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject2->boolProperty)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::replaceWithNewValue
	 */
	public function testReplaceWithNewValueWithObjectKeys() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject1, $dummyObject1, $dummyObject2));
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject2, $dummyObject2, $dummyObject3));
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject3, $dummyObject3, $dummyObject1));

		// Adds $dummyObject1 and $dummyObject2
		$hashMap->put ($dummyObject1, $dummyObject1);
		$hashMap->put ($dummyObject2, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2)->get());

		// Tries to replace a non existing pair
		$this->assertFalse ($hashMap->replaceWithNewValue ($dummyObject1, $dummyObject2, $dummyObject3));

		$this->assertEquals ($dummyObject1, $hashMap->get ($dummyObject1)->get());
		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject2)->get());

		// Replaces $dummyObject1 by $dummyObject2 and $dummyObject2 by $dummyObject3
		$this->assertTrue ($hashMap->replaceWithNewValue ($dummyObject1, $dummyObject1, $dummyObject2));
		$this->assertTrue ($hashMap->replaceWithNewValue ($dummyObject2, $dummyObject2, $dummyObject3));
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($dummyObject2, $hashMap->get ($dummyObject1)->get());
		$this->assertEquals ($dummyObject3, $hashMap->get ($dummyObject2)->get());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::size
	 */
	public function testSizeOfHashMap() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEquals (0, $hashMap->size());
		$this->assertTrue ($hashMap->isEmpty());

		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
		$this->assertEquals (1, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
		$this->assertEquals (2, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		// Does not permits duplicates
		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);
		$this->assertEquals (3, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		// Removes every element
		$hashMap->removeByKey ($dummyObject3->intProperty);
		$this->assertEquals (2, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		$hashMap->removeByKey ($dummyObject2->intProperty);
		$this->assertEquals (1, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		$hashMap->removeByKey ($dummyObject1->intProperty);
		$this->assertEquals (0, $hashMap->size());
		$this->assertTrue ($hashMap->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\collection\map\HashMap::values
	 */
	public function testValuesOfHashMap() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEmpty ($hashMap->values());

		// Adds $dummyObject1
		$hashMap->put ($dummyObject1->intProperty, $dummyObject1);

		$arrayOfValues = $hashMap->values();
		$this->assertCount (1, $arrayOfValues);
		$this->assertEquals ($dummyObject1, $arrayOfValues[0]);

		// Adds $dummyObject2
		$hashMap->put ($dummyObject2->intProperty, $dummyObject2);

		$arrayOfValues = $hashMap->values();
		$this->assertCount (2, $arrayOfValues);
		$this->assertEmpty (array_diff ($arrayOfValues, array ($dummyObject1, $dummyObject2)));

		// Adds $dummyObject3
		$hashMap->put ($dummyObject3->intProperty, $dummyObject3);

		$arrayOfValues = $hashMap->values();
		$this->assertCount (3, $arrayOfValues);
		$this->assertEmpty (array_diff ($arrayOfValues, array ($dummyObject1, $dummyObject2, $dummyObject3)));
	}


	/**
	 * Checks if the given HashMaps contains the same elements (keys and values)
	 *
	 * @param HashMap $hashMap1
	 *    HashMap to check
	 * @param HashMap $hashMap2
	 *    HashMap to check
	 */
	private function checksEqualContentOfGivenHashMaps (HashMap $hashMap1, HashMap $hashMap2) {

		$this->assertEquals ($hashMap1->size(), $hashMap2->size());
		$this->assertGreaterThan (0, $hashMap1->size());

		foreach ($hashMap1->iterator() as $hashMap1Key => $hashMap1Value) {

			$hashMap2Value = $hashMap2->get ($hashMap1Key)->get();
			$this->assertNotNull ($hashMap2Value);
			$this->assertTrue ($hashMap1Value->equals ($hashMap2Value));
		}
	}

}

?>