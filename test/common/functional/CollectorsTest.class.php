<?php

namespace FunctionalPHP\test\common\functional;

require_once '../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\common\Object;
use FunctionalPHP\common\functional\Collectors;
use FunctionalPHP\common\util\ReflectionUtil;

use FunctionalPHP\iterable\map\Map;
use FunctionalPHP\iterable\map\HashMap;

use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\set\HashSet;
use FunctionalPHP\iterable\collection\set\SortedSet;

use FunctionalPHP\test\IsIntPredicate;
use FunctionalPHP\test\Person;


/**
 * Class used to test FunctionalPHP\common\functional\Collectors
 */
final class CollectorsTest extends TestCase {


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::toList
	 */
	public function testToListCollectorImplInformation() {

		$collectorImpl = Collectors::toList();

		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (ArrayList::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::toSet
	 */
	public function testToSetCollectorImplInformation() {

		$collectorImpl = Collectors::toSet();

		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashSet::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::partitioningBy
	 */
	public function testPartitioningByCollectorImplInformation() {

		$collectorImpl = Collectors::partitioningBy (new IsIntPredicate());

		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (2, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_BOOLEAN_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		$this->assertEquals (ArrayList::class, get_class ($collectorImpl->getSupplier()->get (TRUE)->get()->getIterable()));
		$this->assertEquals (ArrayList::class, get_class ($collectorImpl->getSupplier()->get (FALSE)->get()->getIterable()));

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::partitioningByUsingCollection
	 */
	public function testPartitioningByUsingCollectionCollectorImplInformation() {

		$collectionToUse = new SortedSet();
		$collectorImpl   = Collectors::partitioningByUsingCollection (new IsIntPredicate(), $collectionToUse);

		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (2, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_BOOLEAN_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		$this->assertEquals (get_class ($collectionToUse), get_class ($collectorImpl->getSupplier()->get (TRUE)->get()->getIterable()));
		$this->assertEquals (get_class ($collectionToUse), get_class ($collectorImpl->getSupplier()->get (FALSE)->get()->getIterable()));

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingBy
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGroupingByWithClosureWithMoreThanOneParameter() {

		Collectors::groupingBy (function (int $p1, string $p2) {
			                       $p1 += 1;
			                       $p2 .= "_test";
		                        });
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingBy
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGroupingByWithClosureWithEmptyParameterType() {

		Collectors::groupingBy (function ($p1) {
			                       $p1 += 1;
		                        });
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingBy
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGroupingByWithClosureWithParameterTypeDifferentOfObject() {

		Collectors::groupingBy (function (int $p1) {
			                       $p1 += 1;
		                        });
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingBy
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGroupingByWithClosureWithoutReturnedType() {

		Collectors::groupingBy (function (Person $person) {
			                       $person->name = "AA";
		                        });
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingBy
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGroupingByWithClosureWithInvalidNativeReturnedType() {

		Collectors::groupingBy (function (Person $person) : array {
			                       return array();
		                        });
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingBy
	 */
	public function testGroupingByWithClosureThatReturnAnInt_CollectorImplInformation() {

		$collectorImpl = Collectors::groupingBy (function (Person $person) : int {
			                                        return $person->age;
		                                         });
		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_NUMERIC_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingBy
	 */
	public function testGroupingByWithClosureThatReturnAFloat_CollectorImplInformation() {

		$collectorImpl = Collectors::groupingBy (function (Person $person) : float {
			                                        return $person->age * 0.1;
		                                         });
		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_NUMERIC_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingBy
	 */
	public function testGroupingByWithClosureThatReturnABool_CollectorImplInformation() {

		$collectorImpl = Collectors::groupingBy (function (Person $person) : bool {
			                                        return $person->isMale;
		                                         });
		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_BOOLEAN_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingBy
	 */
	public function testGroupingByWithClosureThatReturnAString_CollectorImplInformation() {

		$collectorImpl = Collectors::groupingBy (function (Person $person) : string {
			                                        return $person->name;
		                                         });
		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_STRING_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingBy
	 */
	public function testGroupingByWithClosureThatReturnAnObject_CollectorImplInformation() {

		$collectorImpl = Collectors::groupingBy (function (Person $person) : Person {
			                                        return $person;
		                                         });
		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_OBJECT_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingByUsingCollection
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGroupingByUsingCollectionWithClosureWithMoreThanOneParameter() {

		Collectors::groupingByUsingCollection (function (int $p1, string $p2) {
			                                      $p1 += 1;
			                                      $p2 .= "_test";
		                                       }
		                                      ,new HashSet());
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingByUsingCollection
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGroupingByUsingCollectionWithClosureWithEmptyParameterType() {

		Collectors::groupingByUsingCollection (function ($p1) {
			                                      $p1 += 1;
		                                       }
		                                      ,new HashSet());
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingByUsingCollection
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGroupingByUsingCollectionWithClosureWithParameterTypeDifferentOfObject() {

		Collectors::groupingByUsingCollection (function (int $p1) {
			                                      $p1 += 1;
		                                       }
		                                      ,new HashSet());
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingByUsingCollection
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGroupingByUsingCollectionWithClosureWithoutReturnedType() {

		Collectors::groupingByUsingCollection (function (Person $person) {
			                                      $person->name = "AA";
		                                       }
		                                      ,new HashSet());
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingByUsingCollection
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGroupingByUsingCollectionWithClosureWithInvalidNativeReturnedType() {

		Collectors::groupingByUsingCollection (function (Person $person) : array {
			                                      return array();
		                                       }
		                                      ,new HashSet());
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingByUsingCollection
	 */
	public function testGroupingByUsingCollectionWithClosureThatReturnAnInt_CollectorImplInformation() {

		$collectorImpl = Collectors::groupingByUsingCollection (function (Person $person) : int {
			                                                       return $person->age;
		                                                        }
		                                                       ,new SortedSet());
		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_NUMERIC_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingByUsingCollection
	 */
	public function testGroupingByUsingCollectionWithClosureThatReturnAFloat_CollectorImplInformation() {

		$collectorImpl = Collectors::groupingByUsingCollection (function (Person $person) : float {
			                                                       return $person->age * 0.1;
		                                                        }
		                                                       ,new SortedSet());
		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_NUMERIC_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingByUsingCollection
	 */
	public function testGroupingByUsingCollectionWithClosureThatReturnABool_CollectorImplInformation() {

		$collectorImpl = Collectors::groupingByUsingCollection (function (Person $person) : bool {
			                                                       return $person->isMale;
		                                                        }
		                                                       ,new SortedSet());
		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_BOOLEAN_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingByUsingCollection
	 */
	public function testGroupingByUsingCollectionWithClosureThatReturnAString_CollectorImplInformation() {

		$collectorImpl = Collectors::groupingByUsingCollection (function (Person $person) : string {
			                                                       return $person->name;
		                                                        }
		                                                       ,new SortedSet());
		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_STRING_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\functional\Collectors::groupingByUsingCollection
	 */
	public function testGroupingByUsingCollectionWithClosureThatReturnAnObject_CollectorImplInformation() {

		$collectorImpl = Collectors::groupingByUsingCollection (function (Person $person) : Person {
			                                                       return $person;
		                                                        }
		                                                       ,new SortedSet());
		$this->assertNotNull ($collectorImpl);

		// Checks supplier property
		$this->assertNotNull ($collectorImpl->getSupplier());
		$this->assertEquals (HashMap::class, get_class ($collectorImpl->getSupplier()));
		$this->assertEquals (0, $collectorImpl->getSupplier()->size());
		$this->assertEquals (Map::KEY_OBJECT_TYPE, $collectorImpl->getSupplier()->getTypeOfKeys());

		// Checks accumulator property
		$this->assertNotNull ($collectorImpl->getAccumulator());
		$this->assertEquals (\Closure::class, get_class ($collectorImpl->getAccumulator()));

		// Gets information of closure stored in accumulator property
		$reflectionInformation = ReflectionUtil::getReflectionInformationOfClosure ($collectorImpl->getAccumulator());
		$this->assertNotNull ($reflectionInformation);

		$this->assertEquals (1, $reflectionInformation->numberOfParameters);
		$this->assertEquals (1, count ($reflectionInformation->typesOfParameters));
		$this->assertEquals (Object::class, $reflectionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionInformation->typeOfReturnedValue);
	}

}

?>