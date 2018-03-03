<?php

namespace FunctionalPHP\test\iterable\map;

require_once '../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;
use FunctionalPHP\iterable\map\EntryHashMap;
use FunctionalPHP\iterable\map\HashMap;
use FunctionalPHP\iterable\map\Map;
use FunctionalPHP\test\Person;

/**
 * Class used to test FunctionalPHP\collection\map\HashMap
 */
final class HashMapTest extends TestCase {


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::__construct
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testConstructorWithInvalidTypeOfKeys() {

		$hashMap = new HashMap ("InvalidTypeOfKeys");
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::__construct
	 */
	public function testCreateEmptyHashMap() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::__construct
	 */
	public function testCreateNotEmptyHashMap() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap1 = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap1->put ($person1->name, $person1);
		$hashMap1->put ($person2->name, $person2);
		$hashMap1->put ($person3->name, $person3);

		$hashMap2 = new HashMap (Map::KEY_STRING_TYPE, $hashMap1);
		$this->assertFalse ($hashMap2->isEmpty());
		$this->assertEquals ($hashMap1->size(), $hashMap2->size());

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::clear
	 */
	public function testClearHashMap() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());

		$hashMap->put ($person1->age, $person1);
		$hashMap->put ($person2->age, $person2);
		$hashMap->put ($person3->age, $person3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (3, $hashMap->size());

		$hashMap->clear();
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfAbsent
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testComputeIfAbsentWithInvalidTypeOfKeys() {

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);

		$hashMap->computeIfAbsent (12, function (string $p1): Object {
			                              return NULL;
		                               });
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfAbsent
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testComputeIfAbsentWithClosureWithMoreThanOneParameter() {

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);

		$hashMap->computeIfAbsent ("stringKey", function (string $p1, int $p2): Object {
			                                       return NULL;
		                                        });
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfAbsent
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testComputeIfAbsentWithClosureWithParameterTypeDifferentOfHashMapKeys() {

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);

		$hashMap->computeIfAbsent ("stringKey", function (int $p1): Object {
			                                       return NULL;
		                                        });
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfAbsent
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testComputeIfAbsentWithClosureWithInvalidReturnedType() {

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);

		$hashMap->computeIfAbsent ("stringKey", function (string $p1): string {
			                                       return $p1;
		                                        });
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfAbsent
	 */
	public function testComputeIfAbsentWithExistingKey() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->put ($person1->name, $person1);
		$hashMap->put ($person2->name, $person2);

		$hashMap->computeIfAbsent ($person2->name, function (string $personName): Person {
			                                          return new Person ($personName, 25, TRUE);
		                                           });

		$this->assertEquals (2, $hashMap->size());
		$this->assertTrue ($hashMap->get ($person2->name)->isPresent());

		// Nothing has changed
		$this->assertEquals ($person2->name, $hashMap->get ($person2->name)->get()->name);
		$this->assertEquals ($person2->age, $hashMap->get ($person2->name)->get()->age);
		$this->assertEquals ($person2->isMale, $hashMap->get ($person2->name)->get()->isMale);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfAbsent
	 */
	public function testComputeIfAbsentWithNoExistingKey() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->put ($person1->name, $person1);
		$hashMap->put ($person2->name, $person2);

		$hashMap->computeIfAbsent ($person3->name, function (string $personName): Person {
			                                          return new Person ($personName, 25, FALSE);
		                                           });

		$this->assertEquals (3, $hashMap->size());
		$this->assertTrue ($hashMap->get ($person3->name)->isPresent());
		$this->assertEquals ($person3, $hashMap->get ($person3->name)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfPresent
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testComputeIfPresentWithInvalidTypeOfKeys() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		$hashMap->computeIfPresent ("stringKey", function (int $p1): Object {
			                                        return NULL;
		                                         });
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfPresent
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testComputeIfPresentWithClosureWithMoreThanOneParameter() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		$hashMap->computeIfPresent (12, function (int $p1, string $p2): Object {
			                               return NULL;
		                                });
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfPresent
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testComputeIfPresentWithClosureWithParameterTypeDifferentOfHashMapKeys() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		$hashMap->computeIfPresent (12, function (string $p1): Object {
			                               return NULL;
		                                });
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfPresent
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testComputeIfPresentWithClosureWithInvalidReturnedType() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		$hashMap->computeIfPresent (12, function (int $p1): int {
			                               return $p1;
		                                });
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfPresent
	 */
	public function testComputeIfPresentWithNoExistingKey() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$noExistingAge = 32;

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($person1->age, $person1);
		$hashMap->put ($person2->age, $person2);

		$hashMap->computeIfPresent ($noExistingAge, function (int $personAge): Person {
			                                            return new Person ("Unknown", $personAge, TRUE);
		                                             });

		$this->assertEquals (2, $hashMap->size());
		$this->assertFalse ($hashMap->get ($noExistingAge)->isPresent());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::computeIfPresent
	 */
	public function testComputeIfPresentWithExistingKey() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person2New = new Person ("Sara", $person2->age + 5, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($person1->age, $person1);
		$hashMap->put ($person2->age, $person2);

		$hashMap->computeIfPresent ($person2->age, function (int $personAge): Person {
			                                          return new Person ("Sara", $personAge + 5, FALSE);
		                                           });

		$this->assertEquals (2, $hashMap->size());
		$this->assertTrue ($hashMap->get ($person2->age)->isPresent());
		$this->assertFalse ($hashMap->get ($person2New->age)->isPresent());
		$this->assertEquals ($person2New, $hashMap->get ($person2->age)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithNumericKeysStoredAndGivenStringKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($person->age, $person);

		$hashMap->containsKey ($person->name);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithNumericKeysStoredAndGivenBoolKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($person->age, $person);

		$hashMap->containsKey ($person->isMale);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithNumericKeysStoredAndGivenObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($person->age, $person);

		$hashMap->containsKey ($person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithStringKeysStoredAndGivenBoolKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->put ($person->name, $person);

		$hashMap->containsKey ($person->isMale);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithStringKeysStoredAndGivenObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->put ($person->name, $person);

		$hashMap->containsKey ($person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsKey
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testContainsKeyWithBooleanKeysStoredAndGivenObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap->put ($person->isMale, $person);

		$hashMap->containsKey ($person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsKey
	 */
	public function testContainsNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->containsKey ($person1->age));
		$this->assertFalse ($hashMap->containsKey ($person2->age));
		$this->assertFalse ($hashMap->containsKey ($person3->age));

		$hashMap->put ($person1->age, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->age));
		$this->assertFalse ($hashMap->containsKey ($person2->age));
		$this->assertFalse ($hashMap->containsKey ($person3->age));

		$hashMap->put ($person2->age, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->age));
		$this->assertTrue ($hashMap->containsKey ($person2->age));
		$this->assertFalse ($hashMap->containsKey ($person3->age));

		$hashMap->put ($person3->age, $person3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->age));
		$this->assertTrue ($hashMap->containsKey ($person2->age));
		$this->assertTrue ($hashMap->containsKey ($person3->age));

		// Checks keys with float keys
		$floatConverter = 0.1;

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->containsKey ($person1->age * $floatConverter));
		$this->assertFalse ($hashMap->containsKey ($person2->age * $floatConverter));
		$this->assertFalse ($hashMap->containsKey ($person3->age * $floatConverter));

		$hashMap->put ($person1->age * $floatConverter, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->age * $floatConverter));
		$this->assertFalse ($hashMap->containsKey ($person2->age * $floatConverter));
		$this->assertFalse ($hashMap->containsKey ($person3->age * $floatConverter));

		$hashMap->put ($person2->age * $floatConverter, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->age * $floatConverter));
		$this->assertTrue ($hashMap->containsKey ($person2->age * $floatConverter));
		$this->assertFalse ($hashMap->containsKey ($person3->age * $floatConverter));

		$hashMap->put ($person3->age * $floatConverter, $person3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->age * $floatConverter));
		$this->assertTrue ($hashMap->containsKey ($person2->age * $floatConverter));
		$this->assertTrue ($hashMap->containsKey ($person3->age * $floatConverter));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsKey
	 */
	public function testContainsStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->containsKey ($person1->name));
		$this->assertFalse ($hashMap->containsKey ($person2->name));
		$this->assertFalse ($hashMap->containsKey ($person3->name));

		$hashMap->put ($person1->name, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->name));
		$this->assertFalse ($hashMap->containsKey ($person2->name));
		$this->assertFalse ($hashMap->containsKey ($person3->name));

		$hashMap->put ($person2->name, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->name));
		$this->assertTrue ($hashMap->containsKey ($person2->name));
		$this->assertFalse ($hashMap->containsKey ($person3->name));

		$hashMap->put ($person3->name, $person3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->name));
		$this->assertTrue ($hashMap->containsKey ($person2->name));
		$this->assertTrue ($hashMap->containsKey ($person3->name));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsKey
	 */
	public function testContainsBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->containsKey ($person1->isMale));
		$this->assertFalse ($hashMap->containsKey ($person2->isMale));

		$hashMap->put ($person1->isMale, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->isMale));
		$this->assertFalse ($hashMap->containsKey ($person2->isMale));

		$hashMap->put ($person2->isMale, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->isMale));
		$this->assertTrue ($hashMap->containsKey ($person2->isMale));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsKey
	 */
	public function testContainsObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->containsKey ($person1));
		$this->assertFalse ($hashMap->containsKey ($person2));
		$this->assertFalse ($hashMap->containsKey ($person3));

		$hashMap->put ($person1, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1));
		$this->assertFalse ($hashMap->containsKey ($person2));
		$this->assertFalse ($hashMap->containsKey ($person3));

		$hashMap->put ($person2, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1));
		$this->assertTrue ($hashMap->containsKey ($person2));
		$this->assertFalse ($hashMap->containsKey ($person3));

		$hashMap->put ($person3, $person3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1));
		$this->assertTrue ($hashMap->containsKey ($person2));
		$this->assertTrue ($hashMap->containsKey ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::containsValue
	 */
	public function testContainsValue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertFalse ($hashMap->containsValue ($person3));

		$hashMap->put ($person1->age, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertFalse ($hashMap->containsValue ($person3));

		$hashMap->put ($person2->age, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertFalse ($hashMap->containsValue ($person3));

		$hashMap->put ($person3->age, $person3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::equals
	 */
	public function testCheckEqualityWithHashMapOfDifferentTypeOfKeys() {

		$person = new Person ("John", 18, TRUE);

		// Map::KEY_NUMERIC_TYPE vs Map::KEY_STRING_TYPE
		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2 = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($person->age, $person);
		$hashMap2->put ($person->name, $person);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Map::KEY_NUMERIC_TYPE vs Map::KEY_BOOLEAN_TYPE
		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($person->age, $person);
		$hashMap2->put ($person->isMale, $person);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Map::KEY_NUMERIC_TYPE vs Map::KEY_OBJECT_TYPE
		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2 = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($person->age, $person);
		$hashMap2->put ($person, $person);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Map::KEY_STRING_TYPE vs Map::KEY_BOOLEAN_TYPE
		$hashMap1 = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap2 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($person->name, $person);
		$hashMap2->put ($person->isMale, $person);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Map::KEY_STRING_TYPE vs Map::KEY_OBJECT_TYPE
		$hashMap1 = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap2 = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($person->name, $person);
		$hashMap2->put ($person, $person);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Map::KEY_BOOLEAN_TYPE vs Map::KEY_OBJECT_TYPE
		$hashMap1 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap2 = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($person->isMale, $person);
		$hashMap2->put ($person, $person);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::equals
	 */
	public function testCheckEqualityWithHashMapWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($person1->age, $person1);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap2->put ($person2->age, $person2);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Both map have the same elements
		$hashMap1->put ($person2->age, $person2);
		$hashMap2->put ($person1->age, $person1);
		$this->assertEquals (2, $hashMap1->size());
		$this->assertEquals (2, $hashMap2->size());

		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds $person3 with different key
		$hashMap1->put ($person3->age + $person3->age, $person3);
		$hashMap2->put ($person3->age, $person3);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::equals
	 */
	public function testCheckEqualityWithHashMapWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap1 = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap2 = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($person1->name, $person1);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap2->put ($person2->name, $person2);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Both map have the same elements
		$hashMap1->put ($person2->name, $person2);
		$hashMap2->put ($person1->name, $person1);
		$this->assertEquals (2, $hashMap1->size());
		$this->assertEquals (2, $hashMap2->size());

		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds $person3 with different key
		$hashMap1->put ($person3->name.$person3->name, $person3);
		$hashMap2->put ($person3->name, $person3);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::equals
	 */
	public function testCheckEqualityWithHashMapWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap1 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap2 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($person1->isMale, $person1);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap2->put ($person2->isMale, $person2);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Both map have the same elements
		$hashMap1->put ($person2->isMale, $person2);
		$hashMap2->put ($person1->isMale, $person1);
		$this->assertEquals (2, $hashMap1->size());
		$this->assertEquals (2, $hashMap2->size());

		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds $person3 with different key
		$hashMap1->put ($person3->isMale, $person3);
		$hashMap2->put (!$person3->isMale, $person3);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::equals
	 */
	public function testCheckEqualityWithHashMapWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap1 = new HashMap (Map::KEY_OBJECT_TYPE);
		$hashMap2 = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap1->equals ($hashMap1));
		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$hashMap1->put ($person1, $person1);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		$hashMap2->put ($person2, $person2);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));

		// Both map have the same elements
		$hashMap1->put ($person2, $person2);
		$hashMap2->put ($person1, $person1);
		$this->assertEquals (2, $hashMap1->size());
		$this->assertEquals (2, $hashMap2->size());

		$this->assertTrue ($hashMap1->equals ($hashMap2));
		$this->assertTrue ($hashMap2->equals ($hashMap1));

		$this->checksEqualContentOfGivenHashMaps ($hashMap1, $hashMap2);

		// Adds $person3 with different key
		$hashMap1->put ($person3, $person3);
		$hashMap2->put ($person2, $person3);
		$this->assertFalse ($hashMap1->equals ($hashMap2));
		$this->assertFalse ($hashMap2->equals ($hashMap1));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithNumericKeysStoredAndGivenStringKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($person->age, $person);

		$hashMap->get ($person->name);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithNumericKeysStoredAndGivenBoolKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($person->age, $person);

		$hashMap->get ($person->isMale);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithNumericKeysStoredAndGivenObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->put ($person->age, $person);

		$hashMap->get ($person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithStringKeysStoredAndGivenBoolKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->put ($person->name, $person);

		$hashMap->get ($person->isMale);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithStringKeysStoredAndGivenObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->put ($person->name, $person);

		$hashMap->get ($person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::get
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testGetWithBooleanKeysStoredAndGivenObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap->put ($person->isMale, $person);

		$hashMap->get ($person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::get
	 */
	public function testGetWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->get ($person1->age)->isPresent());
		$this->assertFalse ($hashMap->get ($person2->age)->isPresent());
		$this->assertFalse ($hashMap->get ($person3->age)->isPresent());

		$hashMap->put ($person1->age, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->age)->get());
		$this->assertFalse ($hashMap->get ($person2->age)->isPresent());
		$this->assertFalse ($hashMap->get ($person3->age)->isPresent());

		$hashMap->put ($person2->age, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->age)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->age)->get());
		$this->assertFalse ($hashMap->get ($person3->age)->isPresent());

		$hashMap->put ($person3->age, $person3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->age)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->age)->get());
		$this->assertEquals ($person3, $hashMap->get ($person3->age)->get());

		// Checks keys with float keys
		$floatConverter = 0.1;

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->get ($person1->age * $floatConverter)->isPresent());
		$this->assertFalse ($hashMap->get ($person2->age * $floatConverter)->isPresent());
		$this->assertFalse ($hashMap->get ($person3->age * $floatConverter)->isPresent());

		$hashMap->put ($person1->age * $floatConverter, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->age * $floatConverter)->get());
		$this->assertFalse ($hashMap->get ($person2->age * $floatConverter)->isPresent());
		$this->assertFalse ($hashMap->get ($person3->age * $floatConverter)->isPresent());

		$hashMap->put ($person2->age * $floatConverter, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->age * $floatConverter)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->age * $floatConverter)->get());
		$this->assertFalse ($hashMap->get ($person3->age * $floatConverter)->isPresent());

		$hashMap->put ($person3->age * $floatConverter, $person3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->age * $floatConverter)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->age * $floatConverter)->get());
		$this->assertEquals ($person3, $hashMap->get ($person3->age * $floatConverter)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::get
	 */
	public function testGetWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->get ($person1->name)->isPresent());
		$this->assertFalse ($hashMap->get ($person2->name)->isPresent());
		$this->assertFalse ($hashMap->get ($person3->name)->isPresent());

		$hashMap->put ($person1->name, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->name)->get());
		$this->assertFalse ($hashMap->get ($person2->name)->isPresent());
		$this->assertFalse ($hashMap->get ($person3->name)->isPresent());

		$hashMap->put ($person2->name, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->name)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->name)->get());
		$this->assertFalse ($hashMap->get ($person3->name)->isPresent());

		$hashMap->put ($person3->name, $person3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->name)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->name)->get());
		$this->assertEquals ($person3, $hashMap->get ($person3->name)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::get
	 */
	public function testGetWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->get ($person1->isMale)->isPresent());
		$this->assertFalse ($hashMap->get ($person2->isMale)->isPresent());

		$hashMap->put ($person1->isMale, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->isMale)->get());
		$this->assertFalse ($hashMap->get ($person2->isMale)->isPresent());

		$hashMap->put ($person2->isMale, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->isMale)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->isMale)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::get
	 */
	public function testGetWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->get ($person1)->isPresent());
		$this->assertFalse ($hashMap->get ($person2)->isPresent());
		$this->assertFalse ($hashMap->get ($person3)->isPresent());

		$hashMap->put ($person1, $person1);
		$this->assertEquals (1, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1)->get());
		$this->assertFalse ($hashMap->get ($person2)->isPresent());
		$this->assertFalse ($hashMap->get ($person3)->isPresent());

		$hashMap->put ($person2, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2)->get());
		$this->assertFalse ($hashMap->get ($person3)->isPresent());

		$hashMap->put ($person3, $person3);
		$this->assertEquals (3, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2)->get());
		$this->assertEquals ($person3, $hashMap->get ($person3)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::getKeys
	 */
	public function testGetKeysWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEmpty ($hashMap->getKeys ($person1));
		$this->assertEmpty ($hashMap->getKeys ($person2));
		$this->assertEmpty ($hashMap->getKeys ($person3));

		// Adds $person1
		$hashMap->put ($person1->age, $person1);
		$this->assertEquals (1, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->age, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($person2));
		$this->assertEmpty ($hashMap->getKeys ($person3));

		// Adds $person2
		$hashMap->put ($person2->age, $person2);
		$this->assertEquals (2, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->age, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person2->age, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($person3));

		// Adds $person3
		$hashMap->put ($person3->age, $person3);
		$this->assertEquals (3, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->age, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person2->age, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($person3);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person3->age, $arrayOfKeys[0]);

		// Adds $person2 with different keys
		$hashMap->put ($person2->age*2, $person2);
		$hashMap->put ($person2->age*3, $person2);
		$this->assertEquals (5, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person2->age, $person2->age*2
				                                            ,$person2->age*3)));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::getKeys
	 */
	public function testGetKeysWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertEmpty ($hashMap->getKeys ($person1));
		$this->assertEmpty ($hashMap->getKeys ($person2));
		$this->assertEmpty ($hashMap->getKeys ($person3));

		// Adds $person1
		$hashMap->put ($person1->name, $person1);
		$this->assertEquals (1, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->name, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($person2));
		$this->assertEmpty ($hashMap->getKeys ($person3));

		// Adds $person2
		$hashMap->put ($person2->name, $person2);
		$this->assertEquals (2, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->name, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person2->name, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($person3));

		// Adds $person3
		$hashMap->put ($person3->name, $person3);
		$this->assertEquals (3, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->name, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person2->name, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($person3);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person3->name, $arrayOfKeys[0]);

		// Adds $person2 with different keys
		$hashMap->put ($person2->name."2", $person2);
		$hashMap->put ($person2->name."3", $person2);
		$this->assertEquals (5, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person2->name, $person2->name."2"
				                                            ,$person2->name."3")));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::getKeys
	 */
	public function testGetKeysWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertEmpty ($hashMap->getKeys ($person1));
		$this->assertEmpty ($hashMap->getKeys ($person2));

		// Adds $person1
		$hashMap->put ($person1->isMale, $person1);
		$this->assertEquals (1, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->isMale, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($person2));

		// Adds $person2
		$hashMap->put ($person2->isMale, $person2);
		$this->assertEquals (2, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->isMale, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person2->isMale, $arrayOfKeys[0]);

		// Adds $person2 with FALSE key
		$hashMap->put ($person1->isMale, $person2);
		$this->assertEquals (2, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person1->isMale, $person2->isMale)));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::getKeys
	 */
	public function testGetKeysWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertEmpty ($hashMap->getKeys ($person1));
		$this->assertEmpty ($hashMap->getKeys ($person2));
		$this->assertEmpty ($hashMap->getKeys ($person3));

		// Adds $person1
		$hashMap->put ($person1, $person1);
		$this->assertEquals (1, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($person2));
		$this->assertEmpty ($hashMap->getKeys ($person3));

		// Adds $person2
		$hashMap->put ($person2, $person2);
		$this->assertEquals (2, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person2, $arrayOfKeys[0]);

		$this->assertEmpty ($hashMap->getKeys ($person3));

		// Adds $person3
		$hashMap->put ($person3, $person3);
		$this->assertEquals (3, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person1);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person2, $arrayOfKeys[0]);

		$arrayOfKeys = $hashMap->getKeys ($person3);
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person3, $arrayOfKeys[0]);

		// Adds $person2 with different keys
		$hashMap->put ($person4, $person2);
		$this->assertEquals (4, $hashMap->size());

		$arrayOfKeys = $hashMap->getKeys ($person2);
		$this->assertCount (2, $arrayOfKeys);

		$isPersonsInArray = array (2 => FALSE, 4 => FALSE);
		foreach ($arrayOfKeys as $element) {

			if ($element->equals ($person2))
				$isPersonsInArray[2] = TRUE;

			elseif ($element->equals ($person4))
			$isPersonsInArray[4] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInArray[2]);
		$this->assertTrue ($isPersonsInArray[4]);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::getTypeOfKeys
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
	 * @covers FunctionalPHP\iterable\map\HashMap::hashCode
	 */
	public function testHashCodeWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$entryMap1 = new EntryHashMap ($person1->age, $person1);
		$entryMap2 = new EntryHashMap ($person2->age, $person2);
		$entryMap3 = new EntryHashMap ($person3->age, $person3);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEquals (0, $hashMap->hashCode());

		$hashMap->put ($person1->age, $person1);
		$this->assertEquals ($entryMap1->hashCode(), $hashMap->hashCode());

		$hashMap->put ($person2->age, $person2);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode(), $hashMap->hashCode());

		$hashMap->put ($person3->age, $person3);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode() + $entryMap3->hashCode()
				            ,$hashMap->hashCode());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::hashCode
	 */
	public function testHashCodeWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$entryMap1 = new EntryHashMap ($person1->name, $person1);
		$entryMap2 = new EntryHashMap ($person2->name, $person2);
		$entryMap3 = new EntryHashMap ($person3->name, $person3);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertEquals (0, $hashMap->hashCode());

		$hashMap->put ($person1->name, $person1);
		$this->assertEquals ($entryMap1->hashCode(), $hashMap->hashCode());

		$hashMap->put ($person2->name, $person2);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode(), $hashMap->hashCode());

		$hashMap->put ($person3->name, $person3);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode() + $entryMap3->hashCode()
				            ,$hashMap->hashCode());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::hashCode
	 */
	public function testHashCodeWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$entryMap1 = new EntryHashMap ($person1->isMale, $person1);
		$entryMap2 = new EntryHashMap ($person2->isMale, $person2);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertEquals (0, $hashMap->hashCode());

		$hashMap->put ($person1->isMale, $person1);
		$this->assertEquals ($entryMap1->hashCode(), $hashMap->hashCode());

		$hashMap->put ($person2->isMale, $person2);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode(), $hashMap->hashCode());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::hashCode
	 */
	public function testHashCodeWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$entryMap1 = new EntryHashMap ($person1, $person1);
		$entryMap2 = new EntryHashMap ($person2, $person2);
		$entryMap3 = new EntryHashMap ($person3, $person3);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertEquals (0, $hashMap->hashCode());

		$hashMap->put ($person1, $person1);
		$this->assertEquals ($entryMap1->hashCode(), $hashMap->hashCode());

		$hashMap->put ($person2, $person2);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode(), $hashMap->hashCode());

		$hashMap->put ($person3, $person3);
		$this->assertEquals ($entryMap1->hashCode() + $entryMap2->hashCode() + $entryMap3->hashCode()
				            ,$hashMap->hashCode());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::isEmpty
	 */
	public function testIsEmptyWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
    	$this->assertEquals (0, $hashMap->size());

    	$hashMap->put ($person1->age, $person1);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (1, $hashMap->size());

    	$hashMap->put ($person2->age, $person2);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (2, $hashMap->size());

    	$hashMap->put ($person3->age, $person3);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (3, $hashMap->size());

    	// Does not accept duplicate elements
    	$hashMap->put ($person3->age, $person3);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (3, $hashMap->size());

    	// Removes every element
    	$hashMap->removeByKey ($person3->age);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (2, $hashMap->size());

    	$hashMap->removeByKey ($person2->age);
    	$this->assertFalse ($hashMap->isEmpty());
    	$this->assertEquals (1, $hashMap->size());

    	$hashMap->removeByKey ($person1->age);
    	$this->assertTrue ($hashMap->isEmpty());
    	$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::isEmpty
	 */
	public function testIsEmptyWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());

		$hashMap->put ($person1->name, $person1);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->put ($person2->name, $person2);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		$hashMap->put ($person3->name, $person3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (3, $hashMap->size());

		// Does not accept duplicate elements
		$hashMap->put ($person3->name, $person3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (3, $hashMap->size());

		// Removes every element
		$hashMap->removeByKey ($person3->name);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		$hashMap->removeByKey ($person2->name);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->removeByKey ($person1->name);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::isEmpty
	 */
	public function testIsEmptyWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());

		$hashMap->put ($person1->isMale, $person1);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->put ($person2->isMale, $person2);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		// Does not accept duplicate elements
		$hashMap->put ($person2->isMale, $person2);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		// Removes every element
		$hashMap->removeByKey ($person2->isMale);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->removeByKey ($person1->isMale);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::isEmpty
	 */
	public function testIsEmptyWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());

		$hashMap->put ($person1, $person1);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->put ($person2, $person2);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		$hashMap->put ($person3, $person3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (3, $hashMap->size());

		// Does not accept duplicate elements
		$hashMap->put ($person3, $person3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (3, $hashMap->size());

		// Removes every element
		$hashMap->removeByKey ($person3);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (2, $hashMap->size());

		$hashMap->removeByKey ($person2);
		$this->assertFalse ($hashMap->isEmpty());
		$this->assertEquals (1, $hashMap->size());

		$hashMap->removeByKey ($person1);
		$this->assertTrue ($hashMap->isEmpty());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::iterator
	 */
	public function testIteratorWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		foreach ($hashMap->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $person1
		$hashMap->put ($person1->age, $person1);
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->age, $internalKey);
			$this->assertEquals ($person1, $internalValue);
		}

		// Adds another persons
		$hashMap->put ($person2->age, $person2);
		$hashMap->put ($person3->age, $person3);
		$this->assertEquals (3, $hashMap->size());

		$isPersonsInHashSet = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			if ($internalKey === $person1->age && $internalValue->equals ($person1))
				$isPersonsInHashSet[1] = TRUE;

			elseif ($internalKey === $person2->age && $internalValue->equals ($person2))
				$isPersonsInHashSet[2] = TRUE;

			elseif ($internalKey === $person3->age && $internalValue->equals ($person3))
				$isPersonsInHashSet[3] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInHashSet[1]);
		$this->assertTrue ($isPersonsInHashSet[2]);
		$this->assertTrue ($isPersonsInHashSet[3]);

		// Checks keys with float keys
		$floatConverter = 0.1;
		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		foreach ($hashMap->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $person1
		$hashMap->put ($person1->age * $floatConverter, $person1);
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->age * $floatConverter, $internalKey);
			$this->assertEquals ($person1, $internalValue);
		}

		// Adds another persons
		$hashMap->put ($person2->age * $floatConverter, $person2);
		$hashMap->put ($person3->age * $floatConverter, $person3);
		$this->assertEquals (3, $hashMap->size());

		$isPersonsInHashSet = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			if ($internalKey === ($person1->age * $floatConverter) &&
					$internalValue->equals ($person1))
				$isPersonsInHashSet[1] = TRUE;

			elseif ($internalKey === ($person2->age * $floatConverter) &&
					$internalValue->equals ($person2))
				$isPersonsInHashSet[2] = TRUE;

			elseif ($internalKey === ($person3->age * $floatConverter) &&
					$internalValue->equals ($person3))
				$isPersonsInHashSet[3] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInHashSet[1]);
		$this->assertTrue ($isPersonsInHashSet[2]);
		$this->assertTrue ($isPersonsInHashSet[3]);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::iterator
	 */
	public function testIteratorWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);

		foreach ($hashMap->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $person1
		$hashMap->put ($person1->name, $person1);
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->name, $internalKey);
			$this->assertEquals ($person1, $internalValue);
		}

		// Adds another persons
		$hashMap->put ($person2->name, $person2);
		$hashMap->put ($person3->name, $person3);
		$this->assertEquals (3, $hashMap->size());

		$isPersonsInHashSet = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			if ($internalKey === $person1->name && $internalValue->equals ($person1))
				$isPersonsInHashSet[1] = TRUE;

			elseif ($internalKey === $person2->name && $internalValue->equals ($person2))
				$isPersonsInHashSet[2] = TRUE;

			elseif ($internalKey === $person3->name && $internalValue->equals ($person3))
				$isPersonsInHashSet[3] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInHashSet[1]);
		$this->assertTrue ($isPersonsInHashSet[2]);
		$this->assertTrue ($isPersonsInHashSet[3]);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::iterator
	 */
	public function testIteratorWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);

		foreach ($hashMap->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $person1
		$hashMap->put ($person1->isMale, $person1);
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->isMale, $internalKey);
			$this->assertEquals ($person1, $internalValue);
		}

		// Adds $person2
		$hashMap->put ($person2->isMale, $person2);
		$this->assertEquals (2, $hashMap->size());

		$isPersonsInHashSet = array (1 => FALSE, 2 => FALSE);
		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			if ($internalKey === $person1->isMale && $internalValue->equals ($person1))
				$isPersonsInHashSet[1] = TRUE;

			elseif ($internalKey === $person2->isMale && $internalValue->equals ($person2))
				$isPersonsInHashSet[2] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInHashSet[1]);
		$this->assertTrue ($isPersonsInHashSet[2]);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::iterator
	 */
	public function testIteratorWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);

		foreach ($hashMap->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $person1
		$hashMap->put ($person1, $person1);
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1, $internalKey);
			$this->assertEquals ($person1, $internalValue);
		}

		// Adds another persons
		$hashMap->put ($person2, $person2);
		$hashMap->put ($person3, $person3);
		$this->assertEquals (3, $hashMap->size());

		$isPersonsInHashSet = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			if ($internalKey === $person1 && $internalValue->equals ($person1))
				$isPersonsInHashSet[1] = TRUE;

			elseif ($internalKey === $person2 && $internalValue->equals ($person2))
				$isPersonsInHashSet[2] = TRUE;

			elseif ($internalKey === $person3 && $internalValue->equals ($person3))
				$isPersonsInHashSet[3] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInHashSet[1]);
		$this->assertTrue ($isPersonsInHashSet[2]);
		$this->assertTrue ($isPersonsInHashSet[3]);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::keys
	 */
	public function testKeysWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $person1
		$hashMap->put ($person1->age, $person1);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->age, $arrayOfKeys[0]);

		// Adds $person2
		$hashMap->put ($person2->age, $person2);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person1->age, $person2->age)));

		// Adds $person3
		$hashMap->put ($person3->age, $person3);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person1->age, $person2->age
				                                            ,$person3->age)));
		// Checks keys with float keys
		$floatConverter = 0.1;
		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		// Adds $person1
		$hashMap->put ($person1->age * $floatConverter, $person1);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->age * $floatConverter, $arrayOfKeys[0]);

		// Adds $person2
		$hashMap->put ($person2->age * $floatConverter, $person2);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person1->age * $floatConverter
				                                            ,$person2->age * $floatConverter)));

		// Adds $person3
		$hashMap->put ($person3->age * $floatConverter, $person3);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person1->age * $floatConverter
				                                            ,$person2->age * $floatConverter
				                                            ,$person3->age * $floatConverter)));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::keys
	 */
	public function testKeysWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $person1
		$hashMap->put ($person1->name, $person1);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->name, $arrayOfKeys[0]);

		// Adds $person2
		$hashMap->put ($person2->name, $person2);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person1->name
				                                            ,$person2->name)));
		// Adds $person3
		$hashMap->put ($person3->name, $person3);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person1->name
				                                            ,$person2->name
				                                            ,$person3->name)));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::keys
	 */
	public function testKeysWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $person1
		$hashMap->put ($person1->isMale, $person1);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1->isMale, $arrayOfKeys[0]);

		// Adds $person2
		$hashMap->put ($person2->isMale, $person2);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person1->isMale, $person2->isMale)));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::keys
	 */
	public function testKeysWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $person1
		$hashMap->put ($person1, $person1);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (1, $arrayOfKeys);
		$this->assertEquals ($person1, $arrayOfKeys[0]);

		// Adds $person2
		$hashMap->put ($person2, $person2);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (2, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person1, $person2)));

		// Adds $person3
		$hashMap->put ($person3, $person3);

		$arrayOfKeys = $hashMap->keys();
		$this->assertCount (3, $arrayOfKeys);
		$this->assertEmpty (array_diff ($arrayOfKeys, array ($person1, $person2, $person3)));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::put
	 */
	public function testPutWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $person1
		$this->assertFalse ($hashMap->put ($person1->age, $person1)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->age, $internalKey);
			$this->assertEquals ($person1, $internalValue);
		}

		// Overwrites $person1->age with $person2
		$this->assertEquals ($person1, $hashMap->put ($person1->age, $person2)->get());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->age, $internalKey);
			$this->assertEquals ($person2, $internalValue);
		}

		// Adds $person3 and $person4
		$this->assertFalse ($hashMap->put ($person3->age, $person3)->isPresent());
		$this->assertFalse ($hashMap->put ($person4->age, $person4)->isPresent());
		$this->assertEquals (3, $hashMap->size());

		// Checks the content of the map
		$this->assertEquals ($person2, $hashMap->get ($person1->age)->get());
		$this->assertEquals ($person3, $hashMap->get ($person3->age)->get());
		$this->assertEquals ($person4, $hashMap->get ($person4->age)->get());

		// Checks keys with float keys
		$floatConverter = 0.1;

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);

		// Adds $person1
		$this->assertFalse ($hashMap->put ($person1->age * $floatConverter, $person1)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->age * $floatConverter, $internalKey);
			$this->assertEquals ($person1, $internalValue);
		}

		// Overwrites $person1->age with $person2
		$this->assertEquals ($person1, $hashMap->put ($person1->age * $floatConverter, $person2)->get());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->age * $floatConverter, $internalKey);
			$this->assertEquals ($person2, $internalValue);
		}

		// Adds $person3 and $person4
		$this->assertFalse ($hashMap->put ($person3->age * $floatConverter, $person3)->isPresent());
		$this->assertFalse ($hashMap->put ($person4->age * $floatConverter, $person4)->isPresent());
		$this->assertEquals (3, $hashMap->size());

		// Checks the content of the map
		$this->assertEquals ($person2, $hashMap->get ($person1->age * $floatConverter)->get());
		$this->assertEquals ($person3, $hashMap->get ($person3->age * $floatConverter)->get());
		$this->assertEquals ($person4, $hashMap->get ($person4->age * $floatConverter)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::put
	 */
	public function testPutWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $person1
		$this->assertFalse ($hashMap->put ($person1->name, $person1)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->name, $internalKey);
			$this->assertEquals ($person1, $internalValue);
		}

		// Overwrites $person1->name with $person2
		$this->assertEquals ($person1, $hashMap->put ($person1->name, $person2)->get());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->name, $internalKey);
			$this->assertEquals ($person2, $internalValue);
		}

		// Adds $person3 and $person4
		$this->assertFalse ($hashMap->put ($person3->name, $person3)->isPresent());
		$this->assertFalse ($hashMap->put ($person4->name, $person4)->isPresent());
		$this->assertEquals (3, $hashMap->size());

		// Checks the content of the map
		$this->assertEquals ($person2, $hashMap->get ($person1->name)->get());
		$this->assertEquals ($person3, $hashMap->get ($person3->name)->get());
		$this->assertEquals ($person4, $hashMap->get ($person4->name)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::put
	 */
	public function testPutWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $person1
		$this->assertFalse ($hashMap->put ($person1->isMale, $person1)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->isMale, $internalKey);
			$this->assertEquals ($person1, $internalValue);
		}

		// Overwrites $person1->isMale with $person2
		$this->assertEquals ($person1, $hashMap->put ($person1->isMale, $person2)->get());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1->isMale, $internalKey);
			$this->assertEquals ($person2, $internalValue);
		}

		// Adds $person3
		$this->assertFalse ($hashMap->put ($person3->isMale, $person3)->isPresent());
		$this->assertEquals (2, $hashMap->size());

		// Checks the content of the map
		$this->assertEquals ($person2, $hashMap->get ($person1->isMale)->get());
		$this->assertEquals ($person3, $hashMap->get ($person3->isMale)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::put
	 */
	public function testPutWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertEmpty ($hashMap->keys());

		// Adds $person1
		$this->assertFalse ($hashMap->put ($person1, $person1)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1, $internalKey);
			$this->assertEquals ($person1, $internalValue);
		}

		// Overwrites $person1 with $person2
		$this->assertEquals ($person1, $hashMap->put ($person1, $person2)->get());
		$this->assertEquals (1, $hashMap->size());

		foreach ($hashMap->iterator() as $internalKey => $internalValue) {

			$this->assertEquals ($person1, $internalKey);
			$this->assertEquals ($person2, $internalValue);
		}

		// Adds $person3 and $person4
		$this->assertFalse ($hashMap->put ($person3, $person3)->isPresent());
		$this->assertFalse ($hashMap->put ($person4, $person4)->isPresent());
		$this->assertEquals (3, $hashMap->size());

		// Checks the content of the map
		$this->assertEquals ($person2, $hashMap->get ($person1)->get());
		$this->assertEquals ($person3, $hashMap->get ($person3)->get());
		$this->assertEquals ($person4, $hashMap->get ($person4)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithNumericKeysStoredAndGivenMapWithStringKey() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_STRING_TYPE));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithNumericKeysStoredAndGivenMapWithBooleanKey() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_BOOLEAN_TYPE));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithNumericKeysStoredAndGivenMapWithObjectKey() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_OBJECT_TYPE));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithStringKeysStoredAndGivenMapWithBooleanKey() {

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_BOOLEAN_TYPE));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithStringKeysStoredAndGivenMapWithObjectKey() {

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_OBJECT_TYPE));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testPutAllWithBooleanKeysStoredAndGivenMapWithObjectKey() {

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap->putAll (new HashMap (Map::KEY_OBJECT_TYPE));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 */
	public function testPutAllElementsOfEmptyMap() {

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->putAll (new HashMap (Map::KEY_NUMERIC_TYPE)));
		$this->assertTrue ($hashMap->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 */
	public function testPutAllWithNumericKeysAndHashMap() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap1 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertTrue ($hashMap1->isEmpty());

		$hashMap2 = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap2->put ($person1->age, $person1);
		$hashMap2->put ($person2->age, $person2);
		$hashMap2->put ($person3->age, $person3);
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
		$hashMap2->put ($person1->age * $floatConverter, $person1);
		$hashMap2->put ($person2->age * $floatConverter, $person2);
		$hashMap2->put ($person3->age * $floatConverter, $person3);
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
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 */
	public function testPutAllWithStringKeysAndHashMap() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap1 = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertTrue ($hashMap1->isEmpty());

		$hashMap2 = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap2->put ($person1->name, $person1);
		$hashMap2->put ($person2->name, $person2);
		$hashMap2->put ($person3->name, $person3);
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
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 */
	public function testPutAllWithBooleanKeysAndHashMap() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap1 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertTrue ($hashMap1->isEmpty());

		$hashMap2 = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap2->put ($person1->isMale, $person1);
		$hashMap2->put ($person2->isMale, $person2);
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
	 * @covers FunctionalPHP\iterable\map\HashMap::putAll
	 */
	public function testPutAllWithObjectKeysAndHashMap() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap1 = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertTrue ($hashMap1->isEmpty());

		$hashMap2 = new HashMap (Map::KEY_OBJECT_TYPE);
		$hashMap2->put ($person1, $person1);
		$hashMap2->put ($person2, $person2);
		$hashMap2->put ($person3, $person3);
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
	 * @covers FunctionalPHP\iterable\map\HashMap::remove
	 */
	public function testRemoveWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);
		$person5 = new Person ("Zach", 19, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->remove ($person1->age, $person1));
		$this->assertFalse ($hashMap->remove ($person2->age, $person2));
		$this->assertFalse ($hashMap->remove ($person3->age, $person3));
		$this->assertFalse ($hashMap->remove ($person4->age, $person4));
		$this->assertFalse ($hashMap->remove ($person5->age, $person5));

		$hashMap->put ($person1->age, $person1);
		$hashMap->put ($person2->age, $person2);
		$hashMap->put ($person3->age, $person3);
		$hashMap->put ($person4->age, $person4);
		$hashMap->put ($person5->age, $person5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->age));
		$this->assertTrue ($hashMap->containsKey ($person2->age));
		$this->assertTrue ($hashMap->containsKey ($person3->age));
		$this->assertTrue ($hashMap->containsKey ($person4->age));
		$this->assertTrue ($hashMap->containsKey ($person5->age));

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person1
		$this->assertTrue ($hashMap->remove ($person1->age, $person1));
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->age));
		$this->assertTrue ($hashMap->containsKey ($person2->age));
		$this->assertTrue ($hashMap->containsKey ($person3->age));
		$this->assertTrue ($hashMap->containsKey ($person4->age));
		$this->assertTrue ($hashMap->containsKey ($person5->age));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person2 and $person5
		$this->assertTrue ($hashMap->remove ($person2->age, $person2));
		$this->assertTrue ($hashMap->remove ($person5->age, $person5));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->age));
		$this->assertFalse ($hashMap->containsKey ($person2->age));
		$this->assertTrue ($hashMap->containsKey ($person3->age));
		$this->assertTrue ($hashMap->containsKey ($person4->age));
		$this->assertFalse ($hashMap->containsKey ($person5->age));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Tries to remove $person2 again and an incorrect pair key-value
		$this->assertFalse ($hashMap->remove ($person2->age, $person2));
		$this->assertFalse ($hashMap->remove ($person3->age, $person4));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->age));
		$this->assertFalse ($hashMap->containsKey ($person2->age));
		$this->assertTrue ($hashMap->containsKey ($person3->age));
		$this->assertTrue ($hashMap->containsKey ($person4->age));
		$this->assertFalse ($hashMap->containsKey ($person5->age));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Removes $person3 and $person4
		$this->assertTrue ($hashMap->remove ($person3->age, $person3));
		$this->assertTrue ($hashMap->remove ($person4->age, $person4));
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::remove
	 */
	public function testRemoveWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);
		$person5 = new Person ("Zach", 19, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->remove ($person1->name, $person1));
		$this->assertFalse ($hashMap->remove ($person2->name, $person2));
		$this->assertFalse ($hashMap->remove ($person3->name, $person3));
		$this->assertFalse ($hashMap->remove ($person4->name, $person4));
		$this->assertFalse ($hashMap->remove ($person5->name, $person5));

		$hashMap->put ($person1->name, $person1);
		$hashMap->put ($person2->name, $person2);
		$hashMap->put ($person3->name, $person3);
		$hashMap->put ($person4->name, $person4);
		$hashMap->put ($person5->name, $person5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->name));
		$this->assertTrue ($hashMap->containsKey ($person2->name));
		$this->assertTrue ($hashMap->containsKey ($person3->name));
		$this->assertTrue ($hashMap->containsKey ($person4->name));
		$this->assertTrue ($hashMap->containsKey ($person5->name));

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person1
		$this->assertTrue ($hashMap->remove ($person1->name, $person1));
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->name));
		$this->assertTrue ($hashMap->containsKey ($person2->name));
		$this->assertTrue ($hashMap->containsKey ($person3->name));
		$this->assertTrue ($hashMap->containsKey ($person4->name));
		$this->assertTrue ($hashMap->containsKey ($person5->name));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person2 and $person5
		$this->assertTrue ($hashMap->remove ($person2->name, $person2));
		$this->assertTrue ($hashMap->remove ($person5->name, $person5));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->name));
		$this->assertFalse ($hashMap->containsKey ($person2->name));
		$this->assertTrue ($hashMap->containsKey ($person3->name));
		$this->assertTrue ($hashMap->containsKey ($person4->name));
		$this->assertFalse ($hashMap->containsKey ($person5->name));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Tries to remove $person2 again and an incorrect pair key-value
		$this->assertFalse ($hashMap->remove ($person2->name, $person2));
		$this->assertFalse ($hashMap->remove ($person3->name, $person4));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->name));
		$this->assertFalse ($hashMap->containsKey ($person2->name));
		$this->assertTrue ($hashMap->containsKey ($person3->name));
		$this->assertTrue ($hashMap->containsKey ($person4->name));
		$this->assertFalse ($hashMap->containsKey ($person5->name));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Removes $person3 and $person4
		$this->assertTrue ($hashMap->remove ($person3->name, $person3));
		$this->assertTrue ($hashMap->remove ($person4->name, $person4));
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::remove
	 */
	public function testRemoveWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->remove ($person1->isMale, $person1));
		$this->assertFalse ($hashMap->remove ($person2->isMale, $person2));

		$hashMap->put ($person1->isMale, $person1);
		$hashMap->put ($person2->isMale, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->isMale));
		$this->assertTrue ($hashMap->containsKey ($person2->isMale));

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));

		// Removes $person1
		$this->assertTrue ($hashMap->remove ($person1->isMale, $person1));
		$this->assertEquals (1, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->isMale));
		$this->assertTrue ($hashMap->containsKey ($person2->isMale));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));

		// Tries to remove $person1 again and an incorrect pair key-value
		$this->assertFalse ($hashMap->remove ($person1->isMale, $person1));
		$this->assertFalse ($hashMap->remove ($person2->isMale, $person1));
		$this->assertEquals (1, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->isMale));
		$this->assertTrue ($hashMap->containsKey ($person2->isMale));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));

		// Removes $person2
		$this->assertTrue ($hashMap->remove ($person2->isMale, $person2));
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::remove
	 */
	public function testRemoveWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);
		$person5 = new Person ("Zach", 19, TRUE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->remove ($person1, $person1));
		$this->assertFalse ($hashMap->remove ($person2, $person2));
		$this->assertFalse ($hashMap->remove ($person3, $person3));
		$this->assertFalse ($hashMap->remove ($person4, $person4));
		$this->assertFalse ($hashMap->remove ($person5, $person5));

		$hashMap->put ($person1, $person1);
		$hashMap->put ($person2, $person2);
		$hashMap->put ($person3, $person3);
		$hashMap->put ($person4, $person4);
		$hashMap->put ($person5, $person5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1));
		$this->assertTrue ($hashMap->containsKey ($person2));
		$this->assertTrue ($hashMap->containsKey ($person3));
		$this->assertTrue ($hashMap->containsKey ($person4));
		$this->assertTrue ($hashMap->containsKey ($person5));

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person1
		$this->assertTrue ($hashMap->remove ($person1, $person1));
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1));
		$this->assertTrue ($hashMap->containsKey ($person2));
		$this->assertTrue ($hashMap->containsKey ($person3));
		$this->assertTrue ($hashMap->containsKey ($person4));
		$this->assertTrue ($hashMap->containsKey ($person5));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person2 and $person5
		$this->assertTrue ($hashMap->remove ($person2, $person2));
		$this->assertTrue ($hashMap->remove ($person5, $person5));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1));
		$this->assertFalse ($hashMap->containsKey ($person2));
		$this->assertTrue ($hashMap->containsKey ($person3));
		$this->assertTrue ($hashMap->containsKey ($person4));
		$this->assertFalse ($hashMap->containsKey ($person5));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Tries to remove $person2 again and an incorrect pair key-value
		$this->assertFalse ($hashMap->remove ($person2, $person2));
		$this->assertFalse ($hashMap->remove ($person3, $person4));
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1));
		$this->assertFalse ($hashMap->containsKey ($person2));
		$this->assertTrue ($hashMap->containsKey ($person3));
		$this->assertTrue ($hashMap->containsKey ($person4));
		$this->assertFalse ($hashMap->containsKey ($person5));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Removes $person3 and $person4
		$this->assertTrue ($hashMap->remove ($person3, $person3));
		$this->assertTrue ($hashMap->remove ($person4, $person4));
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::removeByKey
	 */
	public function testRemoveByKeyWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);
		$person5 = new Person ("Zach", 19, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->removeByKey ($person1->age)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person2->age)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person3->age)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person4->age)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person5->age)->isPresent());

		$hashMap->put ($person1->age, $person1);
		$hashMap->put ($person2->age, $person2);
		$hashMap->put ($person3->age, $person3);
		$hashMap->put ($person4->age, $person4);
		$hashMap->put ($person5->age, $person5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->age));
		$this->assertTrue ($hashMap->containsKey ($person2->age));
		$this->assertTrue ($hashMap->containsKey ($person3->age));
		$this->assertTrue ($hashMap->containsKey ($person4->age));
		$this->assertTrue ($hashMap->containsKey ($person5->age));

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person1
		$this->assertEquals ($person1, $hashMap->removeByKey ($person1->age)->get());
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->age));
		$this->assertTrue ($hashMap->containsKey ($person2->age));
		$this->assertTrue ($hashMap->containsKey ($person3->age));
		$this->assertTrue ($hashMap->containsKey ($person4->age));
		$this->assertTrue ($hashMap->containsKey ($person5->age));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person2 and $person5
		$this->assertEquals ($person2, $hashMap->removeByKey ($person2->age)->get());
		$this->assertEquals ($person5, $hashMap->removeByKey ($person5->age)->get());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->age));
		$this->assertFalse ($hashMap->containsKey ($person2->age));
		$this->assertTrue ($hashMap->containsKey ($person3->age));
		$this->assertTrue ($hashMap->containsKey ($person4->age));
		$this->assertFalse ($hashMap->containsKey ($person5->age));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Tries to remove $person2 again
		$this->assertFalse ($hashMap->removeByKey ($person2->age)->isPresent());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->age));
		$this->assertFalse ($hashMap->containsKey ($person2->age));
		$this->assertTrue ($hashMap->containsKey ($person3->age));
		$this->assertTrue ($hashMap->containsKey ($person4->age));
		$this->assertFalse ($hashMap->containsKey ($person5->age));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Removes $person3 and $person4
		$this->assertEquals ($person3, $hashMap->removeByKey ($person3->age)->get());
		$this->assertEquals ($person4, $hashMap->removeByKey ($person4->age)->get());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::removeByKey
	 */
	public function testRemoveByKeyWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);
		$person5 = new Person ("Zach", 19, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->removeByKey ($person1->name)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person2->name)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person3->name)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person4->name)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person5->name)->isPresent());

		$hashMap->put ($person1->name, $person1);
		$hashMap->put ($person2->name, $person2);
		$hashMap->put ($person3->name, $person3);
		$hashMap->put ($person4->name, $person4);
		$hashMap->put ($person5->name, $person5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->name));
		$this->assertTrue ($hashMap->containsKey ($person2->name));
		$this->assertTrue ($hashMap->containsKey ($person3->name));
		$this->assertTrue ($hashMap->containsKey ($person4->name));
		$this->assertTrue ($hashMap->containsKey ($person5->name));

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person1
		$this->assertEquals ($person1, $hashMap->removeByKey ($person1->name)->get());
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->name));
		$this->assertTrue ($hashMap->containsKey ($person2->name));
		$this->assertTrue ($hashMap->containsKey ($person3->name));
		$this->assertTrue ($hashMap->containsKey ($person4->name));
		$this->assertTrue ($hashMap->containsKey ($person5->name));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person2 and $person5
		$this->assertEquals ($person2, $hashMap->removeByKey ($person2->name)->get());
		$this->assertEquals ($person5, $hashMap->removeByKey ($person5->name)->get());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->name));
		$this->assertFalse ($hashMap->containsKey ($person2->name));
		$this->assertTrue ($hashMap->containsKey ($person3->name));
		$this->assertTrue ($hashMap->containsKey ($person4->name));
		$this->assertFalse ($hashMap->containsKey ($person5->name));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Tries to remove $person2 again
		$this->assertFalse ($hashMap->removeByKey ($person2->name)->isPresent());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->name));
		$this->assertFalse ($hashMap->containsKey ($person2->name));
		$this->assertTrue ($hashMap->containsKey ($person3->name));
		$this->assertTrue ($hashMap->containsKey ($person4->name));
		$this->assertFalse ($hashMap->containsKey ($person5->name));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Removes $person3 and $person4
		$this->assertEquals ($person3, $hashMap->removeByKey ($person3->name)->get());
		$this->assertEquals ($person4, $hashMap->removeByKey ($person4->name)->get());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::removeByKey
	 */
	public function testRemoveByKeyWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->removeByKey ($person1->isMale)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person2->isMale)->isPresent());

		$hashMap->put ($person1->isMale, $person1);
		$hashMap->put ($person2->isMale, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->isMale));
		$this->assertTrue ($hashMap->containsKey ($person2->isMale));

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));

		// Removes $person1
		$this->assertEquals ($person1, $hashMap->removeByKey ($person1->isMale)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->isMale));
		$this->assertTrue ($hashMap->containsKey ($person2->isMale));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));

		// Tries to remove $person1 again
		$this->assertFalse ($hashMap->removeByKey ($person1->isMale)->isPresent());
		$this->assertEquals (1, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1->isMale));
		$this->assertTrue ($hashMap->containsKey ($person2->isMale));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));

		// Removes $person2
		$this->assertEquals ($person2, $hashMap->removeByKey ($person2->isMale)->get());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::removeByKey
	 */
	public function testRemoveByKeyWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);
		$person5 = new Person ("Zach", 19, TRUE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->removeByKey ($person1)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person2)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person3)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person4)->isPresent());
		$this->assertFalse ($hashMap->removeByKey ($person5)->isPresent());

		$hashMap->put ($person1, $person1);
		$hashMap->put ($person2, $person2);
		$hashMap->put ($person3, $person3);
		$hashMap->put ($person4, $person4);
		$hashMap->put ($person5, $person5);
		$this->assertEquals (5, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1));
		$this->assertTrue ($hashMap->containsKey ($person2));
		$this->assertTrue ($hashMap->containsKey ($person3));
		$this->assertTrue ($hashMap->containsKey ($person4));
		$this->assertTrue ($hashMap->containsKey ($person5));

		$this->assertTrue ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person1
		$this->assertEquals ($person1, $hashMap->removeByKey ($person1)->get());
		$this->assertEquals (4, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1));
		$this->assertTrue ($hashMap->containsKey ($person2));
		$this->assertTrue ($hashMap->containsKey ($person3));
		$this->assertTrue ($hashMap->containsKey ($person4));
		$this->assertTrue ($hashMap->containsKey ($person5));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertTrue ($hashMap->containsValue ($person5));

		// Removes $person2 and $person5
		$this->assertEquals ($person2, $hashMap->removeByKey ($person2)->get());
		$this->assertEquals ($person5, $hashMap->removeByKey ($person5)->get());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1));
		$this->assertFalse ($hashMap->containsKey ($person2));
		$this->assertTrue ($hashMap->containsKey ($person3));
		$this->assertTrue ($hashMap->containsKey ($person4));
		$this->assertFalse ($hashMap->containsKey ($person5));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Tries to remove $person2 again
		$this->assertFalse ($hashMap->removeByKey ($person2)->isPresent());
		$this->assertEquals (2, $hashMap->size());

		$this->assertFalse ($hashMap->containsKey ($person1));
		$this->assertFalse ($hashMap->containsKey ($person2));
		$this->assertTrue ($hashMap->containsKey ($person3));
		$this->assertTrue ($hashMap->containsKey ($person4));
		$this->assertFalse ($hashMap->containsKey ($person5));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
		$this->assertTrue ($hashMap->containsValue ($person4));
		$this->assertFalse ($hashMap->containsValue ($person5));

		// Removes $person3 and $person4
		$this->assertEquals ($person3, $hashMap->removeByKey ($person3)->get());
		$this->assertEquals ($person4, $hashMap->removeByKey ($person4)->get());
		$this->assertEquals (0, $hashMap->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNumericKeysStoredAndGivenMapWithStringKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replace ($person->name, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNumericKeysStoredAndGivenMapWithBooleanKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replace ($person->isMale, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNumericKeysStoredAndGivenMapWithObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replace ($person, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithStringKeysStoredAndGivenMapWithBooleanKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->replace ($person->isMale, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithStringKeysStoredAndGivenMapWithObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->replace ($person, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replace
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithBooleanKeysStoredAndGivenMapWithObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap->replace ($person, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replace
	 */
	public function testReplaceWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->replace ($person1->age, $person1)->isPresent());
		$this->assertFalse ($hashMap->replace ($person2->age, $person2)->isPresent());
		$this->assertFalse ($hashMap->replace ($person3->age, $person3)->isPresent());

		// Adds $person1
		$hashMap->put ($person1->age, $person1);
		$this->assertEquals (1, $hashMap->size());

		// Replaces $person1 by $person2
		$this->assertEquals ($person1, $hashMap->replace ($person1->age, $person2)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->age));
		$this->assertFalse ($hashMap->containsKey ($person2->age));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));

		// Replaces $person2 by $person3
		$this->assertEquals ($person2, $hashMap->replace ($person1->age, $person3)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->age));
		$this->assertFalse ($hashMap->containsKey ($person2->age));
		$this->assertFalse ($hashMap->containsKey ($person3->age));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replace
	 */
	public function testReplaceWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->replace ($person1->name, $person1)->isPresent());
		$this->assertFalse ($hashMap->replace ($person2->name, $person2)->isPresent());
		$this->assertFalse ($hashMap->replace ($person3->name, $person3)->isPresent());

		// Adds $person1
		$hashMap->put ($person1->name, $person1);
		$this->assertEquals (1, $hashMap->size());

		// Replaces $person1 by $person2
		$this->assertEquals ($person1, $hashMap->replace ($person1->name, $person2)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->name));
		$this->assertFalse ($hashMap->containsKey ($person2->name));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));

		// Replaces $person2 by $person3
		$this->assertEquals ($person2, $hashMap->replace ($person1->name, $person3)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->name));
		$this->assertFalse ($hashMap->containsKey ($person2->name));
		$this->assertFalse ($hashMap->containsKey ($person3->name));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replace
	 */
	public function testReplaceWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->replace ($person1->isMale, $person1)->isPresent());
		$this->assertFalse ($hashMap->replace ($person2->isMale, $person2)->isPresent());

		// Adds $person1
		$hashMap->put ($person1->isMale, $person1);
		$this->assertEquals (1, $hashMap->size());

		// Replaces $person1 by $person2
		$this->assertEquals ($person1, $hashMap->replace ($person1->isMale, $person2)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1->isMale));
		$this->assertFalse ($hashMap->containsKey ($person2->isMale));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replace
	 */
	public function testReplaceWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->replace ($person1, $person1)->isPresent());
		$this->assertFalse ($hashMap->replace ($person2, $person2)->isPresent());
		$this->assertFalse ($hashMap->replace ($person3, $person3)->isPresent());

		// Adds $person1
		$hashMap->put ($person1, $person1);
		$this->assertEquals (1, $hashMap->size());

		// Replaces $person1 by $person2
		$this->assertEquals ($person1, $hashMap->replace ($person1, $person2)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1));
		$this->assertFalse ($hashMap->containsKey ($person2));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertTrue ($hashMap->containsValue ($person2));

		// Replaces $person2 by $person3
		$this->assertEquals ($person2, $hashMap->replace ($person1, $person3)->get());
		$this->assertEquals (1, $hashMap->size());

		$this->assertTrue ($hashMap->containsKey ($person1));
		$this->assertFalse ($hashMap->containsKey ($person2));
		$this->assertFalse ($hashMap->containsKey ($person3));

		$this->assertFalse ($hashMap->containsValue ($person1));
		$this->assertFalse ($hashMap->containsValue ($person2));
		$this->assertTrue ($hashMap->containsValue ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithNumericKeysStoredAndGivenMapWithStringKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replaceWithNewValue ($person->name, $person, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithNumericKeysStoredAndGivenMapWithBooleanKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replaceWithNewValue ($person->isMale, $person, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithNumericKeysStoredAndGivenMapWithObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$hashMap->replaceWithNewValue ($person, $person, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithStringKeysStoredAndGivenMapWithBooleanKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->replaceWithNewValue ($person->isMale, $person, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithStringKeysStoredAndGivenMapWithObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$hashMap->replaceWithNewValue ($person, $person, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replaceWithNewValue
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testReplaceWithNewValueWithBooleanKeysStoredAndGivenMapWithObjectKey() {

		$person = new Person ("John", 18, TRUE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$hashMap->replaceWithNewValue ($person, $person, $person);
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replaceWithNewValue
	 */
	public function testReplaceWithNewValueWithNumericKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->replaceWithNewValue ($person1->age, $person1, $person2));
		$this->assertFalse ($hashMap->replaceWithNewValue ($person2->age, $person2, $person3));
		$this->assertFalse ($hashMap->replaceWithNewValue ($person3->age, $person3, $person1));

		// Adds $person1 and $person2
		$hashMap->put ($person1->age, $person1);
		$hashMap->put ($person2->age, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->age)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->age)->get());

		// Tries to replace a non existing pair
		$this->assertFalse ($hashMap->replaceWithNewValue ($person1->age, $person2, $person3));

		$this->assertEquals ($person1, $hashMap->get ($person1->age)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->age)->get());

		// Replaces $person1 by $person2 and $person2 by $person3
		$this->assertTrue ($hashMap->replaceWithNewValue ($person1->age, $person1, $person2));
		$this->assertTrue ($hashMap->replaceWithNewValue ($person2->age, $person2, $person3));
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person2, $hashMap->get ($person1->age)->get());
		$this->assertEquals ($person3, $hashMap->get ($person2->age)->get());

		// Checks keys with float keys
		$floatConverter = 0.1;

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertFalse ($hashMap->replaceWithNewValue ($person1->age * $floatConverter, $person1, $person2));
		$this->assertFalse ($hashMap->replaceWithNewValue ($person2->age * $floatConverter, $person2, $person3));
		$this->assertFalse ($hashMap->replaceWithNewValue ($person3->age * $floatConverter, $person3, $person1));

		// Adds $person1 and $person2
		$hashMap->put ($person1->age * $floatConverter, $person1);
		$hashMap->put ($person2->age * $floatConverter, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->age * $floatConverter)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->age * $floatConverter)->get());

		// Tries to replace a non existing pair
		$this->assertFalse ($hashMap->replaceWithNewValue ($person1->age * $floatConverter, $person2, $person3));

		$this->assertEquals ($person1, $hashMap->get ($person1->age * $floatConverter)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->age * $floatConverter)->get());

		// Replaces $person1 by $person2 and $person2 by $person3
		$this->assertTrue ($hashMap->replaceWithNewValue ($person1->age * $floatConverter, $person1, $person2));
		$this->assertTrue ($hashMap->replaceWithNewValue ($person2->age * $floatConverter, $person2, $person3));
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person2, $hashMap->get ($person1->age * $floatConverter)->get());
		$this->assertEquals ($person3, $hashMap->get ($person2->age * $floatConverter)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replaceWithNewValue
	 */
	public function testReplaceWithNewValueWithStringKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_STRING_TYPE);
		$this->assertFalse ($hashMap->replaceWithNewValue ($person1->name, $person1, $person2));
		$this->assertFalse ($hashMap->replaceWithNewValue ($person2->name, $person2, $person3));
		$this->assertFalse ($hashMap->replaceWithNewValue ($person3->name, $person3, $person1));

		// Adds $person1 and $person2
		$hashMap->put ($person1->name, $person1);
		$hashMap->put ($person2->name, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->name)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->name)->get());

		// Tries to replace a non existing pair
		$this->assertFalse ($hashMap->replaceWithNewValue ($person1->name, $person2, $person3));

		$this->assertEquals ($person1, $hashMap->get ($person1->name)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->name)->get());

		// Replaces $person1 by $person2 and $person2 by $person3
		$this->assertTrue ($hashMap->replaceWithNewValue ($person1->name, $person1, $person2));
		$this->assertTrue ($hashMap->replaceWithNewValue ($person2->name, $person2, $person3));
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person2, $hashMap->get ($person1->name)->get());
		$this->assertEquals ($person3, $hashMap->get ($person2->name)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replaceWithNewValue
	 */
	public function testReplaceWithNewValueWithBooleanKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_BOOLEAN_TYPE);
		$this->assertFalse ($hashMap->replaceWithNewValue ($person1->isMale, $person1, $person2));
		$this->assertFalse ($hashMap->replaceWithNewValue ($person2->isMale, $person2, $person3));
		$this->assertFalse ($hashMap->replaceWithNewValue ($person3->isMale, $person3, $person1));

		// Adds $person1 and $person2
		$hashMap->put ($person1->isMale, $person1);
		$hashMap->put ($person2->isMale, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1->isMale)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->isMale)->get());

		// Tries to replace a non existing pair
		$this->assertFalse ($hashMap->replaceWithNewValue ($person1->isMale, $person2, $person3));

		$this->assertEquals ($person1, $hashMap->get ($person1->isMale)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2->isMale)->get());

		// Replaces $person1 by $person2 and $person2 by $person3
		$this->assertTrue ($hashMap->replaceWithNewValue ($person1->isMale, $person1, $person2));
		$this->assertTrue ($hashMap->replaceWithNewValue ($person2->isMale, $person2, $person3));
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person2, $hashMap->get ($person1->isMale)->get());
		$this->assertEquals ($person3, $hashMap->get ($person2->isMale)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::replaceWithNewValue
	 */
	public function testReplaceWithNewValueWithObjectKeys() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_OBJECT_TYPE);
		$this->assertFalse ($hashMap->replaceWithNewValue ($person1, $person1, $person2));
		$this->assertFalse ($hashMap->replaceWithNewValue ($person2, $person2, $person3));
		$this->assertFalse ($hashMap->replaceWithNewValue ($person3, $person3, $person1));

		// Adds $person1 and $person2
		$hashMap->put ($person1, $person1);
		$hashMap->put ($person2, $person2);
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person1, $hashMap->get ($person1)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2)->get());

		// Tries to replace a non existing pair
		$this->assertFalse ($hashMap->replaceWithNewValue ($person1, $person2, $person3));

		$this->assertEquals ($person1, $hashMap->get ($person1)->get());
		$this->assertEquals ($person2, $hashMap->get ($person2)->get());

		// Replaces $person1 by $person2 and $person2 by $person3
		$this->assertTrue ($hashMap->replaceWithNewValue ($person1, $person1, $person2));
		$this->assertTrue ($hashMap->replaceWithNewValue ($person2, $person2, $person3));
		$this->assertEquals (2, $hashMap->size());

		$this->assertEquals ($person2, $hashMap->get ($person1)->get());
		$this->assertEquals ($person3, $hashMap->get ($person2)->get());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::size
	 */
	public function testSizeOfHashMap() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEquals (0, $hashMap->size());
		$this->assertTrue ($hashMap->isEmpty());

		$hashMap->put ($person1->age, $person1);
		$this->assertEquals (1, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		$hashMap->put ($person2->age, $person2);
		$this->assertEquals (2, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		$hashMap->put ($person3->age, $person3);
		$this->assertEquals (3, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		// Does not permits duplicates
		$hashMap->put ($person3->age, $person3);
		$this->assertEquals (3, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		// Removes every element
		$hashMap->removeByKey ($person3->age);
		$this->assertEquals (2, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		$hashMap->removeByKey ($person2->age);
		$this->assertEquals (1, $hashMap->size());
		$this->assertFalse ($hashMap->isEmpty());

		$hashMap->removeByKey ($person1->age);
		$this->assertEquals (0, $hashMap->size());
		$this->assertTrue ($hashMap->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\iterable\map\HashMap::values
	 */
	public function testValuesOfHashMap() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashMap = new HashMap (Map::KEY_NUMERIC_TYPE);
		$this->assertEmpty ($hashMap->values());

		// Adds $person1
		$hashMap->put ($person1->age, $person1);

		$arrayOfValues = $hashMap->values();
		$this->assertCount (1, $arrayOfValues);
		$this->assertEquals ($person1, $arrayOfValues[0]);

		// Adds $person2
		$hashMap->put ($person2->age, $person2);

		$arrayOfValues = $hashMap->values();
		$this->assertCount (2, $arrayOfValues);
		$this->assertEmpty (array_diff ($arrayOfValues, array ($person1, $person2)));

		// Adds $person3
		$hashMap->put ($person3->age, $person3);

		$arrayOfValues = $hashMap->values();
		$this->assertCount (3, $arrayOfValues);
		$this->assertEmpty (array_diff ($arrayOfValues, array ($person1, $person2, $person3)));
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