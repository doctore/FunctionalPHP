<?php

namespace FunctionalPHP\test\common\functional;

require_once '../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\iterable\map\Map;
use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\queue\PriorityQueue;
use FunctionalPHP\iterable\collection\set\HashSet;

use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;

use FunctionalPHP\common\functional\Collectors;
use FunctionalPHP\common\functional\Stream;
use FunctionalPHP\common\functional\BasicStream;

use FunctionalPHP\test\Car;
use FunctionalPHP\test\Person;
use FunctionalPHP\test\PersonComparator;
use FunctionalPHP\test\HasPersonMoreThanOneWordAsNamePredicate;
use FunctionalPHP\test\HasPersonNameWithValuePredicate;
use FunctionalPHP\test\HasPersonNoAgeValuePredicate;
use FunctionalPHP\test\HasPersonOddAgePredicate;


/**
 * Class used to test FunctionalPHP\common\functional\BasicStream
 */
final class BasicStreamTest extends TestCase {


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::__construct
	 */
	public function testCreateEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::__construct
	 */
	public function testCreateNotEmptyBasicStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::concat
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testConcatWithStreamsWithDifferentTypesOfStoredElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$firstStream = new BasicStream ($arrayListOfPersons);

		$secondStream = $arrayListOfPersons->stream()->map (function (Person $person) : int {
			                                                   return $person->age;
		                                                    });
		BasicStream::concat ($firstStream, $secondStream);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::concat
	 */
	public function testConcatWithStreamsWithDifferentTypesOfObjectsOfStoredElements() {

		$arrayListOfPersons = new ArrayList();
		$arrayListOfPersons->add (new Person ("Alba", 11, FALSE));
		$arrayListOfPersons->add (new Person ("Albert", 18, TRUE));

		$arrayListOfCars = new ArrayList();
		$arrayListOfCars->add (new Car ('A-2134', 2015));
		$arrayListOfCars->add (new Car ('B-9999', 2015));

		$firstStream  = $arrayListOfPersons->stream();
		$secondStream = $arrayListOfCars->stream();

		// Concats both streams
		$concatenatedStream = BasicStream::concat ($firstStream, $secondStream);

		$this->assertNotNull ($concatenatedStream);
		$this->assertEquals ($arrayListOfPersons->size() + $arrayListOfCars->size(), $concatenatedStream->count());
		$this->assertEquals (Object::class, $concatenatedStream->getCurrentTypeStoredByStream());

		// Checks contained elements
		$numberOfPersonsFound = 0;
		$numberOfCarsFound    = 0;
		$contentOfStream      = $concatenatedStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++) {

			$currentElement = $contentOfStream[$i];
			if (strcmp (get_class ($currentElement), Person::class) == 0) {

				$this->assertTrue ($arrayListOfPersons->contains ($currentElement));
				$numberOfPersonsFound++;
			}
			else if (strcmp (get_class ($currentElement), Car::class) == 0) {

				$this->assertTrue ($arrayListOfCars->contains ($currentElement));
				$numberOfCarsFound++;
			}
			// This condition never can be true
			else
				$this->assertTrue (FALSE);
		}
		$this->assertEquals ($arrayListOfPersons->size(), $numberOfPersonsFound);
		$this->assertEquals ($arrayListOfCars->size(), $numberOfCarsFound);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::concat
	 */
	public function testConcatWithStreamsWithIntAsTypeOfStoredElements() {

		$arrayListOfPersons = new ArrayList();
		$arrayListOfPersons->add (new Person ("Alba", 11, FALSE));
		$arrayListOfPersons->add (new Person ("Albert", 18, TRUE));

		$arrayListOfCars = new ArrayList();
		$arrayListOfCars->add (new Car ('A-2134', 2015));
		$arrayListOfCars->add (new Car ('B-9999', 2015));

		$firstStream  = $arrayListOfPersons->stream()->map (function (Person $person) : int {
			                                                   return $person->age;
		                                                    });

		$secondStream = $arrayListOfCars->stream()->map (function (Car $car) : int {
			                                                return $car->yearOfProduction;
		                                                 });
		// Concats both streams
		$concatenatedStream = BasicStream::concat ($firstStream, $secondStream);

		$this->assertNotNull ($concatenatedStream);
		$this->assertEquals ($arrayListOfPersons->size() + $arrayListOfCars->size(), $concatenatedStream->count());
		$this->assertEquals ("int", $concatenatedStream->getCurrentTypeStoredByStream());

		// Checks contained elements
		$concatenatedArrayOfAgesAndYearsOfProduction = array();
		foreach ($arrayListOfPersons->iterator() as $person)
			$concatenatedArrayOfAgesAndYearsOfProduction[] = $person->age;

		foreach ($arrayListOfCars->iterator() as $car)
			$concatenatedArrayOfAgesAndYearsOfProduction[] = $car->yearOfProduction;


		$contentOfStream = $concatenatedStream->toArray();

		$this->assertEquals (count ($concatenatedArrayOfAgesAndYearsOfProduction), count ($contentOfStream));
		$this->assertEmpty (array_diff ($concatenatedArrayOfAgesAndYearsOfProduction, $contentOfStream));
		$this->assertEmpty (array_diff ($contentOfStream, $concatenatedArrayOfAgesAndYearsOfProduction));
	}



	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::allMatch
	 */
	public function testAllMatchOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertTrue ($basicStream->allMatch (new HasPersonMoreThanOneWordAsNamePredicate()));
		$this->assertTrue ($basicStream->allMatch (new HasPersonNameWithValuePredicate()));
		$this->assertTrue ($basicStream->allMatch (new HasPersonNoAgeValuePredicate()));
		$this->assertTrue ($basicStream->allMatch (new HasPersonOddAgePredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::allMatch
	 */
	public function testAllMatchOfNotEmptyBasicStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertFalse ($basicStream->allMatch (new HasPersonMoreThanOneWordAsNamePredicate()));
		$this->assertTrue ($basicStream->allMatch (new HasPersonNameWithValuePredicate()));
		$this->assertFalse ($basicStream->allMatch (new HasPersonNoAgeValuePredicate()));
		$this->assertFalse ($basicStream->allMatch (new HasPersonOddAgePredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::anyMatch
	 */
	public function testAnyMatchOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertFalse ($basicStream->anyMatch (new HasPersonMoreThanOneWordAsNamePredicate()));
		$this->assertFalse ($basicStream->anyMatch (new HasPersonNameWithValuePredicate()));
		$this->assertFalse ($basicStream->anyMatch (new HasPersonNoAgeValuePredicate()));
		$this->assertFalse ($basicStream->anyMatch (new HasPersonOddAgePredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::anyMatch
	 */
	public function testAnyMatchOfNotEmptyBasicStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertTrue ($basicStream->anyMatch (new HasPersonMoreThanOneWordAsNamePredicate()));
		$this->assertTrue ($basicStream->anyMatch (new HasPersonNameWithValuePredicate()));
		$this->assertFalse ($basicStream->anyMatch (new HasPersonNoAgeValuePredicate()));
		$this->assertTrue ($basicStream->anyMatch (new HasPersonOddAgePredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::collect
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testCollectWithAStreamThatDoesNotStoreObjects() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->map (function (Person $person) : float {
			                  return $person->age * 0.1;
		                   })
		            ->collect (Collectors::toList());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::collect
	 */
	public function testCollectToList() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$arrayListOfPersonsAfterCollect = $basicStream->collect (Collectors::toList());

		$this->assertEquals ($arrayListOfPersons, $arrayListOfPersonsAfterCollect);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::collect
	 */
	public function testCollectToSet() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$hashSetOfPersons = new HashSet ($arrayListOfPersons);
		$hashSetOfPersonsAfterCollect = $basicStream->collect (Collectors::toSet());

		$this->assertEquals ($hashSetOfPersons, $hashSetOfPersonsAfterCollect);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::collect
	 */
	public function testCollectPartitioningBy() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$hashMap = $basicStream->collect (Collectors::partitioningBy (new HasPersonOddAgePredicate()));

		$this->assertNotNull ($hashMap);
		$this->assertNotNull ($hashMap->get (FALSE));
		$this->assertNotNull ($hashMap->get (TRUE));

		$this->assertTrue ($hashMap->get (FALSE)->isPresent());
		$this->assertTrue ($hashMap->get (TRUE)->isPresent());

		$this->assertGreaterThan (0, $hashMap->get (FALSE)->get()->getIterable()->size());
		$this->assertGreaterThan (0, $hashMap->get (TRUE)->get()->getIterable()->size());

		$arrayListOfPersonsAfterCollect = new ArrayList();

		// Checks the predicate in the content of the HashMap
		foreach ($hashMap->get (FALSE)->get()->getIterable()->iterator() as $person) {

			$this->assertFalse ((new HasPersonOddAgePredicate())->test ($person));
			$arrayListOfPersonsAfterCollect->add ($person);
		}

		foreach ($hashMap->get (TRUE)->get()->getIterable()->iterator() as $person) {

			$this->assertTrue ((new HasPersonOddAgePredicate())->test ($person));
			$arrayListOfPersonsAfterCollect->add ($person);
		}
		$this->assertTrue ($arrayListOfPersons->containsAll ($arrayListOfPersonsAfterCollect));
		$this->assertTrue ($arrayListOfPersonsAfterCollect->containsAll ($arrayListOfPersons));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::collect
	 */
	public function testCollectpartitioningByUsingCollection() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$hashSetOfPersons = new HashSet ($arrayListOfPersons);

		$hashMap = $basicStream->collect (Collectors::partitioningByUsingCollection (new HasPersonOddAgePredicate()
				                                                                    ,new HashSet()));
		$this->assertNotNull ($hashMap);
		$this->assertNotNull ($hashMap->get (FALSE));
		$this->assertNotNull ($hashMap->get (TRUE));

		$this->assertTrue ($hashMap->get (FALSE)->isPresent());
		$this->assertTrue ($hashMap->get (TRUE)->isPresent());

		$this->assertGreaterThan (0, $hashMap->get (FALSE)->get()->getIterable()->size());
		$this->assertGreaterThan (0, $hashMap->get (TRUE)->get()->getIterable()->size());

		$hashSetOfPersonsAfterCollect = new HashSet();

		// Checks the predicate in the content of the HashMap
		foreach ($hashMap->get (FALSE)->get()->getIterable()->iterator() as $person) {

			$this->assertFalse ((new HasPersonOddAgePredicate())->test ($person));
			$hashSetOfPersonsAfterCollect->add ($person);
		}

		foreach ($hashMap->get (TRUE)->get()->getIterable()->iterator() as $person) {

			$this->assertTrue ((new HasPersonOddAgePredicate())->test ($person));
			$hashSetOfPersonsAfterCollect->add ($person);
		}
		$this->assertTrue ($hashSetOfPersons->containsAll ($hashSetOfPersonsAfterCollect));
		$this->assertTrue ($hashSetOfPersonsAfterCollect->containsAll ($hashSetOfPersons));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::collect
	 */
	public function testCollectGroupingBy() {

		$arrayListOfPersons = $this->generatePersonsArrayList();

		$arrayListOfCars = new ArrayList();
		foreach ($arrayListOfPersons->iterator() as $person) {

			foreach ($person->cars->iterator() as $car)
				$arrayListOfCars->add ($car);
		}
        $this->assertGreaterThan (0, $arrayListOfCars->size());

        $arrayOfDifferentYearOfProduction = $arrayListOfPersons->stream()->flatMap (function (Person $person) : Stream {
			                                                                           return $person->cars->stream()
			                                                                                               ->map (function (Car $car) : int {
			                                                                                                         return $car->yearOfProduction;
			                                                                                                      });
		                                                                            })
		                                                                 ->distinct()
		                                                                 ->toArray();
        $this->assertNotEmpty ($arrayOfDifferentYearOfProduction);

		// Grouping by the year of production of the cars belonging to the initial list of persons
		$hashMap = $arrayListOfPersons->stream()->flatMap (function (Person $person) : Stream {
		                                                      return $person->cars->stream();
		                                                   })
		                                        ->collect (Collectors::groupingBy (function (Car $car) : int {
		                                                	                          return $car->yearOfProduction;
		                                                                           }));
        $this->assertNotNull ($hashMap);
        $this->assertEquals (Map::KEY_NUMERIC_TYPE, $hashMap->getTypeOfKeys());
        $this->assertEquals (count ($arrayOfDifferentYearOfProduction), count ($hashMap->keys()));

        // Checks that the given map contains all different values of yearOfProduction
        for ($i = 0; $i < count ($arrayOfDifferentYearOfProduction); $i++) {

        	$this->assertNotNull ($hashMap->get ($arrayOfDifferentYearOfProduction[$i]));
        	$this->assertTrue ($hashMap->get ($arrayOfDifferentYearOfProduction[$i])->isPresent());
        }
        // Checks the values of every key in the map
        $arrayListOfCarsAfterCollect = new ArrayList();

        for ($i = 0; $i < count ($arrayOfDifferentYearOfProduction); $i++) {

	        foreach ($hashMap->get ($arrayOfDifferentYearOfProduction[$i])->get()->getIterable()->iterator() as $car) {

	        	$this->assertEquals ($arrayOfDifferentYearOfProduction[$i], $car->yearOfProduction);
	        	$arrayListOfCarsAfterCollect->add ($car);
	        }
        }
        $this->assertTrue ($arrayListOfCars->containsAll ($arrayListOfCarsAfterCollect));
        $this->assertTrue ($arrayListOfCarsAfterCollect->containsAll ($arrayListOfCars));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::collect
	 */
	public function testCollectGroupingByUsingCollection() {

		$arrayListOfPersons = $this->generatePersonsArrayList();

		$hashSetOfCars = new HashSet();
		foreach ($arrayListOfPersons->iterator() as $person) {

			foreach ($person->cars->iterator() as $car)
				$hashSetOfCars->add ($car);
		}
		$this->assertGreaterThan (0, $hashSetOfCars->size());

		$arrayOfDifferentYearOfProduction = $arrayListOfPersons->stream()->flatMap (function (Person $person) : Stream {
			                                                                           return $person->cars->stream()
			                                                                                               ->map (function (Car $car) : int {
				                                                                                                     return $car->yearOfProduction;
			                                                                                                      });
		                                                                            })
		                                                                 ->distinct()
		                                                                 ->toArray();
		$this->assertNotEmpty ($arrayOfDifferentYearOfProduction);

		// Grouping by the year of production of the cars belonging to the initial list of persons
		$hashMap = $arrayListOfPersons->stream()->flatMap (function (Person $person) : Stream {
			                                                  return $person->cars->stream();
		                                                   })
		                                        ->collect (Collectors::groupingByUsingCollection (function (Car $car) : int {
			                                                                                         return $car->yearOfProduction;
		                                                                                          }
		                                                                                         ,new HashSet()));
		$this->assertNotNull ($hashMap);
		$this->assertEquals (Map::KEY_NUMERIC_TYPE, $hashMap->getTypeOfKeys());
		$this->assertEquals (count ($arrayOfDifferentYearOfProduction), count ($hashMap->keys()));

		// Checks that the given map contains all different values of yearOfProduction
		for ($i = 0; $i < count ($arrayOfDifferentYearOfProduction); $i++) {

			$this->assertNotNull ($hashMap->get ($arrayOfDifferentYearOfProduction[$i]));
			$this->assertTrue ($hashMap->get ($arrayOfDifferentYearOfProduction[$i])->isPresent());
		}
		// Checks the values of every key in the map
		$hashSetOfCarsAfterCollect = new HashSet();

		for ($i = 0; $i < count ($arrayOfDifferentYearOfProduction); $i++) {

			foreach ($hashMap->get ($arrayOfDifferentYearOfProduction[$i])->get()->getIterable()->iterator() as $car) {

				$this->assertEquals ($arrayOfDifferentYearOfProduction[$i], $car->yearOfProduction);
				$hashSetOfCarsAfterCollect->add ($car);
			}
		}
		$this->assertTrue ($hashSetOfCars->containsAll ($hashSetOfCarsAfterCollect));
		$this->assertTrue ($hashSetOfCarsAfterCollect->containsAll ($hashSetOfCars));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::count
	 */
	public function testCount() {

		// Empty Stream
		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertEquals (0, $basicStream->count());

		// Not empty Stream
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::distinct
	 */
	public function testDistinctOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->distinct();

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::distinct
	 */
	public function testDistinctStoringObjectsInsideBasicStream() {

		$arrayListOfPersonsWithDuplicates = $this->generatePersonsArrayList();
		$arrayListOfPersonsWithoutDuplicates = $this->generatePersonsArrayList (FALSE);

		$basicStream = new BasicStream ($arrayListOfPersonsWithDuplicates);
		$basicStream->distinct();

		$this->assertGreaterThan (0, $basicStream->count());
		$this->assertNotEquals ($arrayListOfPersonsWithDuplicates->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersonsWithoutDuplicates->size(), $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		$this->assertEquals ($arrayListOfPersonsWithoutDuplicates->size(), count ($contentOfStream));

		for ($i = 0; $i < $arrayListOfPersonsWithoutDuplicates->size(); $i++)
			$this->assertEquals ($arrayListOfPersonsWithoutDuplicates->get($i), $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::distinct
	 */
	public function testDistinctStoringStringsInsideBasicStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		// Converts into a Stream of strings
		$basicStream->map (function (Person $person) : string {
			                  return "sameString";
		                   });

		$this->assertGreaterThan (1, $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());

		$basicStream->distinct();

		$this->assertEquals (1, $basicStream->count());

		$contentOfStream = $basicStream->toArray();

		$this->assertEquals (1, count ($contentOfStream));
		$this->assertEquals ("sameString", $contentOfStream[0]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::distinct
	 */
	public function testDistinctStoringIntInsideBasicStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		// Converts into a Stream of strings
		$basicStream->map (function (Person $person) : int {
			                  return 24;
		                   });

		$this->assertGreaterThan (1, $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());

		$basicStream->distinct();

		$this->assertEquals (1, $basicStream->count());

		$contentOfStream = $basicStream->toArray();

		$this->assertEquals (1, count ($contentOfStream));
		$this->assertEquals (24, $contentOfStream[0]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::distinct
	 */
	public function testDistinctStoringBoolInsideBasicStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		// Converts into a Stream of strings
		$basicStream->map (function (Person $person) : bool {
			                  return TRUE;
		                   });

		$this->assertGreaterThan (1, $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());

		$basicStream->distinct();

		$this->assertEquals (1, $basicStream->count());

		$contentOfStream = $basicStream->toArray();

		$this->assertEquals (1, count ($contentOfStream));
		$this->assertEquals (TRUE, $contentOfStream[0]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filter
	 */
	public function testFilterOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filter (new HasPersonOddAgePredicate());
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->filter (new HasPersonMoreThanOneWordAsNamePredicate());
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->filter (new HasPersonNoAgeValuePredicate());
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->filter (new HasPersonNameWithValuePredicate());
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filter
	 */
	public function testFilterOnePredicate() {

		// HasPersonNameWithValuePredicate
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filter (new HasPersonNameWithValuePredicate());
		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		$this->assertEquals ($arrayListOfPersons->size(), count ($contentOfStream));

		for ($i = 0; $i < $arrayListOfPersons->size(); $i++)
			$this->assertEquals ($arrayListOfPersons->get($i), $contentOfStream[$i]);

		// HasPersonNoAgeValuePredicate
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filter (new HasPersonNoAgeValuePredicate());
		$this->assertEquals (0, $basicStream->count());

		// HasPersonOddAgePredicate
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filter (new HasPersonOddAgePredicate());
		$this->assertGreaterThan (0, $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertTrue ($contentOfStream[$i]->age % 2 != 0);

		// HasPersonMoreThanOneWordAsNamePredicate
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filter (new HasPersonMoreThanOneWordAsNamePredicate());
		$this->assertGreaterThan (0, $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertTrue (str_word_count ($contentOfStream[$i]->name) > 1);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filter
	 */
	public function testFilterMoreThanOnePredicate() {

		// HasPersonOddAgePredicate && HasPersonMoreThanOneWordAsNamePredicate
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filter (new HasPersonOddAgePredicate())
		            ->filter (new HasPersonMoreThanOneWordAsNamePredicate());

        $this->assertGreaterThan (0, $basicStream->count());

        $contentOfStream = $basicStream->toArray();
        for ($i = 0; $i < count ($contentOfStream); $i++) {

        	$this->assertTrue ($contentOfStream[$i]->age % 2 != 0);
        	$this->assertTrue (str_word_count ($contentOfStream[$i]->name) > 1);
        }
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaWithClosureWithMoreThanOneParameter() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filterByLambda (function (int $p1, string $p2) {
			                             $p1 += 1;
			                             $p2 .= "_test";
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaWithClosureWithParameterTypeDifferentOfStreamElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filterByLambda (function (string $p1) {
			                             $p1 .= "_test";
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaWithClosureWithInvalidReturnedType() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filterByLambda (function (Person $person) : Person {
			                             $person->age *= 2;
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filterByLambda (function (Person $person) {
			                             $person->age *= 2;
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 */
	public function testFilterByLambdaByOneIteration() {

		// Filter pair age values
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filterByLambda (function (Person $person) : bool {
			                             return $person->age % 2 == 0;
		                              });

		$this->assertGreaterThan (0, $basicStream->count());
		$this->assertFalse ($arrayListOfPersons->size() == $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals (0, $contentOfStream[$i]->age % 2);

		// Filter name values with lenght = 3
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filterByLambda (function (Person $person) : bool {
			                             return strlen ($person->name) == 3;
		                              });

		$this->assertGreaterThan (0, $basicStream->count());
		$this->assertFalse ($arrayListOfPersons->size() == $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals (3, strlen ($contentOfStream[$i]->name));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 */
	public function testFilterByLambdaByMoreThanOneIteration() {

		// Filter pair age values
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->filterByLambda (function (Person $person) : bool {
			                             return $person->age % 2 == 0;
		                              })
		            ->filterByLambda (function (Person $person) : bool {
		                              	 return str_word_count ($person->name) == 3;
		                              });

		$this->assertGreaterThan (0, $basicStream->count());
		$this->assertFalse ($arrayListOfPersons->size() == $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++) {

			$this->assertEquals (0, $contentOfStream[$i]->age % 2);
			$this->assertEquals (3, str_word_count ($contentOfStream[$i]->name));
		}
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::findFirst
	 */
	public function testFindFirstOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$optional = $basicStream->findFirst();
		$this->assertFalse ($optional->isPresent());

		// HasPersonNoAgeValuePredicate
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$optional = $basicStream->filter (new HasPersonNoAgeValuePredicate())
		                        ->findFirst();

		$this->assertFalse ($optional->isPresent());
		$this->assertEquals (0, $basicStream->count());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::findFirst
	 */
	public function testFindFirstOfNotEmptyBasicStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$optional = $basicStream->findFirst();
		$this->assertTrue ($optional->isPresent());

		// HasPersonOddAgePredicate
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$optional = $basicStream->filter (new HasPersonOddAgePredicate())
		                        ->findFirst();

		$this->assertTrue ($optional->isPresent());
		$this->assertTrue ($optional->get()->age % 2 != 0);

		// HasPersonMoreThanOneWordAsNamePredicate
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$optional = $basicStream->filter (new HasPersonMoreThanOneWordAsNamePredicate())
		                        ->findFirst();

		$this->assertTrue ($optional->isPresent());
		$this->assertTrue (str_word_count ($optional->get()->name) > 1);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::flatMap
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFlatMapWithClosureWithMoreThanOneParameter() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->flatMap (function (int $p1, string $p2) {
			                      $p1 += 1;
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::flatMap
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFlatMapWithClosureWithParameterTypeDifferentOfStreamElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->flatMap (function (int $p1) {
			                      $p1 += 1;
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::flatMap
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFlatMapWithClosureWithInvalidReturnedType() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->flatMap (function (Person $person) : int {
			                      $person->age *= 2;
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::flatMap
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFlatMapOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->flatMap (function (Person $person) : Stream {
			                      return $person->cars->stream();
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::flatMap
	 */
	public function testFlatMapFlatteningACollectionOfObjects() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->flatMap (function (Person $person) : Stream {
			                      return $person->cars->stream();
		                       });

		$this->assertEquals (Car::class, $basicStream->getCurrentTypeStoredByStream());

		// Gets the list of all cars of the "original ArrayList"
		$arrayListOfCars = new ArrayList();
		foreach ($arrayListOfPersons->iterator() as $person)
			$arrayListOfCars->addAll ($person->cars);

		$this->assertEquals ($basicStream->count(), $arrayListOfCars->size());

		// Gets the list of all cars of the current Stream
		$arrayListOfPersonsFromStream = new ArrayList();
		foreach ($basicStream->toArray() as $car)
			$arrayListOfPersonsFromStream->add ($car);

		$this->assertEquals ($arrayListOfCars->size(), $arrayListOfPersonsFromStream->size());

		// Checks if both ArrayList contains the same elements
		$arrayListOfCars->removeAll ($arrayListOfPersonsFromStream);

		$this->assertEquals (0, $arrayListOfCars->size());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::flatMap
	 */
	public function testFlatMapFlatteningAStringCollection() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->flatMap (function (Person $person) : Stream {
                                  return $person->cars->stream()
                                                      ->map (function (Car $car) : string {
                                                                return $car->registration;
                                                             });
		                       });

		$this->assertEquals ("string", $basicStream->getCurrentTypeStoredByStream());

		// Gets the list of all car's registration of the "original ArrayList"
		$arrayOfCarRegistrations = array();
		foreach ($arrayListOfPersons->iterator() as $person) {

			foreach ($person->cars->iterator() as $car)
				$arrayOfCarRegistrations[] = $car->registration;
		}
		$this->assertEquals ($basicStream->count(), count ($arrayOfCarRegistrations));

		// Gets the list of all car's registration of the current Stream
		$arrayOfCarRegistrationsFromStream = $basicStream->toArray();

		$this->assertEquals (count ($arrayOfCarRegistrations), count ($arrayOfCarRegistrationsFromStream));

		// Checks if both arrays contains the same elements
		$arrayWithDifferences = array_diff ($arrayOfCarRegistrations, $arrayOfCarRegistrationsFromStream);

		$this->assertEquals (0, count ($arrayWithDifferences));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::flatMap
	 */
	public function testFlatMapFlatteningAnIntCollection() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->flatMap (function (Person $person) : Stream {
			                      return $person->cars->stream()
			                                          ->map (function (Car $car) : int {
				                                                return $car->yearOfProduction;
			                                                 });
		                       });

		$this->assertEquals ("int", $basicStream->getCurrentTypeStoredByStream());

		// Gets the list of all car's year of production of the "original ArrayList"
		$arrayOfCarYearOfProduction = array();
		foreach ($arrayListOfPersons->iterator() as $person) {

			foreach ($person->cars->iterator() as $car)
				$arrayOfCarYearOfProduction[] = $car->yearOfProduction;
		}
		$this->assertEquals ($basicStream->count(), count ($arrayOfCarYearOfProduction));

		// Gets the list of all car's year of production of the current Stream
		$arrayOfCarYearOfProductionFromStream = $basicStream->toArray();

		$this->assertEquals (count ($arrayOfCarYearOfProduction), count ($arrayOfCarYearOfProductionFromStream));

		// Checks if both arrays contains the same elements
		$arrayWithDifferences = array_diff ($arrayOfCarYearOfProduction, $arrayOfCarYearOfProductionFromStream);

		$this->assertEquals (0, count ($arrayWithDifferences));
	}



	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithMoreThanOneParameter() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->forEach (function (int $p1, string $p2) {
			                      $p1 += 1;
			                      $p2 .= "_test";
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithParameterTypeDifferentOfStreamElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->forEach (function (int $p1) {
		 	                      $p1 += 1;
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithInvalidReturnedType() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->forEach (function (Person $person) : int {
			                      $person->age *= 2;
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::forEach
	 */
	public function testForEachOfNotEmptyBasicStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->forEach (function (Person $person) {
			                      $person->age *= 2;
		                       });

		// It is necessary "reinitialize" it due to the stream works with the same objects that "initial arrayList"
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$contentOfStream = $basicStream->toArray();

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($contentOfStream));

		for ($i = 0; $i < $arrayListOfPersons->size(); $i++)
			$this->assertEquals ($arrayListOfPersons->get($i)->age * 2, $contentOfStream[$i]->age);

		// Using the filter HasPersonMoreThanOneWordAsNamePredicate
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$originalStream = new BasicStream ($arrayListOfPersons);

		$originalStream->filter (new HasPersonMoreThanOneWordAsNamePredicate());

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$finalStream = new BasicStream ($arrayListOfPersons);

		// Only applies forEach to the finalStream
		$finalStream->filter (new HasPersonMoreThanOneWordAsNamePredicate())
		            ->forEach (function (Person $person) {
			                      $person->name .= "_2";
		                       });

        $contentOfOriginalStream = $originalStream->toArray();
        $contentOfFinalStream    = $finalStream->toArray();

        $this->assertGreaterThan (0, count ($contentOfOriginalStream));
        $this->assertEquals (count ($contentOfOriginalStream), count ($contentOfFinalStream));

        for ($i = 0; $i < count ($contentOfOriginalStream); $i++)
        	$this->assertEquals ($contentOfOriginalStream[$i]->name . "_2"
        			            ,$contentOfFinalStream[$i]->name);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::limit
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testLimitWithMaxSizeLessThanZero() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->limit (-1);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::limit
	 */
	public function testLimitOfEmptyBasicStream() {

		// Limit = 0
		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->limit (0);
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		// Limit = 10
		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->limit (10);
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::limit
	 */
	public function testLimitOfNotEmptyBasicStream() {

		// Limit = 5
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->limit (5);

		$this->assertGreaterThan ($basicStream->count(), $arrayListOfPersons->size());
		$this->assertEquals (5, $basicStream->count());
		$this->assertEquals (5, count ($basicStream->toArray()));

		// Limit = 10
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->limit (10);

		$this->assertGreaterThan ($basicStream->count(), $arrayListOfPersons->size());
		$this->assertEquals (10, $basicStream->count());
		$this->assertEquals (10, count ($basicStream->toArray()));

		// Limit = $arrayListOfPersons->size() + 1
		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->limit ($arrayListOfPersons->size() + 1);

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::min
	 */
	public function testMinOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertFalse ($basicStream->min (new PersonComparator())->isPresent());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::min
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMinOfBasicStreamWithStringElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : string {
			                  return $person->name;
		                   });

		$basicStream->min (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::min
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMinOfBasicStreamWithIntElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : int {
			                  return $person->age;
		                   });

		$basicStream->min (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::min
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMinOfBasicStreamWithFloatElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : float {
			                  return $person->age * 0.1;
		                   });

		$basicStream->min (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::min
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMinOfBasicStreamWithBoolElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : bool {
			                  return TRUE;
		                   });

		$basicStream->min (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::min
	 */
	public function testMinOfBasicStreamWithObjects() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$priorityQueue = new PriorityQueue ($arrayListOfPersons, new PersonComparator());

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		// Get the min and compare with the first element in the PriorityQueue
		$minInStream = $basicStream->min (new PersonComparator());
		$this->assertNotNull ($minInStream);
		$this->assertTrue ($minInStream->isPresent());

		// Get the first element of the priority queue
		$minInPriorityQueue = $priorityQueue->peek();
		$this->assertNotNull ($minInStream);
		$this->assertTrue ($minInStream->isPresent());

		$this->assertEquals ($minInPriorityQueue->get(), $minInStream->get());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMapWithClosureWithMoreThanOneParameter() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->map (function (int $p1, string $p2) {
			                  $p1 += 1;
			                  $p2 .= "_test";
		                   });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMapWithClosureWithParameterTypeDifferentOfStreamElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->map (function (bool $p1) {
			                  $p1 = TRUE;
		                   });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMapWithClosureWithInvalidNativeReturnedType() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->map (function (Person $person) : array {
			                  return array ($person->age);
		                   });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 */
	public function testMapConvertOriginalObjectIntoNativeType() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		// Converts into a Stream of strings
		$basicStream->map (function (Person $person) : string {
			                  return "sameString";
		                   });

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ("string", $basicStream->getCurrentTypeStoredByStream());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ("sameString", $contentOfStream[$i]);

		// Initialize Stream and converts it into a Stream of float
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->map (function (Person $person) : float {
				              return $person->age * 0.1;
			               });

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ("float", $basicStream->getCurrentTypeStoredByStream());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($arrayListOfPersons->get($i)->age * 0.1, $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 */
	public function testMapAppliesMoreThanOnce() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		// Converts into a Stream of strings
		$basicStream->map (function (Person $person) : string {
			                  return $person->name;
		                   });

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ("string", $basicStream->getCurrentTypeStoredByStream());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($arrayListOfPersons->get($i)->name, $contentOfStream[$i]);

		// Convert current Stream of string into a Stream of int
		$basicStream->map (function (string $name) : int {
			                  return strlen ($name);
		                   });

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ("int", $basicStream->getCurrentTypeStoredByStream());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals (strlen ($arrayListOfPersons->get($i)->name), $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::max
	 */
	public function testMaxOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertFalse ($basicStream->max (new PersonComparator())->isPresent());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::max
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMaxOfBasicStreamWithStringElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : string {
			                  return $person->name;
		                   });

		$basicStream->max (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::max
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMaxOfBasicStreamWithIntElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : int {
			                  return $person->age;
		                   });

		$basicStream->max (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::max
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMaxOfBasicStreamWithFloatElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : float {
			                  return $person->age * 0.1;
		                   });

		$basicStream->max (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::max
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMaxOfBasicStreamWithBoolElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : bool {
			                  return TRUE;
		                   });

		$basicStream->max (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::max
	 */
	public function testMaxOfBasicStreamWithObjects() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$priorityQueue = new PriorityQueue ($arrayListOfPersons, new PersonComparator());

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		// Get the min and compare with the last element in the PriorityQueue
		$maxInStream = $basicStream->max (new PersonComparator());
		$this->assertNotNull ($maxInStream);
		$this->assertTrue ($maxInStream->isPresent());

		// Get the last element of the priority queue
		$maxInPriorityQueue = NULL;

		foreach ($priorityQueue->iterator() as $element)
			$maxInPriorityQueue = $element;

		$this->assertNotNull ($maxInPriorityQueue);

		$this->assertEquals ($maxInPriorityQueue, $maxInStream->get());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::noneMatch
	 */
	public function testNoneMatchOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertTrue ($basicStream->noneMatch (new HasPersonOddAgePredicate()));
		$this->assertTrue ($basicStream->noneMatch (new HasPersonMoreThanOneWordAsNamePredicate()));
		$this->assertTrue ($basicStream->noneMatch (new HasPersonNoAgeValuePredicate()));
		$this->assertTrue ($basicStream->noneMatch (new HasPersonNameWithValuePredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::noneMatch
	 */
	public function testNoneMatchOfNotEmptyBasicStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertFalse ($basicStream->noneMatch (new HasPersonOddAgePredicate()));
		$this->assertFalse ($basicStream->noneMatch (new HasPersonMoreThanOneWordAsNamePredicate()));
		$this->assertTrue ($basicStream->noneMatch (new HasPersonNoAgeValuePredicate()));
		$this->assertFalse ($basicStream->noneMatch (new HasPersonNameWithValuePredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::reduce
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testReduceWithClosureWithDifferentOfTwoParameters() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->reduce (function (int $p) {
			                     $p += 1;
		                      });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::reduce
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testReduceWithClosureWithReturnedTypeDifferentOfFirstParamter() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->reduce (function (int $p, Person $person) : float {
			                     return $person->age * 0.1;
		                      });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::reduce
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testReduceWithClosureWithSecondParameterDifferentOfStoredElementsInStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->reduce (function ($accumulatedValue, Optional $optional) {
			                     return 1;
		                      });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::reduce
	 */
	public function testReduceWithoutInitialValue() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$sumOfAge = 0;
		foreach ($arrayListOfPersons->iterator() as $person)
			$sumOfAge += $person->age;

		$this->assertGreaterThan (0, $sumOfAge);

		$result = $basicStream->reduce (function ($accumulatedValue, Person $person) {

			                               if (is_null ($accumulatedValue))
			                     	          $accumulatedValue = 0;

			                               return $person->age + $accumulatedValue;
		                                });
		$this->assertNotNull ($result);
		$this->assertTrue ($result->isPresent());
		$this->assertEquals ($sumOfAge, $result->get());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::reduce
	 */
	public function testReduceWithInitialValue() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$sumOfAge = 0;
		foreach ($arrayListOfPersons->iterator() as $person)
			$sumOfAge += $person->age;

		$this->assertGreaterThan (0, $sumOfAge);

		$result = $basicStream->reduce (function ($accumulatedValue, Person $person) {
				                           return $person->age + $accumulatedValue;
		                                }
		                               ,0);
		$this->assertNotNull ($result);
		$this->assertTrue ($result->isPresent());
		$this->assertEquals ($sumOfAge, $result->get());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sorted
	 */
	public function testSortedOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		$basicStream->sorted();
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sorted
	 */
	public function testSortedOfBasicStreamWithObjects() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$priorityQueue = new PriorityQueue ($arrayListOfPersons);

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			if ($element->equals ($contentOfStream[$i]))
				$numberOfElementsInTheSamePosition++;

			$i++;
		}
		$this->assertGreaterThan (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, $priorityQueue->size());

		// After ordering the elements in the stream
		$basicStream->sorted();

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			$this->assertEquals ($element, $contentOfStream[$i]);
			$i++;
		}
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sorted
	 */
	public function testSortedOfBasicStreamWithIntegers() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$ordinationageArray = array();
		foreach ($arrayListOfPersons->iterator() as $element)
			$ordinationageArray[] = $element->age;

		sort ($ordinationageArray);

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationageArray), $basicStream->count());
		$this->assertEquals (count ($ordinationageArray), count ($basicStream->toArray()));

		$basicStream->map (function (Person $person) : int {
			                  return $person->age;
		                   });

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		for ($i = 0; $i < count ($contentOfStream); $i++) {

			if ($contentOfStream[$i] === $ordinationageArray[$i])
				$numberOfElementsInTheSamePosition++;
		}
		$this->assertGreaterThan (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, count ($ordinationageArray));

		// After ordering the elements in the stream
		$basicStream->sorted();

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationageArray), $basicStream->count());
		$this->assertEquals (count ($ordinationageArray), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($ordinationageArray[$i], $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sorted
	 */
	public function testSortedOfBasicStreamWithStrings() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$ordinationageArray = array();
		foreach ($arrayListOfPersons->iterator() as $element)
			$ordinationageArray[] = $element->name;

		sort ($ordinationageArray);

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationageArray), $basicStream->count());
		$this->assertEquals (count ($ordinationageArray), count ($basicStream->toArray()));

		$basicStream->map (function (Person $person) : string {
			                  return $person->name;
		                   });

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		for ($i = 0; $i < count ($contentOfStream); $i++) {

			if ($contentOfStream[$i] === $ordinationageArray[$i])
				$numberOfElementsInTheSamePosition++;
		}
		$this->assertGreaterThan (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, count ($ordinationageArray));

		// After ordering the elements in the stream
		$basicStream->sorted();

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationageArray), $basicStream->count());
		$this->assertEquals (count ($ordinationageArray), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($ordinationageArray[$i], $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 */
	public function testSortedByComparatorOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->sortedByComparator (new PersonComparator());

		$this->assertEquals (0, $basicStream->count());
		$this->assertEmpty ($basicStream->toArray());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByComparatorOfBasicStreamWithStringElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : string {
			                  return $person->name;
		                   });

		$basicStream->sortedByComparator (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByComparatorOfBasicStreamWithIntElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : int {
			                  return $person->age;
		                   });

		$basicStream->sortedByComparator (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByComparatorOfBasicStreamWithFloatElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : float {
			                  return $person->age * 0.1;
		                   });

		$basicStream->sortedByComparator (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByComparatorOfBasicStreamWithBoolElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);
		$basicStream->map (function (Person $person) : bool {
			                  return TRUE;
		                   });

		$basicStream->sortedByComparator (new PersonComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 */
	public function testSortedByComparatorOfBasicStreamWithObjects() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$priorityQueue = new PriorityQueue ($arrayListOfPersons, new PersonComparator());

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			if ($element->equals ($contentOfStream[$i]))
				$numberOfElementsInTheSamePosition++;

			$i++;
		}
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, $basicStream->count());
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, $priorityQueue->size());

		// After ordering the elements in the stream
		$basicStream->sortedByComparator (new PersonComparator());

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			$this->assertEquals ($element, $contentOfStream[$i]);
			$i++;
		}
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByLambdaWithClosureWithDifferentOfTwoParameters() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->sortedByLambda (function (int $p) {
			                             $p += 1;
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByLambdaWithClosureWithParameterTypeDifferentOfStreamElements() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->sortedByLambda (function (string $p1, string $p2) : int {
			                             $p1 .= "_test";
			                             $p2 .= "_test";
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByLambdaWithClosureWithInvalidReturnedType() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$basicStream->sortedByLambda (function (Person $person1, Person $person2) : Person {
			                             return $person1;
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 */
	public function testSortedByLambdaOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		$basicStream->sortedByLambda (function (Person $person1, Person $person2) : int {
			                             return $person1->age;
		                              });

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 */
	public function testSortedByLambdaOfBasicStreamWithObjects() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$priorityQueue = new PriorityQueue ($arrayListOfPersons, new PersonComparator());

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			if ($element->equals ($contentOfStream[$i]))
				$numberOfElementsInTheSamePosition++;

			$i++;
		}
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, $basicStream->count());
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, $priorityQueue->size());

		// After ordering the elements in the stream
		$basicStream->sortedByLambda (function (Person $person1, Person $person2): int {
			                             return strcmp ($person2->name, $person1->name);
	                                  });

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			$this->assertEquals ($element, $contentOfStream[$i]);
			$i++;
		}
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 */
	public function testSortedByLambdaOfBasicStreamWithStrings() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$ordinationageArray = array();
		foreach ($arrayListOfPersons->iterator() as $element)
			$ordinationageArray[] = $element->name;

		usort ($ordinationageArray, function (string $string1, string $string2) : int {

			                                   if ($string1 == $string2)
				                                  return 0;

			                                   return ($string1 < $string2) ? -1 : 1;
		                                    });

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationageArray), $basicStream->count());
		$this->assertEquals (count ($ordinationageArray), count ($basicStream->toArray()));

		$basicStream->map (function (Person $person) : string {
			                  return $person->name;
		                   });

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		for ($i = 0; $i < count ($contentOfStream); $i++) {

			if ($contentOfStream[$i] === $ordinationageArray[$i])
				$numberOfElementsInTheSamePosition++;
		}
		$this->assertGreaterThan (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, count ($ordinationageArray));

		// After ordering the elements in the stream
		$basicStream->sortedByLambda (function (string $string1, string $string2) : int {

			                             if ($string1 == $string2)
				                            return 0;

			                             return ($string1 < $string2) ? -1 : 1;
		                              });

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationageArray), $basicStream->count());
		$this->assertEquals (count ($ordinationageArray), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($ordinationageArray[$i], $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 */
	public function testSortedByLambdaOfBasicStreamWithFloats() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$ordinationageArray = array();
		foreach ($arrayListOfPersons->iterator() as $element)
			$ordinationageArray[] = $element->age * 0.1;

		usort ($ordinationageArray, function (float $float1, float $float2) : int {

			                                   if ($float1 == $float2)
				                                  return 0;

				                               return ($float1 < $float2) ? -1 : 1;
		                                    });

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationageArray), $basicStream->count());
		$this->assertEquals (count ($ordinationageArray), count ($basicStream->toArray()));

		$basicStream->map (function (Person $person) : float {
			                  return $person->age * 0.1;
		                   });

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		for ($i = 0; $i < count ($contentOfStream); $i++) {

			if ($contentOfStream[$i] === $ordinationageArray[$i])
				$numberOfElementsInTheSamePosition++;
		}
		$this->assertGreaterThan (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, count ($ordinationageArray));

		// After ordering the elements in the stream
		$basicStream->sortedByLambda (function (float $float1, float $float2) : int {

		                                 if ($float1 == $float2)
			                                return 0;

			                             return ($float1 < $float2) ? -1 : 1;
		                              });

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationageArray), $basicStream->count());
		$this->assertEquals (count ($ordinationageArray), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($ordinationageArray[$i], $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::toArray
	 */
	public function testToArrayOfEmptyBasicStream() {

		$arrayListOfPersons = new ArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::toArray
	 */
	public function testToArrayOfNotEmptyBasicStream() {

		$arrayListOfPersons = $this->generatePersonsArrayList();
		$basicStream = new BasicStream ($arrayListOfPersons);

		$this->assertEquals ($arrayListOfPersons->size(), $basicStream->count());
		$this->assertEquals ($arrayListOfPersons->size(), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($arrayListOfPersons->get($i), $contentOfStream[$i]);
	}


	/**
	 * Uses in the different tests as "initial collection"
	 */
	private function generatePersonsArrayList (bool $addDuplicates = TRUE) : ArrayList {

		$car1 = new Car ('A-2134', 2015);
		$car2 = new Car ('B-9999', 2015);
		$car3 = new Car ('C-4567', 2010);
		$car4 = new Car ('D-1675', 2000);

		$arrayListOfCars1 = new ArrayList();
		$arrayListOfCars1->add ($car1);
		$arrayListOfCars1->add ($car3);

		$arrayListOfCars2 = new ArrayList();
		$arrayListOfCars2->add ($car2);

		$arrayListOfCars3 = new ArrayList();
		$arrayListOfCars3->add ($car1);
		$arrayListOfCars3->add ($car4);

		// Creates the collection of Persons
		$arrayListOfPersons = new ArrayList();

		$arrayListOfPersons->add (new Person ("Alba", 11, FALSE));
		$arrayListOfPersons->add (new Person ("Albert", 18, TRUE, $arrayListOfCars2));
		$arrayListOfPersons->add (new Person ("Bob", 9, TRUE));
		$arrayListOfPersons->add (new Person ("Clark Smith", 34, TRUE));
		$arrayListOfPersons->add (new Person ("Dalia", 19, FALSE));

		$arrayListOfPersons->add (new Person ("Howard Shore", 33, TRUE));
		$arrayListOfPersons->add (new Person ("John Snow", 29, TRUE, $arrayListOfCars1));
		$arrayListOfPersons->add (new Person ("Mark", 20, TRUE));
		$arrayListOfPersons->add (new Person ("Mary Hanks", 44, FALSE));
		$arrayListOfPersons->add (new Person ("Sara Clark", 55, TRUE, $arrayListOfCars3));
		$arrayListOfPersons->add (new Person ("Sonny John McKay", 24, TRUE));

		// Adds some duplicate elements
		if ($addDuplicates) {
			$arrayListOfPersons->add (new Person ("Alba", 11, FALSE));
			$arrayListOfPersons->add (new Person ("John Snow", 29, TRUE, $arrayListOfCars1));
			$arrayListOfPersons->add (new Person ("John Snow", 29, TRUE, $arrayListOfCars1));
			$arrayListOfPersons->add (new Person ("Mary Hanks", 44, FALSE));
			$arrayListOfPersons->add (new Person ("Sara Clark", 55, TRUE, $arrayListOfCars3));
		}
		return $arrayListOfPersons;
	}

}

?>