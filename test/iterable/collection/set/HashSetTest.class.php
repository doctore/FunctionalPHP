<?php

namespace FunctionalPHP\test\iterable\collection\set;

require_once '../../../LoadRequiredFiles.php';


use PHPUnit\Framework\TestCase;

use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\queue\PriorityQueue;
use FunctionalPHP\iterable\collection\set\HashSet;
use FunctionalPHP\iterable\collection\set\SortedSet;

use FunctionalPHP\test\Person;
use FunctionalPHP\test\HasPersonOddAgePredicate;
use FunctionalPHP\test\HasPersonMoreThanOneWordAsNamePredicate;
use FunctionalPHP\test\HasPersonNoAgeValuePredicate;
use FunctionalPHP\test\HasPersonNameWithValuePredicate;


/**
 * Class used to test FunctionalPHP\collection\set\HashSet
 */
final class HashSetTest extends TestCase {


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::__construct
	 */
	public function testCreateEmptyHashSet() {

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());
		$this->assertEquals (0, $hashSet->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::__construct
	 */
	public function testCreateNotEmptyHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet1 = new HashSet();
		$hashSet1->add ($person1);
		$hashSet1->add ($person2);
		$hashSet1->add ($person3);

		$hashSet2 = new HashSet ($hashSet1);
		$this->assertFalse ($hashSet2->isEmpty());
		$this->assertEquals ($hashSet1->size(), $hashSet2->size());

		foreach ($hashSet1->iterator() as $element)
			$this->assertTrue ($hashSet2->contains ($element));
	}



	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::add
	 */
	public function testAddElements() {

		$person1      = new Person ("John", 18, TRUE);
		$person1Clone = new Person ("John", 18, FALSE);
		$person2      = new Person ("Mary", 20, FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());

		$this->assertTrue ($hashSet->add ($person1));

		$this->assertFalse ($hashSet->isEmpty());
		$this->assertEquals (1, $hashSet->size());
		$this->assertTrue ($hashSet->contains ($person1));

		foreach ($hashSet->iterator() as $element) {

			$this->assertEquals ($person1->age, $element->age);
			$this->assertEquals ($person1->name, $element->name);
			$this->assertEquals ($person1->isMale, $element->isMale);
		}

		// Adds an "equal object"
		$this->assertFalse ($hashSet->add ($person1Clone));
		$this->assertEquals (1, $hashSet->size());
		$this->assertTrue ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person1Clone));

		// Checks that $person1 was replaced by $person1Clone
		foreach ($hashSet->iterator() as $element) {

			$this->assertEquals ($person1Clone->age, $element->age);
			$this->assertEquals ($person1Clone->name, $element->name);
			$this->assertNotEquals ($person1Clone->isMale, $element->isMale);
		}
		// Adds $person2
		$this->assertTrue ($hashSet->add ($person2));
		$this->assertEquals (2, $hashSet->size());
		$this->assertTrue ($hashSet->contains ($person1Clone));
		$this->assertTrue ($hashSet->contains ($person2));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::addAll
	 */
	public function testAddAllElementsOfEmptyCollection() {

		$hashSet = new HashSet();
		$this->assertFalse ($hashSet->addAll (new HashSet()));
		$this->assertTrue ($hashSet->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::addAll
	 */
	public function testAddAllElementsWithHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet1 = new HashSet();
		$this->assertTrue ($hashSet1->isEmpty());

		$hashSet2 = new HashSet();
		$hashSet2->add ($person1);
		$hashSet2->add ($person2);
		$hashSet2->add ($person3);
		$this->assertEquals (3, $hashSet2->size());

		// Adds elements of $hashSet2 inside $hashSet1
		$this->assertTrue ($hashSet1->addAll ($hashSet2));
		$this->assertFalse ($hashSet1->isEmpty());
		$this->assertEquals ($hashSet1->size(), $hashSet2->size());

		foreach ($hashSet1->iterator() as $element)
			$this->assertTrue ($hashSet2->contains ($element));

		// Adds again all elements of $hashSet2 inside $hashSet1
		$this->assertTrue ($hashSet1->addAll ($hashSet2));
		$this->assertEquals ($hashSet1->size(), $hashSet2->size());

		foreach ($hashSet2->iterator() as $element)
			$this->assertTrue ($hashSet1->contains ($element));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::addAll
	 */
	public function testAddAllElementsWithArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());

		$arrayList = new ArrayList();
		$arrayList->add ($person1);
		$arrayList->add ($person2);
		$arrayList->add ($person3);
		$this->assertEquals (3, $arrayList->size());

		// Adds elements of $arrayList inside $hashSet
		$this->assertTrue ($hashSet->addAll ($arrayList));
		$this->assertFalse ($hashSet->isEmpty());
		$this->assertEquals ($arrayList->size(), $hashSet->size());

		foreach ($arrayList->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));

		// Adds again all elements of $arrayList inside $hashSet
		$this->assertTrue ($hashSet->addAll ($arrayList));
		$this->assertEquals ($arrayList->size(), $hashSet->size());

		foreach ($arrayList->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::addAll
	 */
	public function testAddAllElementsWithSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());

		$sortedSet = new SortedSet();
		$sortedSet->add ($person1);
		$sortedSet->add ($person2);
		$sortedSet->add ($person3);
		$this->assertEquals (3, $sortedSet->size());

		// Adds elements of $sortedSet inside $hashSet
		$this->assertTrue ($hashSet->addAll ($sortedSet));
		$this->assertFalse ($hashSet->isEmpty());
		$this->assertEquals ($sortedSet->size(), $hashSet->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));

		// Adds again all elements of $sortedSet inside $hashSet
		$this->assertTrue ($hashSet->addAll ($sortedSet));
		$this->assertEquals ($sortedSet->size(), $hashSet->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::addAll
	 */
	public function testAddAllElementsWithPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());

		$priorityQueue = new PriorityQueue();
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person3);
		$this->assertEquals (3, $priorityQueue->size());

		// Adds elements of $priorityQueue inside $hashSet
		$this->assertTrue ($hashSet->addAll ($priorityQueue));
		$this->assertFalse ($hashSet->isEmpty());
		$this->assertEquals ($priorityQueue->size(), $hashSet->size());

		foreach ($priorityQueue->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));

		// Adds again all elements of $priorityQueue inside $hashSet
		$this->assertTrue ($hashSet->addAll ($priorityQueue));
		$this->assertEquals ($priorityQueue->size(), $hashSet->size());

		foreach ($priorityQueue->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::clear
	 */
	public function testClearHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());
		$this->assertEquals (0, $hashSet->size());

		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$hashSet->add ($person3);
		$this->assertFalse ($hashSet->isEmpty());
		$this->assertEquals (3, $hashSet->size());

		$hashSet->clear();
		$this->assertTrue ($hashSet->isEmpty());
		$this->assertEquals (0, $hashSet->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::contains
	 */
	public function testCheckElementsContainedInHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$this->assertFalse ($hashSet->contains ($person1));
		$this->assertFalse ($hashSet->contains ($person2));
		$this->assertFalse ($hashSet->contains ($person3));

		$hashSet->add ($person1);
		$this->assertTrue ($hashSet->contains ($person1));
		$this->assertFalse ($hashSet->contains ($person2));
		$this->assertFalse ($hashSet->contains ($person3));

		$hashSet->add ($person2);
		$this->assertTrue ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person2));
		$this->assertFalse ($hashSet->contains ($person3));

		$hashSet->add ($person3);
		$this->assertTrue ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person2));
		$this->assertTrue ($hashSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet1 = new HashSet();
		$hashSet2 = new HashSet();
		$this->assertTrue ($hashSet1->containsAll ($hashSet2));

		$hashSet1->add ($person1);
		$hashSet2->add ($person1);
		$this->assertTrue ($hashSet1->containsAll ($hashSet2));
		$this->assertTrue ($hashSet2->containsAll ($hashSet1));

		$hashSet1->add ($person2);
		$this->assertTrue ($hashSet1->containsAll ($hashSet2));
		$this->assertFalse ($hashSet2->containsAll ($hashSet1));

		$hashSet2->add ($person3);
		$this->assertFalse ($hashSet1->containsAll ($hashSet2));
		$this->assertFalse ($hashSet2->containsAll ($hashSet1));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$arrayList = new ArrayList();
		$this->assertTrue ($hashSet->containsAll ($arrayList));

		$hashSet->add ($person1);
		$arrayList->add ($person2);
		$this->assertFalse ($hashSet->containsAll ($arrayList));

		$hashSet->add ($person2);
		$this->assertTrue ($hashSet->containsAll ($arrayList));

		$arrayList->add ($person3);
		$this->assertFalse ($hashSet->containsAll ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$sortedSet = new SortedSet();
		$this->assertTrue ($hashSet->containsAll ($sortedSet));

		$hashSet->add ($person1);
		$sortedSet->add ($person2);
		$this->assertFalse ($hashSet->containsAll ($sortedSet));

		$hashSet->add ($person2);
		$this->assertTrue ($hashSet->containsAll ($sortedSet));

		$sortedSet->add ($person3);
		$this->assertFalse ($hashSet->containsAll ($sortedSet));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($hashSet->containsAll ($priorityQueue));

		$hashSet->add ($person1);
		$priorityQueue->add ($person2);
		$this->assertFalse ($hashSet->containsAll ($priorityQueue));

		$hashSet->add ($person2);
		$this->assertTrue ($hashSet->containsAll ($priorityQueue));

		$priorityQueue->add ($person3);
		$this->assertFalse ($hashSet->containsAll ($priorityQueue));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::equals
	 */
	public function testCheckEqualityWithHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet1 = new HashSet();
		$hashSet2 = new HashSet();
		$this->assertTrue ($hashSet1->equals ($hashSet1));
		$this->assertTrue ($hashSet1->equals ($hashSet2));
		$this->assertTrue ($hashSet2->equals ($hashSet1));

		$hashSet1->add ($person1);
		$this->assertFalse ($hashSet1->equals ($hashSet2));
		$this->assertFalse ($hashSet2->equals ($hashSet1));

		$hashSet2->add ($person2);
		$this->assertFalse ($hashSet1->equals ($hashSet2));
		$this->assertFalse ($hashSet2->equals ($hashSet1));

		// The set have the same elements but added in different order
		$hashSet1->add ($person2);
		$hashSet2->add ($person1);
		$this->assertEquals (2, $hashSet1->size());
		$this->assertEquals (2, $hashSet2->size());

		$this->assertTrue ($hashSet1->equals ($hashSet2));
		$this->assertTrue ($hashSet2->equals ($hashSet1));

		foreach ($hashSet1->iterator() as $element)
			$this->assertTrue ($hashSet2->contains ($element));

		foreach ($hashSet2->iterator() as $element)
			$this->assertTrue ($hashSet1->contains ($element));

		// Adds the $person3
		$hashSet1->add ($person3);
		$this->assertFalse ($hashSet1->equals ($hashSet2));
		$this->assertFalse ($hashSet2->equals ($hashSet1));

		$hashSet2->add ($person3);
		$this->assertTrue ($hashSet1->equals ($hashSet2));
		$this->assertTrue ($hashSet2->equals ($hashSet1));

		foreach ($hashSet1->iterator() as $element)
			$this->assertTrue ($hashSet2->contains ($element));

		foreach ($hashSet2->iterator() as $element)
			$this->assertTrue ($hashSet1->contains ($element));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::equals
	 */
	public function testCheckEqualityWithArrayList() {

		$person = new Person ("John", 18, TRUE);

		$hashSet = new HashSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($hashSet->equals ($arrayList));

		$hashSet->add ($person);
		$this->assertFalse ($hashSet->equals ($arrayList));

		$arrayList->add ($person);
		$this->assertFalse ($hashSet->equals ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::equals
	 */
	public function testCheckEqualityWithSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$sortedSet = new SortedSet();
		$this->assertTrue ($hashSet->equals ($sortedSet));
		$this->assertTrue ($sortedSet->equals ($hashSet));

		$hashSet->add ($person1);
		$this->assertFalse ($hashSet->equals ($sortedSet));
		$this->assertFalse ($sortedSet->equals ($hashSet));

		$sortedSet->add ($person1);
		$this->assertTrue ($hashSet->equals ($sortedSet));
		$this->assertTrue ($sortedSet->equals ($hashSet));

		foreach ($hashSet->iterator() as $element)
			$this->assertTrue ($sortedSet->contains ($element));

		foreach ($sortedSet->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));

		// Adds $person2
		$hashSet->add ($person2);
		$this->assertFalse ($hashSet->equals ($sortedSet));
		$this->assertFalse ($sortedSet->equals ($hashSet));

		$sortedSet->add ($person2);
		$this->assertTrue ($hashSet->equals ($sortedSet));
		$this->assertTrue ($sortedSet->equals ($hashSet));

		// Adds $person3
		$hashSet->add ($person3);
		$this->assertFalse ($hashSet->equals ($sortedSet));
		$this->assertFalse ($sortedSet->equals ($hashSet));

		$sortedSet->add ($person3);
		$this->assertTrue ($hashSet->equals ($sortedSet));
		$this->assertTrue ($sortedSet->equals ($hashSet));

		foreach ($hashSet->iterator() as $element)
			$this->assertTrue ($sortedSet->contains ($element));

		foreach ($sortedSet->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::equals
	 */
	public function testCheckEqualityWithPriorityQueue() {

		$person = new Person ("John", 18, TRUE);

		$hashSet = new HashSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($hashSet->equals ($priorityQueue));

		$hashSet->add ($person);
		$this->assertFalse ($hashSet->equals ($priorityQueue));

		$priorityQueue->add ($person);
		$this->assertFalse ($hashSet->equals ($priorityQueue));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::filter
	 */
	public function testFilterOfEmptyHashSet() {

		$hashSet = new HashSet();
		$this->assertEquals (0, $hashSet->size());

		$filteredHashSet = $hashSet->filter (new HasPersonOddAgePredicate());
		$this->assertEquals (0, $filteredHashSet->size());

		$filteredHashSet = $hashSet->filter (new HasPersonMoreThanOneWordAsNamePredicate());
		$this->assertEquals (0, $filteredHashSet->size());

		$filteredHashSet = $hashSet->filter (new HasPersonNoAgeValuePredicate());
		$this->assertEquals (0, $filteredHashSet->size());

		$filteredHashSet = $hashSet->filter (new HasPersonNameWithValuePredicate());
		$this->assertEquals (0, $filteredHashSet->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::filter
	 */
	public function testFilterOnePredicate() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Sara", 25, FALSE);
		$person3 = new Person ("Mary Swan", 20, FALSE);
		$person4 = new Person ("Seth", 31, TRUE);
		$person5 = new Person ("Marc", 11, TRUE);

		$hashSet = new HashSet();
		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$hashSet->add ($person3);
		$hashSet->add ($person4);
		$hashSet->add ($person5);

		// HasPersonOddAgePredicate
		$filteredHashSet = $hashSet->filter (new HasPersonOddAgePredicate());
		$this->assertGreaterThan (0, $filteredHashSet->size());

		foreach ($filteredHashSet->iterator() as $person)
			$this->assertTrue ($person->age % 2 != 0);

		// HasPersonMoreThanOneWordAsNamePredicate
		$filteredHashSet = $hashSet->filter (new HasPersonMoreThanOneWordAsNamePredicate());
		$this->assertGreaterThan (0, $filteredHashSet->size());

		foreach ($filteredHashSet->iterator() as $person)
			$this->assertTrue (str_word_count ($person->name) > 1);

		// HasPersonNoAgeValuePredicate
		$filteredHashSet = $hashSet->filter (new HasPersonNoAgeValuePredicate());
		$this->assertEquals (0, $filteredHashSet->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::filter
	 */
	public function testFilterMoreThanOnePredicate() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Sara", 25, FALSE);
		$person3 = new Person ("Mary Swan", 21, FALSE);
		$person4 = new Person ("Seth", 31, TRUE);
		$person5 = new Person ("Marc", 11, TRUE);

		$hashSet = new HashSet();
		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$hashSet->add ($person3);
		$hashSet->add ($person4);
		$hashSet->add ($person5);

		// HasPersonOddAgePredicate && HasPersonMoreThanOneWordAsNamePredicate
		$filteredHashSet = $hashSet->filter (new HasPersonOddAgePredicate())
		                           ->filter (new HasPersonMoreThanOneWordAsNamePredicate());

		$this->assertGreaterThan (0, $filteredHashSet->size());

		foreach ($filteredHashSet->iterator() as $person) {

			$this->assertTrue ($person->age % 2 != 0);
			$this->assertTrue (str_word_count ($person->name) > 1);
		}
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaWithClosureWithMoreThanOneParameter() {

		$hashSet = new HashSet();

		$hashSet->filterByLambda (function (int $p1, string $p2) {
			                         $p1 += 1;
			                         $p2 .= "_test";
		                          });
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaWithClosureWithParameterTypeDifferentOfStreamElements() {

		$hashSet = new HashSet();

		$hashSet->filterByLambda (function (string $p1) {
			                         $p1 .= "_test";
		                          });
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaWithClosureWithInvalidReturnedType() {

		$hashSet = new HashSet();

		$hashSet->filterByLambda (function (Person $person) : Person {
			                         $person->age *= 2;
		                          });
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::filterByLambda
	 */
	public function testFilterByLambdaByOneIteration() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Sara", 25, FALSE);
		$person3 = new Person ("Mary Swan", 21, FALSE);
		$person4 = new Person ("Seth", 31, TRUE);
		$person5 = new Person ("Son", 11, TRUE);

		$hashSet = new HashSet();
		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$hashSet->add ($person3);
		$hashSet->add ($person4);
		$hashSet->add ($person5);

		// Filter even age values
		$filteredHashSet = $hashSet->filterByLambda (function (Person $person) : bool {
			                                            return $person->age % 2 == 0;
		                                             });

		$this->assertGreaterThan (0, $filteredHashSet->size());

		foreach ($filteredHashSet->iterator() as $person)
			$this->assertEquals (0, $person->age % 2);

		// Filter name values with lenght = 3
		$filteredHashSet = $hashSet->filterByLambda (function (Person $person) : bool {
			                                            return strlen ($person->name) == 3;
		                                             });

		$this->assertGreaterThan (0, $filteredHashSet->size());
		foreach ($filteredHashSet->iterator() as $person)
			$this->assertEquals (3, strlen ($person->name));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::filterByLambda
	 */
	public function testFilterByLambdaByMoreThanOneIteration() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Sara", 25, FALSE);
		$person3 = new Person ("Mary Swan", 21, FALSE);
		$person4 = new Person ("Seth", 31, TRUE);
		$person5 = new Person ("Son", 12, TRUE);

		$hashSet = new HashSet();
		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$hashSet->add ($person3);
		$hashSet->add ($person4);
		$hashSet->add ($person5);

		// Filter even age values and name values with lenght = 3
		$filteredHashSet = $hashSet->filterByLambda (function (Person $person) : bool {
			                                            return $person->age % 2 == 0;
		                                             })
		                           ->filterByLambda (function (Person $person) : bool {
			                                            return strlen ($person->name) == 3;
		                                             });

		$this->assertGreaterThan (0, $filteredHashSet->size());

		foreach ($filteredHashSet->iterator() as $person) {

			$this->assertEquals (0, $person->age % 2);
			$this->assertEquals (3, strlen ($person->name));
		}
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithMoreThanOneParameter() {

		$hashSet = new HashSet();

		$hashSet->forEach (function (int $p1, string $p2) {
			                  $p1 += 1;
			                  $p2 .= "_test";
		                   });
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithParameterTypeDifferentOfObject() {

		$hashSet = new HashSet();

		$hashSet->forEach (function (int $p1) {
			                  $p1 += 1;
		                   });
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithInvalidReturnedType() {

		$hashSet = new HashSet();
		$hashSet->add (new Person ("John", 18, TRUE));

		$hashSet->forEach (function (Person $person) : int {
			                  $person->age *= 2;
		                   });
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::forEach
	 */
	public function testForEachOfValidClosureFunction() {

		$person1 = new Person ("John", 18, TRUE);
		$person1Clone = new Person ("John", 18, TRUE);

		$person2 = new Person ("Sara", 25, FALSE);
		$person2Clone = new Person ("Sara", 25, FALSE);

		$person3 = new Person ("Mary", 20, FALSE);
		$person3Clone = new Person ("Mary", 20, FALSE);

		$hashSetOriginal = new HashSet();
		$hashSetOriginal->add ($person1);
		$hashSetOriginal->add ($person2);
		$hashSetOriginal->add ($person3);

		$hashSetToModified = new HashSet();
		$hashSetToModified->add ($person1Clone);
		$hashSetToModified->add ($person2Clone);
		$hashSetToModified->add ($person3Clone);

		$hashSetToModified->forEach (function (Person $person) {
			                            $person->age *= 2;
			                            $person->isMale = !$person->isMale;
		});

		$this->assertGreaterThan (0, $hashSetToModified->size());
		$this->assertEquals ($hashSetOriginal->size(), $hashSetToModified->size());

		// Checks that has changed the value of: age and isMale properties
		$elementsComparated = 0;
		foreach ($hashSetOriginal->iterator() as $elementOriginal) {

			foreach ($hashSetToModified->iterator() as $elementModified) {

				if (strcmp ($elementOriginal->name, $elementModified->name) == 0) {

					$this->assertEquals ($elementOriginal->name, $elementModified->name);
					$this->assertEquals ($elementOriginal->age*2, $elementModified->age);
					$this->assertEquals ($elementOriginal->isMale, !$elementModified->isMale);

					$elementsComparated++;
				}
			}
		}
		$this->assertEquals ($hashSetToModified->size(), $elementsComparated);
	}



	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::get
	 */
	public function testCheckGetElementsInHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$personEqualTo1 = new Person ("John", 18, FALSE);


		$hashSet = new HashSet();
		$this->assertFalse ($hashSet->get ($person1)->isPresent());
		$this->assertFalse ($hashSet->get ($person2)->isPresent());
		$this->assertFalse ($hashSet->get ($personEqualTo1)->isPresent());

		$hashSet->add ($person2);
		$this->assertFalse ($hashSet->get ($person1)->isPresent());
		$this->assertTrue ($hashSet->get ($person2)->isPresent());
		$this->assertFalse ($hashSet->get ($personEqualTo1)->isPresent());

		$hashSet->add ($person1);
		$this->assertTrue ($hashSet->get ($person1)->isPresent());
		$this->assertTrue ($hashSet->get ($personEqualTo1)->isPresent());

		$personInHashSet = $hashSet->get ($person1)->get();

		$this->assertEquals ($person1->name, $personInHashSet->name);
		$this->assertEquals ($person1->age, $personInHashSet->age);
		$this->assertEquals ($person1->isMale, $personInHashSet->isMale);

		$this->assertEquals ($personEqualTo1->name, $personInHashSet->name);
		$this->assertEquals ($personEqualTo1->age, $personInHashSet->age);

		// isMale property is different in both objects
		$this->assertNotEquals ($personEqualTo1->isMale, $personInHashSet->isMale);
	}



	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::hashCode
	 */
	public function testHashCodeOfHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$this->assertEquals (0, $hashSet->hashCode());

		$hashSet->add ($person1);
		$this->assertEquals ($person1->hashCode(), $hashSet->hashCode());

		$hashSet->add ($person2);
		$this->assertEquals ($person1->hashCode() + $person2->hashCode()
				            ,$hashSet->hashCode());

		$hashSet->add ($person3);
		$this->assertEquals ($person1->hashCode() + $person2->hashCode() + $person3->hashCode()
				            ,$hashSet->hashCode());
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::isEmpty
	 */
	public function testEmptyHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

    	$hashSet = new HashSet();
    	$this->assertTrue ($hashSet->isEmpty());
    	$this->assertEquals (0, $hashSet->size());

    	$hashSet->add ($person1);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (1, $hashSet->size());

    	$hashSet->add ($person2);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (2, $hashSet->size());

    	$hashSet->add ($person3);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (3, $hashSet->size());

    	// Does not accept duplicate elements
    	$hashSet->add ($person3);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (3, $hashSet->size());

    	// Removes every element
    	$hashSet->remove ($person3);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (2, $hashSet->size());

    	$hashSet->remove ($person2);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (1, $hashSet->size());

    	$hashSet->remove ($person1);
    	$this->assertTrue ($hashSet->isEmpty());
    	$this->assertEquals (0, $hashSet->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::iterator
	 */
	public function testIterateOverHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());

		foreach ($hashSet->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $person1
		$hashSet->add ($person1);
		$this->assertEquals (1, $hashSet->size());

		foreach ($hashSet->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Adds another person
		$hashSet->add ($person2);
		$hashSet->add ($person3);
		$this->assertEquals (3, $hashSet->size());

		$isPersonsInHashSet = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($hashSet->iterator() as $element) {

			if ($element->equals ($person1))
				$isPersonsInHashSet[1] = TRUE;

			elseif ($element->equals ($person2))
			$isPersonsInHashSet[2] = TRUE;

			elseif ($element->equals ($person3))
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
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::remove
	 */
	public function testRemoveElements() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Mark", 30, TRUE);
		$person5 = new Person ("Will", 22, TRUE);

		$hashSet = new HashSet();
		$this->assertFalse ($hashSet->remove ($person1));
		$this->assertFalse ($hashSet->remove ($person2));
		$this->assertFalse ($hashSet->remove ($person3));
		$this->assertFalse ($hashSet->remove ($person4));
		$this->assertFalse ($hashSet->remove ($person5));

		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$hashSet->add ($person3);
		$hashSet->add ($person4);
		$hashSet->add ($person5);
		$this->assertEquals (5, $hashSet->size());
		$this->assertTrue ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person2));
		$this->assertTrue ($hashSet->contains ($person3));
		$this->assertTrue ($hashSet->contains ($person4));
		$this->assertTrue ($hashSet->contains ($person5));

		// Removes $person1
		$this->assertTrue ($hashSet->remove ($person1));
		$this->assertEquals (4, $hashSet->size());

		$this->assertFalse ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person2));
		$this->assertTrue ($hashSet->contains ($person3));
		$this->assertTrue ($hashSet->contains ($person4));
		$this->assertTrue ($hashSet->contains ($person5));

		// Removes $person2
		$this->assertFalse ($hashSet->remove ($person1));
		$this->assertTrue ($hashSet->remove ($person2));
		$this->assertEquals (3, $hashSet->size());

		$this->assertFalse ($hashSet->contains ($person1));
		$this->assertFalse ($hashSet->contains ($person2));
		$this->assertTrue ($hashSet->contains ($person3));
		$this->assertTrue ($hashSet->contains ($person4));
		$this->assertTrue ($hashSet->contains ($person5));

		// Removes $person3
		$this->assertFalse ($hashSet->remove ($person2));
		$this->assertTrue ($hashSet->remove ($person3));
		$this->assertEquals (2, $hashSet->size());

		$this->assertFalse ($hashSet->contains ($person1));
		$this->assertFalse ($hashSet->contains ($person2));
		$this->assertFalse ($hashSet->contains ($person3));
		$this->assertTrue ($hashSet->contains ($person4));
		$this->assertTrue ($hashSet->contains ($person5));

		// Removes $person4
		$this->assertFalse ($hashSet->remove ($person3));
		$this->assertTrue ($hashSet->remove ($person4));
		$this->assertEquals (1, $hashSet->size());

		$this->assertFalse ($hashSet->contains ($person1));
		$this->assertFalse ($hashSet->contains ($person2));
		$this->assertFalse ($hashSet->contains ($person3));
		$this->assertFalse ($hashSet->contains ($person4));
		$this->assertTrue ($hashSet->contains ($person5));

		// Removes $person5
		$this->assertFalse ($hashSet->remove ($person4));
		$this->assertTrue ($hashSet->remove ($person5));
		$this->assertEquals (0, $hashSet->size());
		$this->assertTrue ($hashSet->isEmpty());

		$this->assertFalse ($hashSet->contains ($person1));
		$this->assertFalse ($hashSet->contains ($person2));
		$this->assertFalse ($hashSet->contains ($person3));
		$this->assertFalse ($hashSet->contains ($person4));
		$this->assertFalse ($hashSet->contains ($person5));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet1 = new HashSet();
		$hashSet2 = new HashSet();
		$this->assertFalse ($hashSet1->removeAll ($hashSet2));

		$hashSet1->add ($person1);
		$hashSet1->add ($person3);
		$hashSet2->add ($person2);

		$this->assertFalse ($hashSet1->removeAll ($hashSet2));
		$this->assertEquals (2, $hashSet1->size());

		$this->assertFalse ($hashSet2->removeAll ($hashSet1));
		$this->assertEquals (1, $hashSet2->size());

		// Adds $person1 twice
    	$hashSet1->add ($person1);
    	$hashSet2->add ($person1);

		$this->assertTrue ($hashSet1->removeAll ($hashSet2));
		$this->assertEquals (1, $hashSet1->size());
		$this->assertEquals (2, $hashSet2->size());
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($hashSet->removeAll ($arrayList));

		$hashSet->add ($person1);
		$hashSet->add ($person3);
		$arrayList->add ($person2);

		$this->assertFalse ($hashSet->removeAll ($arrayList));
		$this->assertEquals (2, $hashSet->size());

		// Adds $person1 twice
		$hashSet->add ($person1);
		$arrayList->add ($person1);

		$this->assertTrue ($hashSet->removeAll ($arrayList));
		$this->assertEquals (1, $hashSet->size());
		$this->assertFalse ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$sortedSet = new SortedSet();
		$this->assertFalse ($hashSet->removeAll ($sortedSet));

		$hashSet->add ($person1);
		$hashSet->add ($person3);
		$sortedSet->add ($person2);

		$this->assertFalse ($hashSet->removeAll ($sortedSet));
		$this->assertEquals (2, $hashSet->size());

		// Adds $person1 twice
		$hashSet->add ($person1);
		$sortedSet->add ($person1);

		$this->assertTrue ($hashSet->removeAll ($sortedSet));
		$this->assertEquals (1, $hashSet->size());
		$this->assertFalse ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($hashSet->removeAll ($priorityQueue));

		$hashSet->add ($person1);
		$hashSet->add ($person3);
		$priorityQueue->add ($person2);

		$this->assertFalse ($hashSet->removeAll ($priorityQueue));
		$this->assertEquals (2, $hashSet->size());

		// Adds $person1 twice
		$hashSet->add ($person1);
		$priorityQueue->add ($person1);

		$this->assertTrue ($hashSet->removeAll ($priorityQueue));
		$this->assertEquals (1, $hashSet->size());
		$this->assertFalse ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::retainAll
	 */
	public function testRetainAllElementsOfGivenHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet1 = new HashSet();
		$hashSet2 = new HashSet();
		$this->assertFalse ($hashSet1->retainAll ($hashSet2));

		// Retains all elements of an empty set
		$hashSet1->add ($person1);
		$this->assertTrue ($hashSet1->retainAll ($hashSet2));
		$this->assertTrue ($hashSet1->isEmpty());

		// Both sets shared $person1
		$hashSet1->add ($person1);
		$hashSet1->add ($person2);
		$hashSet2->add ($person1);

		$this->assertEquals (2, $hashSet1->size());
		$this->assertEquals (1, $hashSet2->size());

		$this->assertTrue ($hashSet1->retainAll ($hashSet2));
		$this->assertEquals (1, $hashSet1->size());

		foreach ($hashSet1->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Both sets have the same elements
		$hashSet1->add ($person2);
		$hashSet2->add ($person2);

		$this->assertEquals (2, $hashSet1->size());
		$this->assertEquals (2, $hashSet2->size());

		$this->assertFalse ($hashSet1->retainAll ($hashSet2));
		$this->assertEquals (2, $hashSet1->size());
		$this->assertEquals (2, $hashSet2->size());

		$isPersonsInBothSets = array (1 => FALSE, 2 => FALSE);
		foreach ($hashSet1->iterator() as $element) {

			if ($element->equals ($person1) && $hashSet2->contains ($element))
				$isPersonsInBothSets[1] = TRUE;

			elseif ($element->equals ($person2) && $hashSet2->contains ($element))
			$isPersonsInBothSets[2] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInBothSets[1]);
		$this->assertTrue ($isPersonsInBothSets[2]);

		// Adds $person3
		$hashSet1->add ($person3);
		$this->assertTrue ($hashSet1->retainAll ($hashSet2));
		$this->assertEquals (2, $hashSet1->size());

		$this->assertTrue ($hashSet1->contains ($person1));
		$this->assertTrue ($hashSet1->contains ($person2));
		$this->assertFalse ($hashSet1->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::retainAll
	 */
	public function testRetainAllElementsOfGivenArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($hashSet->retainAll ($arrayList));

		// Retains all elements of an empty list
		$hashSet->add ($person1);
		$this->assertTrue ($hashSet->retainAll ($arrayList));
		$this->assertTrue ($hashSet->isEmpty());

		// Both collections shared $person1
		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$arrayList->add ($person1);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (1, $arrayList->size());

		$this->assertTrue ($hashSet->retainAll ($arrayList));
		$this->assertEquals (1, $hashSet->size());

		foreach ($hashSet->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Both collections have the same elements
		$hashSet->add ($person2);
		$arrayList->add ($person2);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $arrayList->size());

		$this->assertFalse ($hashSet->retainAll ($arrayList));
		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $arrayList->size());

		$isPersonsInBothCollections = array (1 => FALSE, 2 => FALSE);
		foreach ($hashSet->iterator() as $element) {

			if ($element->equals ($person1) && $arrayList->contains ($element))
				$isPersonsInBothCollections[1] = TRUE;

			elseif ($element->equals ($person2) && $arrayList->contains ($element))
			$isPersonsInBothCollections[2] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInBothCollections[1]);
		$this->assertTrue ($isPersonsInBothCollections[2]);

		// Adds $person3
		$hashSet->add ($person3);
		$this->assertTrue ($hashSet->retainAll ($arrayList));
		$this->assertEquals (2, $hashSet->size());

		$this->assertTrue ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person2));
		$this->assertFalse ($hashSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::retainAll
	 */
	public function testRetainAllElementsOfGivenSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$sortedSet = new SortedSet();
		$this->assertFalse ($hashSet->retainAll ($sortedSet));

		// Retains all elements of an empty list
		$hashSet->add ($person1);
		$this->assertTrue ($hashSet->retainAll ($sortedSet));
		$this->assertTrue ($hashSet->isEmpty());

		// Both collections shared $person1
		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$sortedSet->add ($person1);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (1, $sortedSet->size());

		$this->assertTrue ($hashSet->retainAll ($sortedSet));
		$this->assertEquals (1, $hashSet->size());

		foreach ($hashSet->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Both collections have the same elements
		$hashSet->add ($person2);
		$sortedSet->add ($person2);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $sortedSet->size());

		$this->assertFalse ($hashSet->retainAll ($sortedSet));
		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $sortedSet->size());

		$isPersonsInBothCollections = array (1 => FALSE, 2 => FALSE);
		foreach ($hashSet->iterator() as $element) {

			if ($element->equals ($person1) && $sortedSet->contains ($element))
				$isPersonsInBothCollections[1] = TRUE;

			elseif ($element->equals ($person2) && $sortedSet->contains ($element))
			$isPersonsInBothCollections[2] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInBothCollections[1]);
		$this->assertTrue ($isPersonsInBothCollections[2]);

		// Adds $person3
		$hashSet->add ($person3);
		$this->assertTrue ($hashSet->retainAll ($sortedSet));
		$this->assertEquals (2, $hashSet->size());

		$this->assertTrue ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person2));
		$this->assertFalse ($hashSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::retainAll
	 */
	public function testRetainAllElementsOfGivenPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($hashSet->retainAll ($priorityQueue));

		// Retains all elements of an empty list
		$hashSet->add ($person1);
		$this->assertTrue ($hashSet->retainAll ($priorityQueue));
		$this->assertTrue ($hashSet->isEmpty());

		// Both collections shared $person1
		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$priorityQueue->add ($person1);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (1, $priorityQueue->size());

		$this->assertTrue ($hashSet->retainAll ($priorityQueue));
		$this->assertEquals (1, $hashSet->size());

		foreach ($hashSet->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Both collections have the same elements
		$hashSet->add ($person2);
		$priorityQueue->add ($person2);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $priorityQueue->size());

		$this->assertFalse ($hashSet->retainAll ($priorityQueue));
		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $priorityQueue->size());

		$isPersonsInBothCollections = array (1 => FALSE, 2 => FALSE);
		foreach ($hashSet->iterator() as $element) {

			if ($element->equals ($person1) && $priorityQueue->contains ($element))
				$isPersonsInBothCollections[1] = TRUE;

			elseif ($element->equals ($person2) && $priorityQueue->contains ($element))
			$isPersonsInBothCollections[2] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInBothCollections[1]);
		$this->assertTrue ($isPersonsInBothCollections[2]);

		// Adds $person3
		$hashSet->add ($person3);
		$this->assertTrue ($hashSet->retainAll ($priorityQueue));
		$this->assertEquals (2, $hashSet->size());

		$this->assertTrue ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person2));
		$this->assertFalse ($hashSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::size
	 */
	public function testSizeOfHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$this->assertEquals (0, $hashSet->size());
		$this->assertTrue ($hashSet->isEmpty());

		$hashSet->add ($person1);
		$this->assertEquals (1, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		$hashSet->add ($person2);
		$this->assertEquals (2, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		$hashSet->add ($person3);
		$this->assertEquals (3, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		// Does not permits duplicates
		$hashSet->add ($person2);
		$this->assertEquals (3, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		// Removes every element
		$hashSet->remove ($person2);
		$this->assertEquals (2, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		$hashSet->remove ($person1);
		$this->assertEquals (1, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		$hashSet->remove ($person3);
		$this->assertEquals (0, $hashSet->size());
		$this->assertTrue ($hashSet->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::stream
	 */
	public function testStreamOfHashSet() {

		// Empty HashSet
		$hashSet = new HashSet();
		$stream = $hashSet->stream();

		$this->assertNotNull ($stream);
		$this->assertEquals (0, $stream->count());
		$this->assertEmpty ($stream->toArray());

		// Not empty HashSet
		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();
		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$hashSet->add ($person3);

		$stream = $hashSet->stream();

		$this->assertNotNull ($stream);
		$this->assertEquals ($hashSet->size(), $stream->count());
		$this->assertNotEmpty ($stream->toArray());

		$contentOfStream = $stream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertTrue ($hashSet->contains ($contentOfStream[$i]));
	}


	/**
	 * @covers FunctionalPHP\iterable\collection\set\HashSet::toArray
	 */
	public function testToArrayOfHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$hashSet = new HashSet();

		$array = $hashSet->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (0, count ($array));

		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$hashSet->add ($person3);

		// Does not permits duplicates
		$hashSet->add ($person3);

		$array = $hashSet->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (3, count ($array));

		$isPersonsInArray = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($array as $element) {

			if ($element->equals ($person1))
				$isPersonsInArray[1] = TRUE;

			elseif ($element->equals ($person2))
			$isPersonsInArray[2] = TRUE;

			elseif ($element->equals ($person3))
			$isPersonsInArray[3] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isPersonsInArray[1]);
		$this->assertTrue ($isPersonsInArray[2]);
		$this->assertTrue ($isPersonsInArray[3]);
	}

}

?>