<?php

namespace FunctionalPHP\test\iterable\collection\queue;

require_once '../../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\queue\PriorityQueue;
use FunctionalPHP\iterable\collection\set\HashSet;
use FunctionalPHP\iterable\collection\set\SortedSet;
use FunctionalPHP\test\Person;
use FunctionalPHP\test\PersonComparator;

/**
 * Class used to test FunctionalPHP\collection\queue\PriorityQueue
 */
final class PriorityQueueTest extends TestCase {

	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::__construct
	 */
	public function testCreateEmptySortedSet() {

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals (0, $priorityQueue->size());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::__construct
	 */
	public function testCreateNotEmptySortedSetWithoutComparator() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue1->add ($person3);
		$priorityQueue1->add ($person1);
		$priorityQueue1->add ($person2);

		$priorityQueue2 = new PriorityQueue ($priorityQueue1);
		$this->assertFalse ($priorityQueue2->isEmpty());
		$this->assertEquals ($priorityQueue1->size(), $priorityQueue2->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue1, array ($person1, $person2, $person3));
		$this->checksOrdination ($priorityQueue2, array ($person1, $person2, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::__construct
	 */
	public function testCreateNotEmptySortedSetWithComparator() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue1->add ($person3);
		$priorityQueue1->add ($person1);
		$priorityQueue1->add ($person2);

		// Uses a comparator that ordering in reverse order
		$priorityQueue2 = new PriorityQueue ($priorityQueue1, new PersonComparator());
		$this->assertFalse ($priorityQueue2->isEmpty());
		$this->assertEquals ($priorityQueue1->size(), $priorityQueue2->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue1, array ($person1, $person2, $person3));
		$this->checksOrdination ($priorityQueue2, array ($person3, $person2, $person1));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::add
	 */
	public function testAddElements() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());

		$this->assertTrue ($priorityQueue->add ($person1));

		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->contains ($person1));

		// Adds the "rest of persons"
		$this->assertTrue ($priorityQueue->add ($person3));
		$this->assertTrue ($priorityQueue->add ($person2));
		$this->assertEquals (3, $priorityQueue->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue, array ($person1, $person2, $person3));

		// Permits duplicate elements
		$this->assertTrue ($priorityQueue->add ($person1));
		$this->assertTrue ($priorityQueue->add ($person3));
		$this->assertEquals (5, $priorityQueue->size());

		$this->checksOrdination ($priorityQueue, array ($person1, $person1, $person2
				                                       ,$person3, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::addAll
	 */
	public function testAddAllElementsOfEmptyCollection() {

		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($priorityQueue->addAll (new PriorityQueue()));
		$this->assertTrue ($priorityQueue->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::addAll
	 */
	public function testAddAllElementsWithPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue1 = new PriorityQueue();
		$this->assertTrue ($priorityQueue1->isEmpty());

		$priorityQueue2 = new PriorityQueue();
		$priorityQueue2->add ($person2);
		$priorityQueue2->add ($person1);
		$priorityQueue2->add ($person3);
		$this->assertEquals (3, $priorityQueue2->size());

		// Adds elements of $priorityQueue2 inside $priorityQueue1
		$this->assertTrue ($priorityQueue1->addAll ($priorityQueue2));
		$this->assertFalse ($priorityQueue1->isEmpty());
		$this->assertEquals ($priorityQueue1->size(), $priorityQueue2->size());

		foreach ($priorityQueue1->iterator() as $element)
			$this->assertTrue ($priorityQueue2->contains ($element));

		// Adds again all elements of $priorityQueue2 inside $priorityQueue1
		$this->assertTrue ($priorityQueue1->addAll ($priorityQueue2));
		$this->assertEquals ($priorityQueue2->size() * 2, $priorityQueue1->size());

		foreach ($priorityQueue2->iterator() as $element)
			$this->assertTrue ($priorityQueue1->contains ($element));

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue2, array ($person1, $person2, $person3));
		$this->checksOrdination ($priorityQueue1, array ($person1, $person1, $person2
				                                        ,$person2, $person3, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::addAll
	 */
	public function testAddAllElementsWithArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());

		$arrayList = new ArrayList();
		$arrayList->add ($person2);
		$arrayList->add ($person3);
		$arrayList->add ($person1);
		$this->assertEquals (3, $arrayList->size());

		// Adds elements of $arrayList inside $priorityQueue
		$this->assertTrue ($priorityQueue->addAll ($arrayList));
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals ($arrayList->size(), $priorityQueue->size());

		foreach ($arrayList->iterator() as $element)
			$this->assertTrue ($priorityQueue->contains ($element));

		// Adds again all elements of $arrayList inside $priorityQueue
		$this->assertTrue ($priorityQueue->addAll ($arrayList));
		$this->assertEquals ($arrayList->size() * 2, $priorityQueue->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue, array ($person1, $person1, $person2
				                                       ,$person2, $person3, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::addAll
	 */
	public function testAddAllElementsWithHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue (new PriorityQueue(), new PersonComparator());
		$this->assertTrue ($priorityQueue->isEmpty());

		$hashSet = new HashSet();
		$hashSet->add ($person3);
		$hashSet->add ($person2);
		$hashSet->add ($person1);
		$this->assertEquals (3, $hashSet->size());

		// Adds elements of $hashSet inside $priorityQueue
		$this->assertTrue ($priorityQueue->addAll ($hashSet));
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals ($hashSet->size(), $priorityQueue->size());

		foreach ($hashSet->iterator() as $element)
			$this->assertTrue ($priorityQueue->contains ($element));

		// Adds again all elements of $hashSet inside $priorityQueue
		$this->assertTrue ($priorityQueue->addAll ($hashSet));
		$this->assertEquals ($hashSet->size() * 2, $priorityQueue->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue, array ($person3, $person3, $person2
				                                       ,$person2, $person1, $person1));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::addAll
	 */
	public function testAddAllElementsWithSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());

		$sortedSet = new SortedSet();
		$sortedSet->add ($person1);
		$sortedSet->add ($person2);
		$sortedSet->add ($person3);
		$this->assertEquals (3, $sortedSet->size());

		// Adds elements of $sortedSet inside $priorityQueue
		$this->assertTrue ($priorityQueue->addAll ($sortedSet));
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals ($sortedSet->size(), $priorityQueue->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertTrue ($priorityQueue->contains ($element));

		// Adds again all elements of $sortedSet inside $priorityQueue
		$this->assertTrue ($priorityQueue->addAll ($sortedSet));
		$this->assertEquals ($sortedSet->size() * 2, $priorityQueue->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue, array ($person1, $person1, $person2
				                                       ,$person2, $person3, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::clear
	 */
	public function testClearPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals (0, $priorityQueue->size());

		$priorityQueue->add ($person1);
		$priorityQueue->add ($person3);
		$priorityQueue->add ($person2);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (3, $priorityQueue->size());

		$priorityQueue->clear();
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals (0, $priorityQueue->size());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::contains
	 */
	public function testCheckElementsContainedInPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($priorityQueue->contains ($person1));
		$this->assertFalse ($priorityQueue->contains ($person2));
		$this->assertFalse ($priorityQueue->contains ($person3));

		$priorityQueue->add ($person1);
		$this->assertTrue ($priorityQueue->contains ($person1));
		$this->assertFalse ($priorityQueue->contains ($person2));
		$this->assertFalse ($priorityQueue->contains ($person3));

		$priorityQueue->add ($person2);
		$this->assertTrue ($priorityQueue->contains ($person1));
		$this->assertTrue ($priorityQueue->contains ($person2));
		$this->assertFalse ($priorityQueue->contains ($person3));

		$priorityQueue->add ($person3);
		$this->assertTrue ($priorityQueue->contains ($person1));
		$this->assertTrue ($priorityQueue->contains ($person2));
		$this->assertTrue ($priorityQueue->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::containsAll
	 */
	public function testCheckElementsContainedInAGivenPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue2 = new PriorityQueue();
		$this->assertTrue ($priorityQueue1->containsAll ($priorityQueue2));

		$priorityQueue1->add ($person1);
		$priorityQueue2->add ($person1);
		$this->assertTrue ($priorityQueue1->containsAll ($priorityQueue2));
		$this->assertTrue ($priorityQueue2->containsAll ($priorityQueue1));

		$priorityQueue1->add ($person2);
		$this->assertTrue ($priorityQueue1->containsAll ($priorityQueue2));
		$this->assertFalse ($priorityQueue2->containsAll ($priorityQueue1));

		$priorityQueue2->add ($person3);
		$this->assertFalse ($priorityQueue1->containsAll ($priorityQueue2));
		$this->assertFalse ($priorityQueue2->containsAll ($priorityQueue1));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::containsAll
	 */
	public function testCheckElementsContainedInAGivenArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$arrayList = new ArrayList();
		$this->assertTrue ($priorityQueue->containsAll ($arrayList));

		$priorityQueue->add ($person1);
		$arrayList->add ($person2);
		$this->assertFalse ($priorityQueue->containsAll ($arrayList));

		$priorityQueue->add ($person2);
		$this->assertTrue ($priorityQueue->containsAll ($arrayList));

		$arrayList->add ($person3);
		$this->assertFalse ($priorityQueue->containsAll ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::containsAll
	 */
	public function testCheckElementsContainedInAGivenHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$hashSet = new HashSet();
		$this->assertTrue ($priorityQueue->containsAll ($hashSet));

		$priorityQueue->add ($person1);
		$hashSet->add ($person2);
		$this->assertFalse ($priorityQueue->containsAll ($hashSet));

		$priorityQueue->add ($person2);
		$this->assertTrue ($priorityQueue->containsAll ($hashSet));

		$hashSet->add ($person3);
		$this->assertFalse ($priorityQueue->containsAll ($hashSet));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::containsAll
	 */
	public function testCheckElementsContainedInAGivenSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$sortedSet = new SortedSet();
		$this->assertTrue ($priorityQueue->containsAll ($sortedSet));

		$priorityQueue->add ($person1);
		$sortedSet->add ($person2);
		$this->assertFalse ($priorityQueue->containsAll ($sortedSet));

		$priorityQueue->add ($person2);
		$this->assertTrue ($priorityQueue->containsAll ($sortedSet));

		$sortedSet->add ($person3);
		$this->assertFalse ($priorityQueue->containsAll ($sortedSet));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::equals
	 */
	public function testCheckEqualityWithPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue2 = new PriorityQueue();
		$this->assertTrue ($priorityQueue1->equals ($priorityQueue1));
		$this->assertTrue ($priorityQueue1->equals ($priorityQueue2));
		$this->assertTrue ($priorityQueue2->equals ($priorityQueue1));

		$priorityQueue1->add ($person1);
		$this->assertFalse ($priorityQueue1->equals ($priorityQueue2));
		$this->assertFalse ($priorityQueue2->equals ($priorityQueue1));

		$priorityQueue2->add ($person2);
		$this->assertFalse ($priorityQueue1->equals ($priorityQueue2));
		$this->assertFalse ($priorityQueue2->equals ($priorityQueue1));

		// The set have the same elements but added in different order
		$priorityQueue1->add ($person3);
		$priorityQueue1->add ($person2);
		$priorityQueue2->add ($person1);
		$priorityQueue2->add ($person3);
		$this->assertEquals (3, $priorityQueue1->size());
		$this->assertEquals (3, $priorityQueue2->size());

		$this->assertTrue ($priorityQueue1->equals ($priorityQueue2));
		$this->assertTrue ($priorityQueue2->equals ($priorityQueue1));

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue1, array ($person1, $person2, $person3));
		$this->checksOrdination ($priorityQueue2, array ($person1, $person2, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::equals
	 */
	public function testCheckEqualityWithArrayList() {

		$person = new Person ("John", 18, TRUE);

		$priorityQueue = new PriorityQueue();
		$arrayList = new ArrayList();
		$this->assertFalse ($priorityQueue->equals ($arrayList));

		$priorityQueue->add ($person);
		$this->assertFalse ($priorityQueue->equals ($arrayList));

		$arrayList->add ($person);
		$this->assertFalse ($priorityQueue->equals ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::equals
	 */
	public function testCheckEqualityWithHashSet() {

		$person = new Person ("John", 18, TRUE);

		$priorityQueue = new PriorityQueue();
		$hashSet = new HashSet();
		$this->assertFalse ($priorityQueue->equals ($hashSet));

		$priorityQueue->add ($person);
		$this->assertFalse ($priorityQueue->equals ($hashSet));

		$hashSet->add ($person);
		$this->assertFalse ($priorityQueue->equals ($hashSet));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::equals
	 */
	public function testCheckEqualityWithSortedSet() {

		$person = new Person ("John", 18, TRUE);

		$priorityQueue = new PriorityQueue();
		$sortedSet = new SortedSet();
		$this->assertFalse ($priorityQueue->equals ($sortedSet));

		$priorityQueue->add ($person);
		$this->assertFalse ($priorityQueue->equals ($sortedSet));

		$sortedSet->add ($person);
		$this->assertFalse ($priorityQueue->equals ($sortedSet));
	}



	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithMoreThanOneParameter() {

		$priorityQueue = new PriorityQueue();

		$priorityQueue->forEach (function (int $p1, string $p2) {
			                        $p1 += 1;
			                        $p2 .= "_test";
		                         });
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithParameterTypeDifferentOfObject() {

		$priorityQueue = new PriorityQueue();

		$priorityQueue->forEach (function (int $p1) {
			                        $p1 += 1;
		                         });
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithInvalidReturnedType() {

		$priorityQueue = new PriorityQueue();
		$priorityQueue->add (new Person ("John", 18, TRUE));

		$priorityQueue->forEach (function (Person $person) : int {
			                        $person->age *= 2;
		                         });
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::forEach
	 */
	public function testForEachOfValidClosureFunction() {

		$person1 = new Person ("John", 18, TRUE);
		$person1Clone = new Person ("John", 18, TRUE);

		$person2 = new Person ("Sara", 25, FALSE);
		$person2Clone = new Person ("Sara", 25, FALSE);

		$person3 = new Person ("Mary", 20, FALSE);
		$person3Clone = new Person ("Mary", 20, FALSE);

		$priorityQueueOriginal = new PriorityQueue();
		$priorityQueueOriginal->add ($person1);
		$priorityQueueOriginal->add ($person2);
		$priorityQueueOriginal->add ($person3);

		$priorityQueueToModified = new PriorityQueue();
		$priorityQueueToModified->add ($person1Clone);
		$priorityQueueToModified->add ($person2Clone);
		$priorityQueueToModified->add ($person3Clone);

		$priorityQueueToModified->forEach (function (Person $person) {
			                                  $person->name .= "_test";
		                                   });

		$this->assertGreaterThan (0, $priorityQueueToModified->size());
		$this->assertEquals ($priorityQueueOriginal->size(), $priorityQueueToModified->size());

		// Checks that only has changed the value of name property
		$count = $priorityQueueOriginal->size();
		for ($i = 1; $i <= $count; $i++) {

			$headOfOriginal = $priorityQueueOriginal->poll()->get();
			$headOfModified = $priorityQueueToModified->poll()->get();

			$this->assertEquals ($headOfOriginal->name."_test", $headOfModified->name);
			$this->assertEquals ($headOfOriginal->age, $headOfModified->age);
			$this->assertEquals ($headOfOriginal->isMale, $headOfModified->isMale);
		}
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::hashCode
	 */
	public function testHashCodeOfPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertEquals (0, $priorityQueue->hashCode());

		$priorityQueue->add ($person1);
		$this->assertEquals ($person1->hashCode(), $priorityQueue->hashCode());

		$priorityQueue->add ($person2);
		$this->assertEquals ($person1->hashCode() + $person2->hashCode()
				            ,$priorityQueue->hashCode());

		$priorityQueue->add ($person3);
		$this->assertEquals ($person1->hashCode() + $person2->hashCode() + $person3->hashCode()
				            ,$priorityQueue->hashCode());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::isEmpty
	 */
	public function testEmptyPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals (0, $priorityQueue->size());

		$priorityQueue->add ($person1);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (1, $priorityQueue->size());

		$priorityQueue->add ($person2);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (2, $priorityQueue->size());

		$priorityQueue->add ($person3);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (3, $priorityQueue->size());

		// Permits duplicate elements
		$priorityQueue->add ($person2);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (4, $priorityQueue->size());

		// Removes every element
		$priorityQueue->remove ($person3);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (3, $priorityQueue->size());

		$priorityQueue->remove ($person2);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (2, $priorityQueue->size());

		$priorityQueue->remove ($person1);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (1, $priorityQueue->size());

		$priorityQueue->remove ($person2);
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals (0, $priorityQueue->size());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::iterator
	 */
	public function testIterateOverPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());

		foreach ($priorityQueue->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $person1
		$priorityQueue->add ($person1);
		$this->assertEquals (1, $priorityQueue->size());

		foreach ($priorityQueue->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Adds another persons
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person3);
		$priorityQueue->add ($person2);
		$this->assertEquals (4, $priorityQueue->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue, array ($person1, $person2, $person2
				                                       ,$person3));
		// Checks reverse comparator
		$priorityQueue = new PriorityQueue (new PriorityQueue(), new PersonComparator());
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person3);
		$priorityQueue->add ($person1);
		$this->assertEquals (4, $priorityQueue->size());

		$this->checksOrdination ($priorityQueue, array ($person3, $person2, $person1
				                                       ,$person1));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::peek
	 */
	public function testPeekUsingCompareToOfStoredObjects() {

		$person1 = new Person ("Mary", 20, FALSE);
		$person2 = new Person ("Sara", 25, FALSE);
		$person3 = new Person ("John", 18, TRUE);

		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($priorityQueue->peek()->isPresent());

		$priorityQueue->add ($person1);
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($person1, $headElement);

		$priorityQueue->add ($person2);
		$this->assertEquals (2, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($person1, $headElement);

		$priorityQueue->add ($person3);
		$this->assertEquals (3, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($person3, $headElement);

		// Permits duplicate elements
		$priorityQueue->add ($person2);
		$this->assertEquals (4, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($person3, $headElement);
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::peek
	 */
	public function testPeekUsingGivenComparator() {

		$person1 = new Person ("Mary", 20, FALSE);
		$person2 = new Person ("Sara", 25, FALSE);
		$person3 = new Person ("John", 18, TRUE);

		$priorityQueue = new PriorityQueue (new PriorityQueue(), new PersonComparator());
		$this->assertFalse ($priorityQueue->peek()->isPresent());

		$priorityQueue->add ($person3);
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($person3, $headElement);

		$priorityQueue->add ($person2);
		$this->assertEquals (2, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($person2, $headElement);

		$priorityQueue->add ($person1);
		$this->assertEquals (3, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($person2, $headElement);

		// Permits duplicate elements
		$priorityQueue->add ($person1);
		$this->assertEquals (4, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($person2, $headElement);
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::poll
	 */
	public function testPollUsingCompareToOfStoredObjects() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($priorityQueue->poll()->isPresent());

		$priorityQueue->add ($person3);
		$headElement = $priorityQueue->poll()->get();

		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals ($person3, $headElement);
		$this->assertFalse ($priorityQueue->poll()->isPresent());

		$priorityQueue->add ($person2);
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person3);
		$this->assertEquals (4, $priorityQueue->size());

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (3, $priorityQueue->size());
		$this->assertEquals ($person1, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (2, $priorityQueue->size());
		$this->assertEquals ($person1, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertEquals ($person2, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals ($person3, $headElement);

		$this->assertFalse ($priorityQueue->poll()->isPresent());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::poll
	 */
	public function testPollUsingGivenComparator() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue (new PriorityQueue(), new PersonComparator());
		$this->assertFalse ($priorityQueue->poll()->isPresent());

		$priorityQueue->add ($person3);
		$headElement = $priorityQueue->poll()->get();

		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals ($person3, $headElement);
		$this->assertFalse ($priorityQueue->poll()->isPresent());

		$priorityQueue->add ($person2);
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person3);
		$this->assertEquals (4, $priorityQueue->size());

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (3, $priorityQueue->size());
		$this->assertEquals ($person3, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (2, $priorityQueue->size());
		$this->assertEquals ($person2, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertEquals ($person1, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals ($person1, $headElement);

		$this->assertFalse ($priorityQueue->poll()->isPresent());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::remove
	 */
	public function testRemoveElements() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($priorityQueue->remove ($person1));
		$this->assertFalse ($priorityQueue->remove ($person2));

		$priorityQueue->add ($person1);
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person1);
		$this->assertEquals (3, $priorityQueue->size());

		$this->assertTrue ($priorityQueue->remove ($person1));
		$this->assertEquals (2, $priorityQueue->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue, array ($person1, $person2));

		$this->assertTrue ($priorityQueue->remove ($person2));
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->contains ($person1));

		$this->assertTrue ($priorityQueue->remove ($person1));
		$this->assertTrue ($priorityQueue->isEmpty());

		$priorityQueue->add ($person3);
		$priorityQueue->add ($person3);
		$this->checksOrdination ($priorityQueue, array ($person3, $person3));

		$this->assertFalse ($priorityQueue->remove ($person1));
		$this->assertFalse ($priorityQueue->remove ($person2));
		$this->assertTrue ($priorityQueue->remove ($person3));
		$this->assertEquals (1, $priorityQueue->size());

		// Removes the last occurence of $person3
		$this->assertTrue ($priorityQueue->remove ($person3));
		$this->assertEquals (0, $priorityQueue->size());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::removeAll
	 */
	public function testRemoveAllElementsOfGivenPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue2 = new PriorityQueue();
		$this->assertFalse ($priorityQueue1->removeAll ($priorityQueue2));

		$priorityQueue1->add ($person1);
		$priorityQueue1->add ($person3);
		$priorityQueue2->add ($person2);

		$this->assertFalse ($priorityQueue1->removeAll ($priorityQueue2));
		$this->assertEquals (2, $priorityQueue1->size());

		$this->assertFalse ($priorityQueue2->removeAll ($priorityQueue1));
		$this->assertEquals (1, $priorityQueue2->size());

		// Adds $person1 twice
		$priorityQueue1->add ($person1);
		$priorityQueue2->add ($person1);

		$this->assertEquals (3, $priorityQueue1->size());
		$this->assertEquals (2, $priorityQueue2->size());
		$this->checksOrdination ($priorityQueue1, array ($person1, $person1, $person3));
		$this->checksOrdination ($priorityQueue2, array ($person1, $person2));

		$this->assertTrue ($priorityQueue1->removeAll ($priorityQueue2));

		$this->assertEquals (1, $priorityQueue1->size());
		$this->assertEquals (2, $priorityQueue2->size());
		$this->assertEquals ($person3, $priorityQueue1->peek()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::removeAll
	 */
	public function testRemoveAllElementsOfGivenArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Mary", 20, FALSE);
    	$person3 = new Person ("Sara", 25, FALSE);

    	$priorityQueue = new PriorityQueue();
    	$arrayList = new ArrayList();
    	$this->assertFalse ($priorityQueue->removeAll ($arrayList));

    	$priorityQueue->add ($person1);
    	$priorityQueue->add ($person3);
    	$arrayList->add ($person2);

    	$this->assertFalse ($priorityQueue->removeAll ($arrayList));
    	$this->assertEquals (2, $priorityQueue->size());

    	// Adds $person1 twice
    	$priorityQueue->add ($person1);
    	$arrayList->add ($person1);

    	$this->assertEquals (3, $priorityQueue->size());
    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($priorityQueue, array ($person1, $person1, $person3));

    	$this->assertTrue ($priorityQueue->removeAll ($arrayList));
    	$this->assertEquals (1, $priorityQueue->size());
    	$this->assertEquals ($person3, $priorityQueue->peek()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::removeAll
	 */
	public function testRemoveAllElementsOfGivenHashSet() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Mary", 20, FALSE);
    	$person3 = new Person ("Sara", 25, FALSE);

    	$priorityQueue = new PriorityQueue();
    	$hashSet = new HashSet();
    	$this->assertFalse ($priorityQueue->removeAll ($hashSet));

    	$priorityQueue->add ($person1);
    	$priorityQueue->add ($person3);
    	$hashSet->add ($person2);

    	$this->assertFalse ($priorityQueue->removeAll ($hashSet));
    	$this->assertEquals (2, $priorityQueue->size());

    	// Adds $person1 twice
    	$priorityQueue->add ($person1);
    	$hashSet->add ($person1);

    	$this->assertEquals (3, $priorityQueue->size());
    	$this->assertEquals (2, $hashSet->size());
    	$this->checksOrdination ($priorityQueue, array ($person1, $person1, $person3));

    	$this->assertTrue ($priorityQueue->removeAll ($hashSet));
    	$this->assertEquals (1, $priorityQueue->size());
    	$this->assertEquals ($person3, $priorityQueue->peek()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::removeAll
	 */
	public function testRemoveAllElementsOfGivenSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$sortedSet = new SortedSet();
		$this->assertFalse ($priorityQueue->removeAll ($sortedSet));

		$priorityQueue->add ($person1);
		$priorityQueue->add ($person3);
		$sortedSet->add ($person2);

		$this->assertFalse ($priorityQueue->removeAll ($sortedSet));
		$this->assertEquals (2, $priorityQueue->size());

		// Adds $person1 twice
		$priorityQueue->add ($person1);
		$sortedSet->add ($person1);

		$this->assertEquals (3, $priorityQueue->size());
		$this->assertEquals (2, $sortedSet->size());
		$this->checksOrdination ($priorityQueue, array ($person1, $person1, $person3));

		$this->assertTrue ($priorityQueue->removeAll ($sortedSet));
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertEquals ($person3, $priorityQueue->peek()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::retainAll
	 */
	public function testRetainAllElementsOfGivenPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue2 = new PriorityQueue();
		$this->assertFalse ($priorityQueue1->retainAll ($priorityQueue2));

		// Retains all elements of an empty queue
		$priorityQueue1->add ($person1);
		$this->assertTrue ($priorityQueue1->retainAll ($priorityQueue2));
		$this->assertTrue ($priorityQueue1->isEmpty());

		// Both queues shared $person1
		$priorityQueue1->add ($person1);
		$priorityQueue1->add ($person2);
		$priorityQueue2->add ($person1);

		$this->assertEquals (2, $priorityQueue1->size());
		$this->assertEquals (1, $priorityQueue2->size());

		$this->assertTrue ($priorityQueue1->retainAll ($priorityQueue2));
		$this->assertEquals (1, $priorityQueue1->size());
		$this->assertTrue ($priorityQueue1->contains ($person1));

		// Both queues have the same elements
		$priorityQueue1->add ($person2);
		$priorityQueue2->add ($person2);

		$this->assertEquals (2, $priorityQueue1->size());
		$this->assertEquals (2, $priorityQueue2->size());
		$this->checksOrdination ($priorityQueue1, array ($person1, $person2));
		$this->checksOrdination ($priorityQueue2, array ($person1, $person2));

		$this->assertFalse ($priorityQueue1->retainAll ($priorityQueue2));

		$this->assertEquals (2, $priorityQueue1->size());
		$this->assertEquals (2, $priorityQueue2->size());
		$this->checksOrdination ($priorityQueue1, array ($person1, $person2));

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue1, array ($person1, $person2));
		$this->checksOrdination ($priorityQueue2, array ($person1, $person2));

		// Permits dulicate elements
		$priorityQueue1->add ($person2);
		$priorityQueue2->add ($person3);

    	$this->assertEquals (3, $priorityQueue1->size());
    	$this->assertEquals (3, $priorityQueue2->size());
    	$this->checksOrdination ($priorityQueue1, array ($person1, $person2, $person2));
    	$this->checksOrdination ($priorityQueue2, array ($person1, $person2, $person3));

		$this->assertFalse ($priorityQueue1->retainAll ($priorityQueue2));
		$this->assertEquals (3, $priorityQueue1->size());
		$this->checksOrdination ($priorityQueue1, array ($person1, $person2, $person2));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::retainAll
	 */
	public function testRetainAllElementsOfGivenArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$arrayList = new ArrayList();
		$this->assertFalse ($priorityQueue->retainAll ($arrayList));

		// Retains all elements of an empty list
		$priorityQueue->add ($person1);
		$this->assertTrue ($priorityQueue->retainAll ($arrayList));
		$this->assertTrue ($priorityQueue->isEmpty());

		// Both collections shared $person1
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person2);
		$arrayList->add ($person1);

		$this->assertEquals (2, $priorityQueue->size());
		$this->assertEquals (1, $arrayList->size());

		$this->assertTrue ($priorityQueue->retainAll ($arrayList));
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->contains ($person1));

		// Both collections have the same elements
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person3);
		$arrayList->add ($person2);
		$arrayList->add ($person3);

		$this->assertEquals (4, $priorityQueue->size());
		$this->assertEquals (3, $arrayList->size());
		$this->checksOrdination ($priorityQueue, array ($person1, $person2, $person2
				                                       ,$person3));

		$this->assertFalse ($priorityQueue->retainAll ($arrayList));
		$this->assertEquals (4, $priorityQueue->size());
		$this->checksOrdination ($priorityQueue, array ($person1, $person2, $person2
				                                       ,$person3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::retainAll
	 */
	public function testRetainAllElementsOfGivenHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$hashSet = new HashSet();
		$this->assertFalse ($priorityQueue->retainAll ($hashSet));

		// Retains all elements of an empty list
		$priorityQueue->add ($person1);
		$this->assertTrue ($priorityQueue->retainAll ($hashSet));
		$this->assertTrue ($priorityQueue->isEmpty());

		// Both collections shared $person1
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person2);
		$hashSet->add ($person1);

		$this->assertEquals (2, $priorityQueue->size());
		$this->assertEquals (1, $hashSet->size());

		$this->assertTrue ($priorityQueue->retainAll ($hashSet));
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->contains ($person1));

		// Both collections have the same elements
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person3);
		$hashSet->add ($person2);
		$hashSet->add ($person3);

		$this->assertEquals (4, $priorityQueue->size());
		$this->assertEquals (3, $hashSet->size());
		$this->checksOrdination ($priorityQueue, array ($person1, $person2, $person2
				                                       ,$person3));

		$this->assertFalse ($priorityQueue->retainAll ($hashSet));
		$this->assertEquals (4, $priorityQueue->size());
		$this->checksOrdination ($priorityQueue, array ($person1, $person2, $person2
				                                       ,$person3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::retainAll
	 */
	public function testRetainAllElementsOfGivenSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$sortedSet = new SortedSet();
		$this->assertFalse ($priorityQueue->retainAll ($sortedSet));

		// Retains all elements of an empty list
		$priorityQueue->add ($person1);
		$this->assertTrue ($priorityQueue->retainAll ($sortedSet));
		$this->assertTrue ($priorityQueue->isEmpty());

		// Both collections shared $person1
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person2);
		$sortedSet->add ($person1);

		$this->assertEquals (2, $priorityQueue->size());
		$this->assertEquals (1, $sortedSet->size());

		$this->assertTrue ($priorityQueue->retainAll ($sortedSet));
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->contains ($person1));

		// Both collections have the same elements
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person3);
		$sortedSet->add ($person2);
		$sortedSet->add ($person3);

		$this->assertEquals (4, $priorityQueue->size());
		$this->assertEquals (3, $sortedSet->size());
		$this->checksOrdination ($priorityQueue, array ($person1, $person2, $person2
				                                       ,$person3));

		$this->assertFalse ($priorityQueue->retainAll ($sortedSet));
		$this->assertEquals (4, $priorityQueue->size());
		$this->checksOrdination ($priorityQueue, array ($person1, $person2, $person2
				                                       ,$person3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::size
	 */
	public function testSizeOfPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertEquals (0, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->isEmpty());

		$priorityQueue->add ($person1);
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		$priorityQueue->add ($person2);
		$this->assertEquals (2, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		$priorityQueue->add ($person3);
		$this->assertEquals (3, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		// Permits duplicate elements
		$priorityQueue->add ($person2);
		$this->assertEquals (4, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		// Removes every element
		$priorityQueue->remove ($person2);
		$this->assertEquals (3, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		$priorityQueue->remove ($person1);
		$this->assertEquals (2, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		$priorityQueue->remove ($person2);
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		$priorityQueue->remove ($person3);
		$this->assertEquals (0, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::stream
	 */
	public function testStreamOfPriorityQueue() {

		// Empty PriorityQueue
		$priorityQueue = new PriorityQueue();
		$stream = $priorityQueue->stream();

		$this->assertNotNull ($stream);
		$this->assertEquals (0, $stream->count());
		$this->assertEmpty ($stream->toArray());

		// Not empty PriorityQueue
		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person3);

		$stream = $priorityQueue->stream();

		$this->assertNotNull ($stream);
		$this->assertEquals ($priorityQueue->size(), $stream->count());
		$this->assertNotEmpty ($stream->toArray());

		$contentOfStream = $stream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertTrue ($priorityQueue->contains ($contentOfStream[$i]));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::toArray
	 */
	public function testToArrayOfPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$priorityQueue = new PriorityQueue();

		$array = $priorityQueue->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (0, count ($array));

		$priorityQueue->add ($person2);
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person3);
		$priorityQueue->add ($person1);

		$array = $priorityQueue->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (4, count ($array));

		$this->assertEquals ($person1, $array[0]);
		$this->assertEquals ($person1, $array[1]);
		$this->assertEquals ($person2, $array[2]);
		$this->assertEquals ($person3, $array[3]);

		// Checks reverse ordination
		$priorityQueue = new PriorityQueue (new PriorityQueue(), new PersonComparator());

		$priorityQueue->add ($person2);
		$priorityQueue->add ($person1);
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person3);

		$array = $priorityQueue->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (4, count ($array));

		$this->assertEquals ($person3, $array[0]);
		$this->assertEquals ($person2, $array[1]);
		$this->assertEquals ($person2, $array[2]);
		$this->assertEquals ($person1, $array[3]);
	}


	/**
	 *    Checks if the elements stored in the given PriorityQueue are returned in the same order as
	 * the given $elementsInExpectedOrder.
	 *
	 * @param PriorityQueue $priorityQueue
	 *    PriorityQueue to check
	 * @param array $elementsInExpectedOrder
	 *    The expected result of the "returned elements" stored in the given queue
	 */
	private function checksOrdination (PriorityQueue $priorityQueue, array $elementsInExpectedOrder) {

		$this->assertEquals ($priorityQueue->size(), count ($elementsInExpectedOrder));
		$this->assertGreaterThan (0, $priorityQueue->size());

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			$this->assertEquals ($elementsInExpectedOrder[$i], $element);
			$i++;
		}
	}

}

?>