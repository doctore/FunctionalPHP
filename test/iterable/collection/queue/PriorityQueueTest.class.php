<?php

namespace FunctionalPHP\test\iterable\collection\queue;

require_once '../../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\queue\PriorityQueue;
use FunctionalPHP\iterable\collection\set\HashSet;
use FunctionalPHP\iterable\collection\set\SortedSet;
use FunctionalPHP\test\DummyObject;
use FunctionalPHP\test\DummyObjectComparator;

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

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue1->add ($dummyObject3);
		$priorityQueue1->add ($dummyObject1);
		$priorityQueue1->add ($dummyObject2);

		$priorityQueue2 = new PriorityQueue ($priorityQueue1);
		$this->assertFalse ($priorityQueue2->isEmpty());
		$this->assertEquals ($priorityQueue1->size(), $priorityQueue2->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue1, array ($dummyObject1, $dummyObject2, $dummyObject3));
		$this->checksOrdination ($priorityQueue2, array ($dummyObject1, $dummyObject2, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::__construct
	 */
	public function testCreateNotEmptySortedSetWithComparator() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue1->add ($dummyObject3);
		$priorityQueue1->add ($dummyObject1);
		$priorityQueue1->add ($dummyObject2);

		// Uses a comparator that ordering in reverse order
		$priorityQueue2 = new PriorityQueue ($priorityQueue1, new DummyObjectComparator());
		$this->assertFalse ($priorityQueue2->isEmpty());
		$this->assertEquals ($priorityQueue1->size(), $priorityQueue2->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue1, array ($dummyObject1, $dummyObject2, $dummyObject3));
		$this->checksOrdination ($priorityQueue2, array ($dummyObject3, $dummyObject2, $dummyObject1));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::add
	 */
	public function testAddElements() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());

		$this->assertTrue ($priorityQueue->add ($dummyObject1));

		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->contains ($dummyObject1));

		// Adds the "rest of dummy objects"
		$this->assertTrue ($priorityQueue->add ($dummyObject3));
		$this->assertTrue ($priorityQueue->add ($dummyObject2));
		$this->assertEquals (3, $priorityQueue->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject2, $dummyObject3));

		// Permits duplicate elements
		$this->assertTrue ($priorityQueue->add ($dummyObject1));
		$this->assertTrue ($priorityQueue->add ($dummyObject3));
		$this->assertEquals (5, $priorityQueue->size());

		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject1, $dummyObject2
				                                       ,$dummyObject3, $dummyObject3));
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

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue1 = new PriorityQueue();
		$this->assertTrue ($priorityQueue1->isEmpty());

		$priorityQueue2 = new PriorityQueue();
		$priorityQueue2->add ($dummyObject2);
		$priorityQueue2->add ($dummyObject1);
		$priorityQueue2->add ($dummyObject3);
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
		$this->checksOrdination ($priorityQueue2, array ($dummyObject1, $dummyObject2, $dummyObject3));
		$this->checksOrdination ($priorityQueue1, array ($dummyObject1, $dummyObject1, $dummyObject2
				                                        ,$dummyObject2, $dummyObject3, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::addAll
	 */
	public function testAddAllElementsWithArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());

		$arrayList = new ArrayList();
		$arrayList->add ($dummyObject2);
		$arrayList->add ($dummyObject3);
		$arrayList->add ($dummyObject1);
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
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject1, $dummyObject2
				                                       ,$dummyObject2, $dummyObject3, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::addAll
	 */
	public function testAddAllElementsWithHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue (new PriorityQueue(), new DummyObjectComparator());
		$this->assertTrue ($priorityQueue->isEmpty());

		$hashSet = new HashSet();
		$hashSet->add ($dummyObject3);
		$hashSet->add ($dummyObject2);
		$hashSet->add ($dummyObject1);
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
		$this->checksOrdination ($priorityQueue, array ($dummyObject3, $dummyObject3, $dummyObject2
				                                       ,$dummyObject2, $dummyObject1, $dummyObject1));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::addAll
	 */
	public function testAddAllElementsWithSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());

		$sortedSet = new SortedSet();
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject3);
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
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject1, $dummyObject2
				                                       ,$dummyObject2, $dummyObject3, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::clear
	 */
	public function testClearPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals (0, $priorityQueue->size());

		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject3);
		$priorityQueue->add ($dummyObject2);
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

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($priorityQueue->contains ($dummyObject1));
		$this->assertFalse ($priorityQueue->contains ($dummyObject2));
		$this->assertFalse ($priorityQueue->contains ($dummyObject3));

		$priorityQueue->add ($dummyObject1);
		$this->assertTrue ($priorityQueue->contains ($dummyObject1));
		$this->assertFalse ($priorityQueue->contains ($dummyObject2));
		$this->assertFalse ($priorityQueue->contains ($dummyObject3));

		$priorityQueue->add ($dummyObject2);
		$this->assertTrue ($priorityQueue->contains ($dummyObject1));
		$this->assertTrue ($priorityQueue->contains ($dummyObject2));
		$this->assertFalse ($priorityQueue->contains ($dummyObject3));

		$priorityQueue->add ($dummyObject3);
		$this->assertTrue ($priorityQueue->contains ($dummyObject1));
		$this->assertTrue ($priorityQueue->contains ($dummyObject2));
		$this->assertTrue ($priorityQueue->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::containsAll
	 */
	public function testCheckElementsContainedInAGivenPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue2 = new PriorityQueue();
		$this->assertTrue ($priorityQueue1->containsAll ($priorityQueue2));

		$priorityQueue1->add ($dummyObject1);
		$priorityQueue2->add ($dummyObject1);
		$this->assertTrue ($priorityQueue1->containsAll ($priorityQueue2));
		$this->assertTrue ($priorityQueue2->containsAll ($priorityQueue1));

		$priorityQueue1->add ($dummyObject2);
		$this->assertTrue ($priorityQueue1->containsAll ($priorityQueue2));
		$this->assertFalse ($priorityQueue2->containsAll ($priorityQueue1));

		$priorityQueue2->add ($dummyObject3);
		$this->assertFalse ($priorityQueue1->containsAll ($priorityQueue2));
		$this->assertFalse ($priorityQueue2->containsAll ($priorityQueue1));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::containsAll
	 */
	public function testCheckElementsContainedInAGivenArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$arrayList = new ArrayList();
		$this->assertTrue ($priorityQueue->containsAll ($arrayList));

		$priorityQueue->add ($dummyObject1);
		$arrayList->add ($dummyObject2);
		$this->assertFalse ($priorityQueue->containsAll ($arrayList));

		$priorityQueue->add ($dummyObject2);
		$this->assertTrue ($priorityQueue->containsAll ($arrayList));

		$arrayList->add ($dummyObject3);
		$this->assertFalse ($priorityQueue->containsAll ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::containsAll
	 */
	public function testCheckElementsContainedInAGivenHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$hashSet = new HashSet();
		$this->assertTrue ($priorityQueue->containsAll ($hashSet));

		$priorityQueue->add ($dummyObject1);
		$hashSet->add ($dummyObject2);
		$this->assertFalse ($priorityQueue->containsAll ($hashSet));

		$priorityQueue->add ($dummyObject2);
		$this->assertTrue ($priorityQueue->containsAll ($hashSet));

		$hashSet->add ($dummyObject3);
		$this->assertFalse ($priorityQueue->containsAll ($hashSet));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::containsAll
	 */
	public function testCheckElementsContainedInAGivenSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$sortedSet = new SortedSet();
		$this->assertTrue ($priorityQueue->containsAll ($sortedSet));

		$priorityQueue->add ($dummyObject1);
		$sortedSet->add ($dummyObject2);
		$this->assertFalse ($priorityQueue->containsAll ($sortedSet));

		$priorityQueue->add ($dummyObject2);
		$this->assertTrue ($priorityQueue->containsAll ($sortedSet));

		$sortedSet->add ($dummyObject3);
		$this->assertFalse ($priorityQueue->containsAll ($sortedSet));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::equals
	 */
	public function testCheckEqualityWithPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue2 = new PriorityQueue();
		$this->assertTrue ($priorityQueue1->equals ($priorityQueue1));
		$this->assertTrue ($priorityQueue1->equals ($priorityQueue2));
		$this->assertTrue ($priorityQueue2->equals ($priorityQueue1));

		$priorityQueue1->add ($dummyObject1);
		$this->assertFalse ($priorityQueue1->equals ($priorityQueue2));
		$this->assertFalse ($priorityQueue2->equals ($priorityQueue1));

		$priorityQueue2->add ($dummyObject2);
		$this->assertFalse ($priorityQueue1->equals ($priorityQueue2));
		$this->assertFalse ($priorityQueue2->equals ($priorityQueue1));

		// The set have the same elements but added in different order
		$priorityQueue1->add ($dummyObject3);
		$priorityQueue1->add ($dummyObject2);
		$priorityQueue2->add ($dummyObject1);
		$priorityQueue2->add ($dummyObject3);
		$this->assertEquals (3, $priorityQueue1->size());
		$this->assertEquals (3, $priorityQueue2->size());

		$this->assertTrue ($priorityQueue1->equals ($priorityQueue2));
		$this->assertTrue ($priorityQueue2->equals ($priorityQueue1));

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue1, array ($dummyObject1, $dummyObject2, $dummyObject3));
		$this->checksOrdination ($priorityQueue2, array ($dummyObject1, $dummyObject2, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::equals
	 */
	public function testCheckEqualityWithArrayList() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$priorityQueue = new PriorityQueue();
		$arrayList = new ArrayList();
		$this->assertFalse ($priorityQueue->equals ($arrayList));

		$priorityQueue->add ($dummyObject);
		$this->assertFalse ($priorityQueue->equals ($arrayList));

		$arrayList->add ($dummyObject);
		$this->assertFalse ($priorityQueue->equals ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::equals
	 */
	public function testCheckEqualityWithHashSet() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$priorityQueue = new PriorityQueue();
		$hashSet = new HashSet();
		$this->assertFalse ($priorityQueue->equals ($hashSet));

		$priorityQueue->add ($dummyObject);
		$this->assertFalse ($priorityQueue->equals ($hashSet));

		$hashSet->add ($dummyObject);
		$this->assertFalse ($priorityQueue->equals ($hashSet));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::equals
	 */
	public function testCheckEqualityWithSortedSet() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$priorityQueue = new PriorityQueue();
		$sortedSet = new SortedSet();
		$this->assertFalse ($priorityQueue->equals ($sortedSet));

		$priorityQueue->add ($dummyObject);
		$this->assertFalse ($priorityQueue->equals ($sortedSet));

		$sortedSet->add ($dummyObject);
		$this->assertFalse ($priorityQueue->equals ($sortedSet));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::hashCode
	 */
	public function testHashCodeOfPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertEquals (0, $priorityQueue->hashCode());

		$priorityQueue->add ($dummyObject1);
		$this->assertEquals ($dummyObject1->hashCode(), $priorityQueue->hashCode());

		$priorityQueue->add ($dummyObject2);
		$this->assertEquals ($dummyObject1->hashCode() + $dummyObject2->hashCode()
				            ,$priorityQueue->hashCode());

		$priorityQueue->add ($dummyObject3);
		$this->assertEquals ($dummyObject1->hashCode() + $dummyObject2->hashCode() + $dummyObject3->hashCode()
				            ,$priorityQueue->hashCode());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::isEmpty
	 */
	public function testEmptyPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals (0, $priorityQueue->size());

		$priorityQueue->add ($dummyObject1);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (1, $priorityQueue->size());

		$priorityQueue->add ($dummyObject2);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (2, $priorityQueue->size());

		$priorityQueue->add ($dummyObject3);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (3, $priorityQueue->size());

		// Permits duplicate elements
		$priorityQueue->add ($dummyObject2);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (4, $priorityQueue->size());

		// Removes every element
		$priorityQueue->remove ($dummyObject3);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (3, $priorityQueue->size());

		$priorityQueue->remove ($dummyObject2);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (2, $priorityQueue->size());

		$priorityQueue->remove ($dummyObject1);
		$this->assertFalse ($priorityQueue->isEmpty());
		$this->assertEquals (1, $priorityQueue->size());

		$priorityQueue->remove ($dummyObject2);
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals (0, $priorityQueue->size());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::iterator
	 */
	public function testIterateOverPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($priorityQueue->isEmpty());

		foreach ($priorityQueue->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $dummyObject1
		$priorityQueue->add ($dummyObject1);
		$this->assertEquals (1, $priorityQueue->size());

		foreach ($priorityQueue->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Adds another dummy objects
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject3);
		$priorityQueue->add ($dummyObject2);
		$this->assertEquals (4, $priorityQueue->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject2, $dummyObject2
				                                       ,$dummyObject3));
		// Checks reverse comparator
		$priorityQueue = new PriorityQueue (new PriorityQueue(), new DummyObjectComparator());
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject3);
		$priorityQueue->add ($dummyObject1);
		$this->assertEquals (4, $priorityQueue->size());

		$this->checksOrdination ($priorityQueue, array ($dummyObject3, $dummyObject2, $dummyObject1
				                                       ,$dummyObject1));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::peek
	 */
	public function testPeekUsingCompareToOfStoredObjects() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($priorityQueue->peek()->isPresent());

		$priorityQueue->add ($dummyObject1);
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($dummyObject1, $headElement);

		$priorityQueue->add ($dummyObject2);
		$this->assertEquals (2, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($dummyObject2, $headElement);

		$priorityQueue->add ($dummyObject3);
		$this->assertEquals (3, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($dummyObject3, $headElement);

		// Permits duplicate elements
		$priorityQueue->add ($dummyObject2);
		$this->assertEquals (4, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($dummyObject3, $headElement);
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::peek
	 */
	public function testPeekUsingGivenComparator() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue (new PriorityQueue(), new DummyObjectComparator());
		$this->assertFalse ($priorityQueue->peek()->isPresent());

		$priorityQueue->add ($dummyObject3);
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($dummyObject3, $headElement);

		$priorityQueue->add ($dummyObject2);
		$this->assertEquals (2, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($dummyObject2, $headElement);

		$priorityQueue->add ($dummyObject1);
		$this->assertEquals (3, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($dummyObject1, $headElement);

		// Permits duplicate elements
		$priorityQueue->add ($dummyObject2);
		$this->assertEquals (4, $priorityQueue->size());
		$headElement = $priorityQueue->peek()->get();
		$this->assertEquals ($dummyObject1, $headElement);
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::poll
	 */
	public function testPollUsingCompareToOfStoredObjects() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($priorityQueue->poll()->isPresent());

		$priorityQueue->add ($dummyObject3);
		$headElement = $priorityQueue->poll()->get();

		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals ($dummyObject3, $headElement);
		$this->assertFalse ($priorityQueue->poll()->isPresent());

		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject3);
		$this->assertEquals (4, $priorityQueue->size());

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (3, $priorityQueue->size());
		$this->assertEquals ($dummyObject3, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (2, $priorityQueue->size());
		$this->assertEquals ($dummyObject2, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertEquals ($dummyObject1, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals ($dummyObject1, $headElement);

		$this->assertFalse ($priorityQueue->poll()->isPresent());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::poll
	 */
	public function testPollUsingGivenComparator() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue (new PriorityQueue(), new DummyObjectComparator());
		$this->assertFalse ($priorityQueue->poll()->isPresent());

		$priorityQueue->add ($dummyObject3);
		$headElement = $priorityQueue->poll()->get();

		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals ($dummyObject3, $headElement);
		$this->assertFalse ($priorityQueue->poll()->isPresent());

		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject3);
		$this->assertEquals (4, $priorityQueue->size());

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (3, $priorityQueue->size());
		$this->assertEquals ($dummyObject1, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (2, $priorityQueue->size());
		$this->assertEquals ($dummyObject1, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertEquals ($dummyObject2, $headElement);

		$headElement = $priorityQueue->poll()->get();
		$this->assertTrue ($priorityQueue->isEmpty());
		$this->assertEquals ($dummyObject3, $headElement);

		$this->assertFalse ($priorityQueue->poll()->isPresent());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::remove
	 */
	public function testRemoveElements() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($priorityQueue->remove ($dummyObject1));
		$this->assertFalse ($priorityQueue->remove ($dummyObject2));

		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject1);
		$this->assertEquals (3, $priorityQueue->size());

		$this->assertTrue ($priorityQueue->remove ($dummyObject1));
		$this->assertEquals (2, $priorityQueue->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject2));

		$this->assertTrue ($priorityQueue->remove ($dummyObject2));
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->contains ($dummyObject1));

		$this->assertTrue ($priorityQueue->remove ($dummyObject1));
		$this->assertTrue ($priorityQueue->isEmpty());

		$priorityQueue->add ($dummyObject3);
		$priorityQueue->add ($dummyObject3);
		$this->checksOrdination ($priorityQueue, array ($dummyObject3, $dummyObject3));

		$this->assertFalse ($priorityQueue->remove ($dummyObject1));
		$this->assertFalse ($priorityQueue->remove ($dummyObject2));
		$this->assertTrue ($priorityQueue->remove ($dummyObject3));
		$this->assertEquals (1, $priorityQueue->size());

		// Removes the last occurence of $dummyObject3
		$this->assertTrue ($priorityQueue->remove ($dummyObject3));
		$this->assertEquals (0, $priorityQueue->size());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::removeAll
	 */
	public function testRemoveAllElementsOfGivenPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue2 = new PriorityQueue();
		$this->assertFalse ($priorityQueue1->removeAll ($priorityQueue2));

		$priorityQueue1->add ($dummyObject1);
		$priorityQueue1->add ($dummyObject3);
		$priorityQueue2->add ($dummyObject2);

		$this->assertFalse ($priorityQueue1->removeAll ($priorityQueue2));
		$this->assertEquals (2, $priorityQueue1->size());

		$this->assertFalse ($priorityQueue2->removeAll ($priorityQueue1));
		$this->assertEquals (1, $priorityQueue2->size());

		// Adds $dummyObject1 twice
		$priorityQueue1->add ($dummyObject1);
		$priorityQueue2->add ($dummyObject1);

		$this->assertEquals (3, $priorityQueue1->size());
		$this->assertEquals (2, $priorityQueue2->size());
		$this->checksOrdination ($priorityQueue1, array ($dummyObject1, $dummyObject1, $dummyObject3));
		$this->checksOrdination ($priorityQueue2, array ($dummyObject1, $dummyObject2));

		$this->assertTrue ($priorityQueue1->removeAll ($priorityQueue2));

		$this->assertEquals (1, $priorityQueue1->size());
		$this->assertEquals (2, $priorityQueue2->size());
		$this->assertEquals ($dummyObject3, $priorityQueue1->peek()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::removeAll
	 */
	public function testRemoveAllElementsOfGivenArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$priorityQueue = new PriorityQueue();
    	$arrayList = new ArrayList();
    	$this->assertFalse ($priorityQueue->removeAll ($arrayList));

    	$priorityQueue->add ($dummyObject1);
    	$priorityQueue->add ($dummyObject3);
    	$arrayList->add ($dummyObject2);

    	$this->assertFalse ($priorityQueue->removeAll ($arrayList));
    	$this->assertEquals (2, $priorityQueue->size());

    	// Adds $dummyObject1 twice
    	$priorityQueue->add ($dummyObject1);
    	$arrayList->add ($dummyObject1);

    	$this->assertEquals (3, $priorityQueue->size());
    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject1, $dummyObject3));

    	$this->assertTrue ($priorityQueue->removeAll ($arrayList));
    	$this->assertEquals (1, $priorityQueue->size());
    	$this->assertEquals ($dummyObject3, $priorityQueue->peek()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::removeAll
	 */
	public function testRemoveAllElementsOfGivenHashSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$priorityQueue = new PriorityQueue();
    	$hashSet = new HashSet();
    	$this->assertFalse ($priorityQueue->removeAll ($hashSet));

    	$priorityQueue->add ($dummyObject1);
    	$priorityQueue->add ($dummyObject3);
    	$hashSet->add ($dummyObject2);

    	$this->assertFalse ($priorityQueue->removeAll ($hashSet));
    	$this->assertEquals (2, $priorityQueue->size());

    	// Adds $dummyObject1 twice
    	$priorityQueue->add ($dummyObject1);
    	$hashSet->add ($dummyObject1);

    	$this->assertEquals (3, $priorityQueue->size());
    	$this->assertEquals (2, $hashSet->size());
    	$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject1, $dummyObject3));

    	$this->assertTrue ($priorityQueue->removeAll ($hashSet));
    	$this->assertEquals (1, $priorityQueue->size());
    	$this->assertEquals ($dummyObject3, $priorityQueue->peek()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::removeAll
	 */
	public function testRemoveAllElementsOfGivenSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$sortedSet = new SortedSet();
		$this->assertFalse ($priorityQueue->removeAll ($sortedSet));

		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject3);
		$sortedSet->add ($dummyObject2);

		$this->assertFalse ($priorityQueue->removeAll ($sortedSet));
		$this->assertEquals (2, $priorityQueue->size());

		// Adds $dummyObject1 twice
		$priorityQueue->add ($dummyObject1);
		$sortedSet->add ($dummyObject1);

		$this->assertEquals (3, $priorityQueue->size());
		$this->assertEquals (2, $sortedSet->size());
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject1, $dummyObject3));

		$this->assertTrue ($priorityQueue->removeAll ($sortedSet));
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertEquals ($dummyObject3, $priorityQueue->peek()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::retainAll
	 */
	public function testRetainAllElementsOfGivenPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue1 = new PriorityQueue();
		$priorityQueue2 = new PriorityQueue();
		$this->assertFalse ($priorityQueue1->retainAll ($priorityQueue2));

		// Retains all elements of an empty queue
		$priorityQueue1->add ($dummyObject1);
		$this->assertTrue ($priorityQueue1->retainAll ($priorityQueue2));
		$this->assertTrue ($priorityQueue1->isEmpty());

		// Both queues shared dummyObject1
		$priorityQueue1->add ($dummyObject1);
		$priorityQueue1->add ($dummyObject2);
		$priorityQueue2->add ($dummyObject1);

		$this->assertEquals (2, $priorityQueue1->size());
		$this->assertEquals (1, $priorityQueue2->size());

		$this->assertTrue ($priorityQueue1->retainAll ($priorityQueue2));
		$this->assertEquals (1, $priorityQueue1->size());
		$this->assertTrue ($priorityQueue1->contains ($dummyObject1));

		// Both queues have the same elements
		$priorityQueue1->add ($dummyObject2);
		$priorityQueue2->add ($dummyObject2);

		$this->assertEquals (2, $priorityQueue1->size());
		$this->assertEquals (2, $priorityQueue2->size());
		$this->checksOrdination ($priorityQueue1, array ($dummyObject1, $dummyObject2));
		$this->checksOrdination ($priorityQueue2, array ($dummyObject1, $dummyObject2));

		$this->assertFalse ($priorityQueue1->retainAll ($priorityQueue2));

		$this->assertEquals (2, $priorityQueue1->size());
		$this->assertEquals (2, $priorityQueue2->size());
		$this->checksOrdination ($priorityQueue1, array ($dummyObject1, $dummyObject2));

		// Checks the ordination of stored objects
		$this->checksOrdination ($priorityQueue1, array ($dummyObject1, $dummyObject2));
		$this->checksOrdination ($priorityQueue2, array ($dummyObject1, $dummyObject2));

		// Permits dulicate elements
		$priorityQueue1->add ($dummyObject2);
		$priorityQueue2->add ($dummyObject3);

    	$this->assertEquals (3, $priorityQueue1->size());
    	$this->assertEquals (3, $priorityQueue2->size());
    	$this->checksOrdination ($priorityQueue1, array ($dummyObject1, $dummyObject2, $dummyObject2));
    	$this->checksOrdination ($priorityQueue2, array ($dummyObject1, $dummyObject2, $dummyObject3));

		$this->assertFalse ($priorityQueue1->retainAll ($priorityQueue2));
		$this->assertEquals (3, $priorityQueue1->size());
		$this->checksOrdination ($priorityQueue1, array ($dummyObject1, $dummyObject2, $dummyObject2));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::retainAll
	 */
	public function testRetainAllElementsOfGivenArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$arrayList = new ArrayList();
		$this->assertFalse ($priorityQueue->retainAll ($arrayList));

		// Retains all elements of an empty list
		$priorityQueue->add ($dummyObject1);
		$this->assertTrue ($priorityQueue->retainAll ($arrayList));
		$this->assertTrue ($priorityQueue->isEmpty());

		// Both collections shared $dummyObject1
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject2);
		$arrayList->add ($dummyObject1);

		$this->assertEquals (2, $priorityQueue->size());
		$this->assertEquals (1, $arrayList->size());

		$this->assertTrue ($priorityQueue->retainAll ($arrayList));
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->contains ($dummyObject1));

		// Both collections have the same elements
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject3);
		$arrayList->add ($dummyObject2);
		$arrayList->add ($dummyObject3);

		$this->assertEquals (4, $priorityQueue->size());
		$this->assertEquals (3, $arrayList->size());
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject2, $dummyObject2
				                                       ,$dummyObject3));

		$this->assertFalse ($priorityQueue->retainAll ($arrayList));
		$this->assertEquals (4, $priorityQueue->size());
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject2, $dummyObject2
				                                       ,$dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::retainAll
	 */
	public function testRetainAllElementsOfGivenHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$hashSet = new HashSet();
		$this->assertFalse ($priorityQueue->retainAll ($hashSet));

		// Retains all elements of an empty list
		$priorityQueue->add ($dummyObject1);
		$this->assertTrue ($priorityQueue->retainAll ($hashSet));
		$this->assertTrue ($priorityQueue->isEmpty());

		// Both collections shared $dummyObject1
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject2);
		$hashSet->add ($dummyObject1);

		$this->assertEquals (2, $priorityQueue->size());
		$this->assertEquals (1, $hashSet->size());

		$this->assertTrue ($priorityQueue->retainAll ($hashSet));
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->contains ($dummyObject1));

		// Both collections have the same elements
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject3);
		$hashSet->add ($dummyObject2);
		$hashSet->add ($dummyObject3);

		$this->assertEquals (4, $priorityQueue->size());
		$this->assertEquals (3, $hashSet->size());
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject2, $dummyObject2
				                                       ,$dummyObject3));

		$this->assertFalse ($priorityQueue->retainAll ($hashSet));
		$this->assertEquals (4, $priorityQueue->size());
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject2, $dummyObject2
				                                       ,$dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::retainAll
	 */
	public function testRetainAllElementsOfGivenSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$sortedSet = new SortedSet();
		$this->assertFalse ($priorityQueue->retainAll ($sortedSet));

		// Retains all elements of an empty list
		$priorityQueue->add ($dummyObject1);
		$this->assertTrue ($priorityQueue->retainAll ($sortedSet));
		$this->assertTrue ($priorityQueue->isEmpty());

		// Both collections shared $dummyObject1
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject2);
		$sortedSet->add ($dummyObject1);

		$this->assertEquals (2, $priorityQueue->size());
		$this->assertEquals (1, $sortedSet->size());

		$this->assertTrue ($priorityQueue->retainAll ($sortedSet));
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->contains ($dummyObject1));

		// Both collections have the same elements
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject3);
		$sortedSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject3);

		$this->assertEquals (4, $priorityQueue->size());
		$this->assertEquals (3, $sortedSet->size());
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject2, $dummyObject2
				                                       ,$dummyObject3));

		$this->assertFalse ($priorityQueue->retainAll ($sortedSet));
		$this->assertEquals (4, $priorityQueue->size());
		$this->checksOrdination ($priorityQueue, array ($dummyObject1, $dummyObject2, $dummyObject2
				                                       ,$dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\queue\PriorityQueue::size
	 */
	public function testSizeOfPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$this->assertEquals (0, $priorityQueue->size());
		$this->assertTrue ($priorityQueue->isEmpty());

		$priorityQueue->add ($dummyObject1);
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		$priorityQueue->add ($dummyObject2);
		$this->assertEquals (2, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		$priorityQueue->add ($dummyObject3);
		$this->assertEquals (3, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		// Permits duplicate elements
		$priorityQueue->add ($dummyObject2);
		$this->assertEquals (4, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		// Removes every element
		$priorityQueue->remove ($dummyObject2);
		$this->assertEquals (3, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		$priorityQueue->remove ($dummyObject1);
		$this->assertEquals (2, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		$priorityQueue->remove ($dummyObject2);
		$this->assertEquals (1, $priorityQueue->size());
		$this->assertFalse ($priorityQueue->isEmpty());

		$priorityQueue->remove ($dummyObject3);
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
		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject3);

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

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$priorityQueue = new PriorityQueue();

		$array = $priorityQueue->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (0, count ($array));

		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject3);
		$priorityQueue->add ($dummyObject1);

		$array = $priorityQueue->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (4, count ($array));

		$this->assertEquals ($dummyObject1, $array[0]);
		$this->assertEquals ($dummyObject1, $array[1]);
		$this->assertEquals ($dummyObject2, $array[2]);
		$this->assertEquals ($dummyObject3, $array[3]);

		// Checks reverse ordination
		$priorityQueue = new PriorityQueue (new PriorityQueue(), new DummyObjectComparator());

		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject3);

		$array = $priorityQueue->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (4, count ($array));

		$this->assertEquals ($dummyObject3, $array[0]);
		$this->assertEquals ($dummyObject2, $array[1]);
		$this->assertEquals ($dummyObject2, $array[2]);
		$this->assertEquals ($dummyObject1, $array[3]);
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