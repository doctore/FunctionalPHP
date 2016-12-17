<?php

namespace FunctionalPHP\test\iterable\collection\set;

require_once '../../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\queue\PriorityQueue;
use FunctionalPHP\iterable\collection\set\HashSet;
use FunctionalPHP\iterable\collection\set\SortedSet;
use FunctionalPHP\test\DummyObject;
use FunctionalPHP\test\DummyObjectComparator;

/**
 * Class used to test FunctionalPHP\collection\set\SortedSet
 */
final class SortedSetTest extends TestCase {


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::__construct
	 */
	public function testCreateEmptySortedSet() {

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());
		$this->assertEquals (0, $sortedSet->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::__construct
	 */
	public function testCreateNotEmptySortedSetWithoutComparator() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet1->add ($dummyObject3);
		$sortedSet1->add ($dummyObject1);
		$sortedSet1->add ($dummyObject2);

		$sortedSet2 = new SortedSet ($sortedSet1);
		$this->assertFalse ($sortedSet2->isEmpty());
		$this->assertEquals ($sortedSet1->size(), $sortedSet2->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet1, array ($dummyObject1, $dummyObject2, $dummyObject3));
		$this->checksOrdination ($sortedSet2, array ($dummyObject1, $dummyObject2, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::__construct
	 */
	public function testCreateNotEmptySortedSetWithComparator() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet1->add ($dummyObject3);
		$sortedSet1->add ($dummyObject1);
		$sortedSet1->add ($dummyObject2);

		// Uses a comparator that ordering in reverse order
		$sortedSet2 = new SortedSet ($sortedSet1, new DummyObjectComparator());
		$this->assertFalse ($sortedSet2->isEmpty());
		$this->assertEquals ($sortedSet1->size(), $sortedSet2->size());

		foreach ($sortedSet1->iterator() as $element)
			$this->assertTrue ($sortedSet2->contains ($element));

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet1, array ($dummyObject1, $dummyObject2, $dummyObject3));
		$this->checksOrdination ($sortedSet2, array ($dummyObject3, $dummyObject2, $dummyObject1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::add
	 */
	public function testAddElements() {

		$dummyObject1      = new DummyObject (1, "a", FALSE);
		$dummyObject1Clone = new DummyObject (1, "a", TRUE);
		$dummyObject2      = new DummyObject (2, "b", FALSE);
		$dummyObject3      = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());

		$this->assertTrue ($sortedSet->add ($dummyObject1));

		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (1, $sortedSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject1));

		foreach ($sortedSet->iterator() as $element) {

			$this->assertEquals ($dummyObject1->intProperty, $element->intProperty);
			$this->assertEquals ($dummyObject1->stringProperty, $element->stringProperty);
			$this->assertEquals ($dummyObject1->boolProperty, $element->boolProperty);
		}

		// Adds an "equal object"
		$this->assertTrue ($sortedSet->add ($dummyObject1Clone));
		$this->assertEquals (1, $sortedSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject1));
		$this->assertTrue ($sortedSet->contains ($dummyObject1Clone));

		// Checks that dummyObject1 was replaced by dummyObject1Clone
		foreach ($sortedSet->iterator() as $element) {

			$this->assertEquals ($dummyObject1Clone->intProperty, $element->intProperty);
			$this->assertEquals ($dummyObject1Clone->stringProperty, $element->stringProperty);
			$this->assertEquals ($dummyObject1Clone->boolProperty, $element->boolProperty);
		}

		// Adds the "rest of dummy objects"
		$this->assertTrue ($sortedSet->add ($dummyObject3));
		$this->assertTrue ($sortedSet->add ($dummyObject2));

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet, array ($dummyObject1Clone, $dummyObject2, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::addAll
	 */
	public function testAddAllElementsOfEmptyCollection() {

		$sortedSet = new SortedSet();
		$this->assertFalse ($sortedSet->addAll (new SortedSet()));
		$this->assertTrue ($sortedSet->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::addAll
	 */
	public function testAddAllElementsWithSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet1 = new SortedSet();
		$this->assertTrue ($sortedSet1->isEmpty());

		$sortedSet2 = new SortedSet();
		$sortedSet2->add ($dummyObject2);
		$sortedSet2->add ($dummyObject1);
		$sortedSet2->add ($dummyObject3);
		$this->assertEquals (3, $sortedSet2->size());

		// Adds elements of $sortedSet2 inside $sortedSet1
		$this->assertTrue ($sortedSet1->addAll ($sortedSet2));
		$this->assertFalse ($sortedSet1->isEmpty());
		$this->assertEquals ($sortedSet1->size(), $sortedSet2->size());

		foreach ($sortedSet1->iterator() as $element)
			$this->assertTrue ($sortedSet2->contains ($element));

		// Adds again all elements of $sortedSet2 inside $sortedSet1
		$this->assertTrue ($sortedSet1->addAll ($sortedSet2));
		$this->assertEquals ($sortedSet1->size(), $sortedSet2->size());

		foreach ($sortedSet2->iterator() as $element)
			$this->assertTrue ($sortedSet1->contains ($element));

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet1, array ($dummyObject1, $dummyObject2, $dummyObject3));
		$this->checksOrdination ($sortedSet2, array ($dummyObject1, $dummyObject2, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::addAll
	 */
	public function testAddAllElementsWithArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());

		$arrayList = new ArrayList();
		$arrayList->add ($dummyObject2);
		$arrayList->add ($dummyObject3);
		$arrayList->add ($dummyObject1);
		$this->assertEquals (3, $arrayList->size());

		// Adds elements of $arrayList inside $sortedSet
		$this->assertTrue ($sortedSet->addAll ($arrayList));
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals ($arrayList->size(), $sortedSet->size());

		foreach ($arrayList->iterator() as $element)
			$this->assertTrue ($sortedSet->contains ($element));

		// Adds again all elements of $arrayList inside $sortedSet
		$this->assertTrue ($sortedSet->addAll ($arrayList));
		$this->assertEquals ($arrayList->size(), $sortedSet->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet, array ($dummyObject1, $dummyObject2, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::addAll
	 */
	public function testAddAllElementsWithHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet (new HashSet(), new DummyObjectComparator());
		$this->assertTrue ($sortedSet->isEmpty());

		$hashSet = new HashSet();
		$hashSet->add ($dummyObject3);
		$hashSet->add ($dummyObject2);
		$hashSet->add ($dummyObject1);
		$this->assertEquals (3, $hashSet->size());

		// Adds elements of $hashSet inside $sortedSet
		$this->assertTrue ($sortedSet->addAll ($hashSet));
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals ($hashSet->size(), $sortedSet->size());

		foreach ($hashSet->iterator() as $element)
			$this->assertTrue ($sortedSet->contains ($element));

		// Adds again all elements of $hashSet inside $sortedSet
		$this->assertTrue ($sortedSet->addAll ($hashSet));
		$this->assertEquals ($hashSet->size(), $sortedSet->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet, array ($dummyObject3, $dummyObject2, $dummyObject1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::addAll
	 */
	public function testAddAllElementsWithPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet (new HashSet(), new DummyObjectComparator());
		$this->assertTrue ($sortedSet->isEmpty());

		$priorityQueue = new PriorityQueue();
		$priorityQueue->add ($dummyObject3);
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject1);
		$this->assertEquals (3, $priorityQueue->size());

		// Adds elements of $priorityQueue inside $sortedSet
		$this->assertTrue ($sortedSet->addAll ($priorityQueue));
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals ($priorityQueue->size(), $sortedSet->size());

		foreach ($priorityQueue->iterator() as $element)
			$this->assertTrue ($sortedSet->contains ($element));

		// Adds again all elements of $priorityQueue inside $sortedSet
		$this->assertTrue ($sortedSet->addAll ($priorityQueue));
		$this->assertEquals ($priorityQueue->size(), $sortedSet->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet, array ($dummyObject3, $dummyObject2, $dummyObject1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::clear
	 */
	public function testClearSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());
		$this->assertEquals (0, $sortedSet->size());

		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject3);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (3, $sortedSet->size());

		$sortedSet->clear();
		$this->assertTrue ($sortedSet->isEmpty());
		$this->assertEquals (0, $sortedSet->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::contains
	 */
	public function testCheckElementsContainedInSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$this->assertFalse ($sortedSet->contains ($dummyObject1));
		$this->assertFalse ($sortedSet->contains ($dummyObject2));
		$this->assertFalse ($sortedSet->contains ($dummyObject3));

		$sortedSet->add ($dummyObject1);
		$this->assertTrue ($sortedSet->contains ($dummyObject1));
		$this->assertFalse ($sortedSet->contains ($dummyObject2));
		$this->assertFalse ($sortedSet->contains ($dummyObject3));

		$sortedSet->add ($dummyObject2);
		$this->assertTrue ($sortedSet->contains ($dummyObject1));
		$this->assertTrue ($sortedSet->contains ($dummyObject2));
		$this->assertFalse ($sortedSet->contains ($dummyObject3));

		$sortedSet->add ($dummyObject3);
		$this->assertTrue ($sortedSet->contains ($dummyObject1));
		$this->assertTrue ($sortedSet->contains ($dummyObject2));
		$this->assertTrue ($sortedSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet2 = new SortedSet();
		$this->assertTrue ($sortedSet1->containsAll ($sortedSet2));

		$sortedSet1->add ($dummyObject1);
		$sortedSet2->add ($dummyObject1);
		$this->assertTrue ($sortedSet1->containsAll ($sortedSet2));
		$this->assertTrue ($sortedSet2->containsAll ($sortedSet1));

		$sortedSet1->add ($dummyObject2);
		$this->assertTrue ($sortedSet1->containsAll ($sortedSet2));
		$this->assertFalse ($sortedSet2->containsAll ($sortedSet1));

		$sortedSet2->add ($dummyObject3);
		$this->assertFalse ($sortedSet1->containsAll ($sortedSet2));
		$this->assertFalse ($sortedSet2->containsAll ($sortedSet1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$arrayList = new ArrayList();
		$this->assertTrue ($sortedSet->containsAll ($arrayList));

		$sortedSet->add ($dummyObject1);
		$arrayList->add ($dummyObject2);
		$this->assertFalse ($sortedSet->containsAll ($arrayList));

		$sortedSet->add ($dummyObject2);
		$this->assertTrue ($sortedSet->containsAll ($arrayList));

		$arrayList->add ($dummyObject3);
		$this->assertFalse ($sortedSet->containsAll ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$hashSet = new HashSet();
		$this->assertTrue ($sortedSet->containsAll ($hashSet));

		$sortedSet->add ($dummyObject1);
		$hashSet->add ($dummyObject2);
		$this->assertFalse ($sortedSet->containsAll ($hashSet));

		$sortedSet->add ($dummyObject2);
		$this->assertTrue ($sortedSet->containsAll ($hashSet));

		$hashSet->add ($dummyObject3);
		$this->assertFalse ($sortedSet->containsAll ($hashSet));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($sortedSet->containsAll ($priorityQueue));

		$sortedSet->add ($dummyObject1);
		$priorityQueue->add ($dummyObject2);
		$this->assertFalse ($sortedSet->containsAll ($priorityQueue));

		$sortedSet->add ($dummyObject2);
		$this->assertTrue ($sortedSet->containsAll ($priorityQueue));

		$priorityQueue->add ($dummyObject3);
		$this->assertFalse ($sortedSet->containsAll ($priorityQueue));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::equals
	 */
	public function testCheckEqualityWithSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet2 = new SortedSet();
		$this->assertTrue ($sortedSet1->equals ($sortedSet1));
		$this->assertTrue ($sortedSet1->equals ($sortedSet2));
		$this->assertTrue ($sortedSet2->equals ($sortedSet1));

		$sortedSet1->add ($dummyObject1);
		$this->assertFalse ($sortedSet1->equals ($sortedSet2));
		$this->assertFalse ($sortedSet2->equals ($sortedSet1));

		$sortedSet2->add ($dummyObject2);
		$this->assertFalse ($sortedSet1->equals ($sortedSet2));
		$this->assertFalse ($sortedSet2->equals ($sortedSet1));

		// The set have the same elements but added in different order
		$sortedSet1->add ($dummyObject3);
		$sortedSet1->add ($dummyObject2);
		$sortedSet2->add ($dummyObject1);
		$sortedSet2->add ($dummyObject3);
		$this->assertEquals (3, $sortedSet1->size());
		$this->assertEquals (3, $sortedSet2->size());

		$this->assertTrue ($sortedSet1->equals ($sortedSet2));
		$this->assertTrue ($sortedSet2->equals ($sortedSet1));

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet1, array ($dummyObject1, $dummyObject2, $dummyObject3));
		$this->checksOrdination ($sortedSet2, array ($dummyObject1, $dummyObject2, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::equals
	 */
	public function testCheckEqualityWithArrayList() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$sortedSet = new SortedSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($sortedSet->equals ($arrayList));

		$sortedSet->add ($dummyObject);
		$this->assertFalse ($sortedSet->equals ($arrayList));

		$arrayList->add ($dummyObject);
		$this->assertFalse ($sortedSet->equals ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::equals
	 */
	public function testCheckEqualityWithHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$hashSet = new HashSet();
		$this->assertTrue ($sortedSet->equals ($hashSet));
		$this->assertTrue ($hashSet->equals ($sortedSet));

		$sortedSet->add ($dummyObject1);
		$this->assertFalse ($sortedSet->equals ($hashSet));
		$this->assertFalse ($hashSet->equals ($sortedSet));

		$hashSet->add ($dummyObject1);
		$this->assertTrue ($sortedSet->equals ($hashSet));
		$this->assertTrue ($hashSet->equals ($sortedSet));

		foreach ($sortedSet->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));

		foreach ($hashSet->iterator() as $element)
			$this->assertTrue ($sortedSet->contains ($element));

		// Adds $dummyObject2
		$sortedSet->add ($dummyObject2);
		$this->assertFalse ($sortedSet->equals ($hashSet));
		$this->assertFalse ($hashSet->equals ($sortedSet));

		$hashSet->add ($dummyObject2);
		$this->assertTrue ($sortedSet->equals ($hashSet));
		$this->assertTrue ($hashSet->equals ($sortedSet));

		// Adds $dummyObject3
		$sortedSet->add ($dummyObject3);
		$this->assertFalse ($sortedSet->equals ($hashSet));
		$this->assertFalse ($hashSet->equals ($sortedSet));

		$hashSet->add ($dummyObject3);
		$this->assertTrue ($sortedSet->equals ($hashSet));
		$this->assertTrue ($hashSet->equals ($sortedSet));

		foreach ($sortedSet->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));

		foreach ($hashSet->iterator() as $element)
			$this->assertTrue ($sortedSet->contains ($element));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::equals
	 */
	public function testCheckEqualityWithPriorityQueue() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$sortedSet = new SortedSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($sortedSet->equals ($priorityQueue));

		$sortedSet->add ($dummyObject);
		$this->assertFalse ($sortedSet->equals ($priorityQueue));

		$priorityQueue->add ($dummyObject);
		$this->assertFalse ($sortedSet->equals ($priorityQueue));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::first
	 */
	public function testFirstElementOfSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$this->assertFalse ($sortedSet->first()->isPresent());

		$sortedSet->add ($dummyObject3);
		$this->assertEquals (1, $sortedSet->size());
		$this->assertEquals ($dummyObject3, $sortedSet->first()->get());

		$sortedSet->add ($dummyObject1);
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals ($dummyObject1, $sortedSet->first()->get());

		$sortedSet->add ($dummyObject2);
		$this->assertEquals (3, $sortedSet->size());
		$this->assertEquals ($dummyObject1, $sortedSet->first()->get());

		// Checks reverse ordination
		$sortedSet = new SortedSet (new SortedSet(), new DummyObjectComparator());

		$sortedSet->add ($dummyObject1);
		$this->assertEquals (1, $sortedSet->size());
		$this->assertEquals ($dummyObject1, $sortedSet->first()->get());

		$sortedSet->add ($dummyObject3);
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals ($dummyObject3, $sortedSet->first()->get());

		$sortedSet->add ($dummyObject2);
		$this->assertEquals (3, $sortedSet->size());
		$this->assertEquals ($dummyObject3, $sortedSet->first()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::hashCode
	 */
	public function testHashCodeOfSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$this->assertEquals (0, $sortedSet->hashCode());

		$sortedSet->add ($dummyObject1);
		$this->assertEquals ($dummyObject1->hashCode(), $sortedSet->hashCode());

		$sortedSet->add ($dummyObject2);
		$this->assertEquals ($dummyObject1->hashCode() + $dummyObject2->hashCode()
				            ,$sortedSet->hashCode());

		$sortedSet->add ($dummyObject3);
		$this->assertEquals ($dummyObject1->hashCode() + $dummyObject2->hashCode() + $dummyObject3->hashCode()
				            ,$sortedSet->hashCode());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::headSet
	 */
	public function testHeadSetUsingCompareToOfStoredObjects() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();

		$headSortedSet = $sortedSet->headSet ($dummyObject1);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject1, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject2);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject2, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject3);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject3, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		// Adds $dummyObject2 in the "origin set"
		$sortedSet->add ($dummyObject2);

		$headSortedSet = $sortedSet->headSet ($dummyObject1);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject1, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject2);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject2, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($dummyObject2));

		$headSortedSet = $sortedSet->headSet ($dummyObject3);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($dummyObject2));

		$headSortedSet = $sortedSet->headSet ($dummyObject3, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($dummyObject2));

		// Adds "the rest" of dummy objects
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject3);

		$headSortedSet = $sortedSet->headSet ($dummyObject1);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject1, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($dummyObject1));

		$headSortedSet = $sortedSet->headSet ($dummyObject2);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($dummyObject1));

		$headSortedSet = $sortedSet->headSet ($dummyObject2, TRUE);
		$this->assertEquals (2, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($dummyObject1, $dummyObject2));

		$headSortedSet = $sortedSet->headSet ($dummyObject3);
		$this->assertEquals (2, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($dummyObject1, $dummyObject2));

		$headSortedSet = $sortedSet->headSet ($dummyObject3, TRUE);
		$this->assertEquals (3, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($dummyObject1, $dummyObject2, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::headSet
	 */
	public function testHeadSetUsingGivenComparator() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet (new SortedSet(), new DummyObjectComparator());

		$headSortedSet = $sortedSet->headSet ($dummyObject1);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject1, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject2);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject2, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject3);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject3, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		// Adds $dummyObject2 in the "origin set"
		$sortedSet->add ($dummyObject2);

		$headSortedSet = $sortedSet->headSet ($dummyObject1);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($dummyObject2));

		$headSortedSet = $sortedSet->headSet ($dummyObject1, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($dummyObject2));

		$headSortedSet = $sortedSet->headSet ($dummyObject2);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject2, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($dummyObject2));

		$headSortedSet = $sortedSet->headSet ($dummyObject3);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject3, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		// Adds "the rest" of dummy objects
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject3);

		$headSortedSet = $sortedSet->headSet ($dummyObject1);
		$this->assertEquals (2, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($dummyObject3, $dummyObject2));

		$headSortedSet = $sortedSet->headSet ($dummyObject1, TRUE);
		$this->assertEquals (3, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($dummyObject3, $dummyObject2, $dummyObject1));

		$headSortedSet = $sortedSet->headSet ($dummyObject2);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($dummyObject3));

		$headSortedSet = $sortedSet->headSet ($dummyObject2, TRUE);
		$this->assertEquals (2, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($dummyObject3, $dummyObject2));

		$headSortedSet = $sortedSet->headSet ($dummyObject3);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($dummyObject3, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::isEmpty
	 */
	public function testEmptySortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());
		$this->assertEquals (0, $sortedSet->size());

		$sortedSet->add ($dummyObject1);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (1, $sortedSet->size());

		$sortedSet->add ($dummyObject2);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (2, $sortedSet->size());

		$sortedSet->add ($dummyObject3);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (3, $sortedSet->size());

		// Does not permit duplicate elements
		$sortedSet->add ($dummyObject2);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (3, $sortedSet->size());

		// Removes every element
		$sortedSet->remove ($dummyObject3);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (2, $sortedSet->size());

		$sortedSet->remove ($dummyObject2);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (1, $sortedSet->size());

		$sortedSet->remove ($dummyObject1);
		$this->assertTrue ($sortedSet->isEmpty());
		$this->assertEquals (0, $sortedSet->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::iterator
	 */
	public function testIterateOverSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());

		foreach ($sortedSet->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $dummyObject1
		$sortedSet->add ($dummyObject1);
		$this->assertEquals (1, $sortedSet->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Adds another dummy objects
		$sortedSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject3);
		$this->assertEquals (3, $sortedSet->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet, array ($dummyObject1, $dummyObject2, $dummyObject3));

		// Checks reverse comparator
		$sortedSet = new SortedSet (new SortedSet(), new DummyObjectComparator());
		$sortedSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject3);

		$this->checksOrdination ($sortedSet, array ($dummyObject3, $dummyObject2, $dummyObject1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::last
	 */
	public function testLastElementOfSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$this->assertFalse ($sortedSet->last()->isPresent());

		$sortedSet->add ($dummyObject1);
		$this->assertEquals (1, $sortedSet->size());
		$this->assertEquals ($dummyObject1, $sortedSet->last()->get());

		$sortedSet->add ($dummyObject3);
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals ($dummyObject3, $sortedSet->last()->get());

		$sortedSet->add ($dummyObject2);
		$this->assertEquals (3, $sortedSet->size());
		$this->assertEquals ($dummyObject3, $sortedSet->last()->get());

		// Checks reverse ordination
		$sortedSet = new SortedSet (new SortedSet(), new DummyObjectComparator());

		$sortedSet->add ($dummyObject2);
		$this->assertEquals (1, $sortedSet->size());
		$this->assertEquals ($dummyObject2, $sortedSet->last()->get());

		$sortedSet->add ($dummyObject3);
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals ($dummyObject2, $sortedSet->last()->get());

		$sortedSet->add ($dummyObject1);
		$this->assertEquals (3, $sortedSet->size());
		$this->assertEquals ($dummyObject1, $sortedSet->last()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::remove
	 */
	public function testRemoveElements() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$this->assertFalse ($sortedSet->remove ($dummyObject1));
		$this->assertFalse ($sortedSet->remove ($dummyObject2));
		$this->assertFalse ($sortedSet->remove ($dummyObject3));

		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject3);
		$this->assertEquals (3, $sortedSet->size());

		$this->assertTrue ($sortedSet->remove ($dummyObject1));
		$this->assertEquals (2, $sortedSet->size());
		$this->checksOrdination ($sortedSet, array ($dummyObject2, $dummyObject3));

		$this->assertTrue ($sortedSet->remove ($dummyObject2));
		$this->assertEquals (1, $sortedSet->size());

		$this->assertFalse ($sortedSet->remove ($dummyObject1));
		$this->assertFalse ($sortedSet->remove ($dummyObject2));
		$this->assertTrue ($sortedSet->remove ($dummyObject3));
		$this->assertEquals (0, $sortedSet->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet2 = new SortedSet();
		$this->assertFalse ($sortedSet1->removeAll ($sortedSet2));

		$sortedSet1->add ($dummyObject1);
		$sortedSet1->add ($dummyObject3);
		$sortedSet2->add ($dummyObject2);

		$this->assertFalse ($sortedSet1->removeAll ($sortedSet2));
		$this->assertEquals (2, $sortedSet1->size());

		$this->assertFalse ($sortedSet2->removeAll ($sortedSet1));
		$this->assertEquals (1, $sortedSet2->size());

		// Adds $dummyObject1 twice
		$sortedSet1->add ($dummyObject1);
		$sortedSet2->add ($dummyObject1);

		$this->assertEquals (2, $sortedSet1->size());
		$this->assertEquals (2, $sortedSet2->size());
		$this->checksOrdination ($sortedSet1, array ($dummyObject1, $dummyObject3));
		$this->checksOrdination ($sortedSet2, array ($dummyObject1, $dummyObject2));

		$this->assertTrue ($sortedSet1->removeAll ($sortedSet2));
		$this->assertEquals (1, $sortedSet1->size());
		$this->assertTrue ($sortedSet1->contains ($dummyObject3));

		$this->assertEquals (2, $sortedSet2->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($sortedSet->removeAll ($arrayList));

		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject3);
		$arrayList->add ($dummyObject2);

		$this->assertFalse ($sortedSet->removeAll ($arrayList));
		$this->assertEquals (2, $sortedSet->size());

		// $sortedSet $dummyObject1 twice
    	$sortedSet->add ($dummyObject1);
    	$arrayList->add ($dummyObject1);

		$this->assertTrue ($sortedSet->removeAll ($arrayList));
		$this->assertEquals (1, $sortedSet->size());
    	$this->assertTrue ($sortedSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$hashSet = new HashSet();
		$this->assertFalse ($sortedSet->removeAll ($hashSet));

		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject3);
		$hashSet->add ($dummyObject2);

		$this->assertFalse ($sortedSet->removeAll ($hashSet));
		$this->assertEquals (2, $sortedSet->size());

		// $sortedSet $dummyObject1 twice
		$sortedSet->add ($dummyObject1);
		$hashSet->add ($dummyObject1);

		$this->assertTrue ($sortedSet->removeAll ($hashSet));
		$this->assertEquals (1, $sortedSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($sortedSet->removeAll ($priorityQueue));

		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject3);
		$priorityQueue->add ($dummyObject2);

		$this->assertFalse ($sortedSet->removeAll ($priorityQueue));
		$this->assertEquals (2, $sortedSet->size());

		// $sortedSet $dummyObject1 twice
		$sortedSet->add ($dummyObject1);
		$priorityQueue->add ($dummyObject1);

		$this->assertTrue ($sortedSet->removeAll ($priorityQueue));
		$this->assertEquals (1, $sortedSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::retainAll
	 */
	public function testRetainAllElementsOfGivenSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet2 = new SortedSet();
		$this->assertFalse ($sortedSet1->retainAll ($sortedSet2));

		// Retains all elements of an empty set
		$sortedSet1->add ($dummyObject1);
		$this->assertTrue ($sortedSet1->retainAll ($sortedSet2));
		$this->assertTrue ($sortedSet1->isEmpty());

		// Both sets shared dummyObject1
		$sortedSet1->add ($dummyObject1);
		$sortedSet1->add ($dummyObject2);
		$sortedSet2->add ($dummyObject1);

		$this->assertEquals (2, $sortedSet1->size());
		$this->assertEquals (1, $sortedSet2->size());

		$this->assertTrue ($sortedSet1->retainAll ($sortedSet2));
		$this->assertEquals (1, $sortedSet1->size());

		foreach ($sortedSet1->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Both sets have the same elements
		$sortedSet1->add ($dummyObject2);
		$sortedSet2->add ($dummyObject2);

		$this->assertEquals (2, $sortedSet1->size());
		$this->assertEquals (2, $sortedSet2->size());

		$this->assertFalse ($sortedSet1->retainAll ($sortedSet2));
		$this->assertEquals (2, $sortedSet1->size());
		$this->assertEquals (2, $sortedSet2->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet1, array ($dummyObject1, $dummyObject2));
		$this->checksOrdination ($sortedSet2, array ($dummyObject1, $dummyObject2));

		// Adds $dummyObject3
		$sortedSet1->add ($dummyObject3);
		$this->assertTrue ($sortedSet1->retainAll ($sortedSet2));

		$this->assertEquals (2, $sortedSet1->size());
		$this->checksOrdination ($sortedSet1, array ($dummyObject1, $dummyObject2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::retainAll
	 */
	public function testRetainAllElementsOfGivenArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($sortedSet->retainAll ($arrayList));

		// Retains all elements of an empty list
		$sortedSet->add ($dummyObject1);
		$this->assertTrue ($sortedSet->retainAll ($arrayList));
		$this->assertTrue ($sortedSet->isEmpty());

		// Both collections shared $dummyObject1
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject2);
		$arrayList->add ($dummyObject1);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (1, $arrayList->size());

		$this->assertTrue ($sortedSet->retainAll ($arrayList));
		$this->assertEquals (1, $sortedSet->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Both collections have the same elements
		$sortedSet->add ($dummyObject2);
		$arrayList->add ($dummyObject2);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $arrayList->size());

		$this->assertFalse ($sortedSet->retainAll ($arrayList));
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $arrayList->size());

		$this->assertEquals ($dummyObject1, $arrayList->get(0));
		$this->assertEquals ($dummyObject2, $arrayList->get(1));
		$this->checksOrdination ($sortedSet, array ($dummyObject1, $dummyObject2));

		// Adds $dummyObject3
		$sortedSet->add ($dummyObject3);
		$this->assertTrue ($sortedSet->retainAll ($arrayList));

		$this->assertEquals (2, $sortedSet->size());
		$this->checksOrdination ($sortedSet, array ($dummyObject1, $dummyObject2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::retainAll
	 */
	public function testRetainAllElementsOfGivenHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$hashSet = new HashSet();
		$this->assertFalse ($sortedSet->retainAll ($hashSet));

		// Retains all elements of an empty list
		$sortedSet->add ($dummyObject1);
		$this->assertTrue ($sortedSet->retainAll ($hashSet));
		$this->assertTrue ($sortedSet->isEmpty());

		// Both collections shared $dummyObject1
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject2);
		$hashSet->add ($dummyObject1);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (1, $hashSet->size());

		$this->assertTrue ($sortedSet->retainAll ($hashSet));
		$this->assertEquals (1, $sortedSet->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Both collections have the same elements
		$sortedSet->add ($dummyObject2);
		$hashSet->add ($dummyObject2);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $hashSet->size());

		$this->assertFalse ($sortedSet->retainAll ($hashSet));
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $hashSet->size());

		$this->assertTrue ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject2));
		$this->checksOrdination ($sortedSet, array ($dummyObject1, $dummyObject2));

		// Adds $dummyObject3
		$sortedSet->add ($dummyObject3);
		$this->assertTrue ($sortedSet->retainAll ($hashSet));

		$this->assertEquals (2, $sortedSet->size());
		$this->checksOrdination ($sortedSet, array ($dummyObject1, $dummyObject2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::retainAll
	 */
	public function testRetainAllElementsOfGivenPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($sortedSet->retainAll ($priorityQueue));

		// Retains all elements of an empty list
		$sortedSet->add ($dummyObject1);
		$this->assertTrue ($sortedSet->retainAll ($priorityQueue));
		$this->assertTrue ($sortedSet->isEmpty());

		// Both collections shared $dummyObject1
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject2);
		$priorityQueue->add ($dummyObject1);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (1, $priorityQueue->size());

		$this->assertTrue ($sortedSet->retainAll ($priorityQueue));
		$this->assertEquals (1, $sortedSet->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Both collections have the same elements
		$sortedSet->add ($dummyObject2);
		$priorityQueue->add ($dummyObject2);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $priorityQueue->size());

		$this->assertFalse ($sortedSet->retainAll ($priorityQueue));
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $priorityQueue->size());

		$this->assertTrue ($priorityQueue->contains ($dummyObject1));
		$this->assertTrue ($priorityQueue->contains ($dummyObject2));
		$this->checksOrdination ($sortedSet, array ($dummyObject1, $dummyObject2));

		// Adds $dummyObject3
		$sortedSet->add ($dummyObject3);
		$this->assertTrue ($sortedSet->retainAll ($priorityQueue));

		$this->assertEquals (2, $sortedSet->size());
		$this->checksOrdination ($sortedSet, array ($dummyObject1, $dummyObject2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::size
	 */
	public function testSizeOfSortedSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$sortedSet = new SortedSet();
    	$this->assertEquals (0, $sortedSet->size());
    	$this->assertTrue ($sortedSet->isEmpty());

    	$sortedSet->add ($dummyObject1);
    	$this->assertEquals (1, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	$sortedSet->add ($dummyObject2);
    	$this->assertEquals (2, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	$sortedSet->add ($dummyObject3);
    	$this->assertEquals (3, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	// Does not permit duplicate elements
    	$sortedSet->add ($dummyObject2);
    	$this->assertEquals (3, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	// Removes every element
    	$sortedSet->remove ($dummyObject2);
    	$this->assertEquals (2, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	$sortedSet->remove ($dummyObject1);
    	$this->assertEquals (1, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	$sortedSet->remove ($dummyObject3);
    	$this->assertEquals (0, $sortedSet->size());
    	$this->assertTrue ($sortedSet->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::stream
	 */
	public function testStreamOfSortedSet() {

		// Empty SortedSet
		$sorted = new SortedSet();
		$stream = $sorted->stream();

		$this->assertNotNull ($stream);
		$this->assertEquals (0, $stream->count());
		$this->assertEmpty ($stream->toArray());

		// Not empty SortedSet
		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sorted = new SortedSet();
		$sorted->add ($dummyObject1);
		$sorted->add ($dummyObject2);
		$sorted->add ($dummyObject3);

		$stream = $sorted->stream();

		$this->assertNotNull ($stream);
		$this->assertEquals ($sorted->size(), $stream->count());
		$this->assertNotEmpty ($stream->toArray());

		$contentOfStream = $stream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertTrue ($sorted->contains ($contentOfStream[$i]));
	}



	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::subSet
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testSubSetWithFromElementGreaterThanToElementUsingCompareToOfStoredObjects() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);

		$sortedSet = new SortedSet();
		$subSet = $sortedSet->subSet ($dummyObject2, $dummyObject1);
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::subSet
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testSubSetWithFromElementGreaterThanToElementUsingGivenComparator() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);

		$sortedSet = new SortedSet (new SortedSet(), new DummyObjectComparator());
		$subSet = $sortedSet->subSet ($dummyObject1, $dummyObject2);
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::subSet
	 */
	public function testSubSetUsingCompareToOfStoredObjects() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (4, "d", FALSE);
		$dummyObject5 = new DummyObject (5, "e", FALSE);

		$sortedSet = new SortedSet();
		$sortedSet->add ($dummyObject2);
		$this->assertEquals (1, $sortedSet->size());

		// The given range is not contained in $sortedSet
		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject4);
		$this->assertTrue ($subSet->isEmpty());

		// By default, $fromElement is not included in the range
		$subSet = $sortedSet->subSet ($dummyObject2, $dummyObject3);
		$this->assertTrue ($subSet->isEmpty());

		$subSet = $sortedSet->subSet ($dummyObject2, $dummyObject3, TRUE);
		$this->assertFalse ($subSet->isEmpty());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Same result if we included the $toElement
		$subSet = $sortedSet->subSet ($dummyObject2, $dummyObject3, TRUE, TRUE);
		$this->assertFalse ($subSet->isEmpty());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Adds several more elements
		$sortedSet->add ($dummyObject3);
		$sortedSet->add ($dummyObject4);

		// Uses a $fromElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($dummyObject1, $dummyObject3);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject2));

		$subSet = $sortedSet->subSet ($dummyObject1, $dummyObject3, TRUE, FALSE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject2));

		$subSet = $sortedSet->subSet ($dummyObject1, $dummyObject3, FALSE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($dummyObject2, $dummyObject3));

		$subSet = $sortedSet->subSet ($dummyObject1, $dummyObject3, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($dummyObject2, $dummyObject3));

		// Uses a $toElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject5);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject4));

		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject5, TRUE, FALSE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($dummyObject3, $dummyObject4));

		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject5, FALSE, TRUE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject4));

		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject5, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($dummyObject3, $dummyObject4));

		// Uses a $toElement and $toElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($dummyObject1, $dummyObject5);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($dummyObject1, $dummyObject5, TRUE, FALSE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($dummyObject1, $dummyObject5, FALSE, TRUE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($dummyObject1, $dummyObject5, TRUE, TRUE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Uses a $toElement and $toElement stored in $sortedSet
		$subSet = $sortedSet->subSet ($dummyObject2, $dummyObject3);
		$this->assertTrue ($subSet->isEmpty());

		$subSet = $sortedSet->subSet ($dummyObject2, $dummyObject3, TRUE, FALSE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject2));

		$subSet = $sortedSet->subSet ($dummyObject2, $dummyObject3, FALSE, TRUE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject3));

		$subSet = $sortedSet->subSet ($dummyObject2, $dummyObject3, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($dummyObject2, $dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::subSet
	 */
	public function testSubSetUsingGivenComparator() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (4, "d", FALSE);
		$dummyObject5 = new DummyObject (5, "e", FALSE);

		$sortedSet = new SortedSet (new SortedSet(), new DummyObjectComparator());
		$sortedSet->add ($dummyObject4);
		$this->assertEquals (1, $sortedSet->size());

		// The given range is not contained in $sortedSet
		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject2);
		$this->assertTrue ($subSet->isEmpty());

		// By default, $fromElement is not included in the range
		$subSet = $sortedSet->subSet ($dummyObject4, $dummyObject3);
		$this->assertTrue ($subSet->isEmpty());

		$subSet = $sortedSet->subSet ($dummyObject4, $dummyObject3, TRUE);
		$this->assertFalse ($subSet->isEmpty());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Same result if we included the $toElement
		$subSet = $sortedSet->subSet ($dummyObject4, $dummyObject3, TRUE, TRUE);
		$this->assertFalse ($subSet->isEmpty());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Adds several more elements
		$sortedSet->add ($dummyObject3);
		$sortedSet->add ($dummyObject2);

		// Uses a $fromElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($dummyObject5, $dummyObject3);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject4));

		$subSet = $sortedSet->subSet ($dummyObject5, $dummyObject3, TRUE, FALSE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject4));

		$subSet = $sortedSet->subSet ($dummyObject5, $dummyObject3, FALSE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($dummyObject4, $dummyObject3));

		$subSet = $sortedSet->subSet ($dummyObject5, $dummyObject3, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($dummyObject4, $dummyObject3));

		// Uses a $toElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject1);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject2));

		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject1, TRUE, FALSE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($dummyObject3, $dummyObject2));

		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject1, FALSE, TRUE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject2));

		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject1, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($dummyObject3, $dummyObject2));

		// Uses a $toElement and $toElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($dummyObject5, $dummyObject1);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($dummyObject5, $dummyObject1, TRUE, FALSE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($dummyObject5, $dummyObject1, FALSE, TRUE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($dummyObject5, $dummyObject1, TRUE, TRUE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Uses a $toElement and $toElement stored in $sortedSet
		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject2);
		$this->assertTrue ($subSet->isEmpty());

		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject2, TRUE, FALSE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject3));

		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject2, FALSE, TRUE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($dummyObject2));

		$subSet = $sortedSet->subSet ($dummyObject3, $dummyObject2, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($dummyObject3, $dummyObject2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::tailSet
	 */
	public function testTailSetUsingCompareToOfStoredObjects() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		// Adds $dummyObject2 in the "origin set"
		$sortedSet->add ($dummyObject2);

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($dummyObject2));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($dummyObject2));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($dummyObject2));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		// Adds "the rest" of dummy objects
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject3);

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1);
		$this->assertEquals (2, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($dummyObject2, $dummyObject3));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1, TRUE);
		$this->assertEquals (3, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($dummyObject1, $dummyObject2, $dummyObject3));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($dummyObject3));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2, TRUE);
		$this->assertEquals (2, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($dummyObject2, $dummyObject3));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::tailSet
	 */
	public function testTailSetUsingGivenComparator() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet (new SortedSet(), new DummyObjectComparator());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		// Adds $dummyObject2 in the "origin set"
		$sortedSet->add ($dummyObject2);

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($dummyObject2));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($dummyObject2));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($dummyObject2));

		// Adds "the rest" of dummy objects
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject3);

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($dummyObject1, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($dummyObject1));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($dummyObject1));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject2, TRUE);
		$this->assertEquals (2, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($dummyObject2, $dummyObject1));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3);
		$this->assertEquals (2, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($dummyObject2, $dummyObject1));

		$tailSortedSet = $sortedSet->tailSet ($dummyObject3, TRUE);
		$this->assertEquals (3, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($dummyObject3, $dummyObject2, $dummyObject1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::toArray
	 */
	public function testToArrayOfSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$sortedSet = new SortedSet();

		$array = $sortedSet->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (0, count ($array));

		$sortedSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject3);

		$array = $sortedSet->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (3, count ($array));

		$this->assertEquals ($dummyObject1, $array[0]);
		$this->assertEquals ($dummyObject2, $array[1]);
		$this->assertEquals ($dummyObject3, $array[2]);

		// Checks reverse ordination
		$sortedSet = new SortedSet (new SortedSet(), new DummyObjectComparator());

		$sortedSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject3);

		$array = $sortedSet->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (3, count ($array));

		$this->assertEquals ($dummyObject3, $array[0]);
		$this->assertEquals ($dummyObject2, $array[1]);
		$this->assertEquals ($dummyObject1, $array[2]);
	}


	/**
	 *    Checks if the elements stored in the given SortedSet are returned in the same order as
	 * the given $elementsInExpectedOrder.
	 *
	 * @param SortedSet $sortedSet
	 *    SortedSet to check
	 * @param array $elementsInExpectedOrder
	 *    The expected result of the "returned elements" stored in the given set
	 */
	private function checksOrdination (SortedSet $sortedSet, array $elementsInExpectedOrder) {

		$this->assertEquals ($sortedSet->size(), count ($elementsInExpectedOrder));
		$this->assertGreaterThan (0, $sortedSet->size());

		$i = 0;
		foreach ($sortedSet->iterator() as $element) {

			$this->assertEquals ($elementsInExpectedOrder[$i], $element);
			$i++;
		}
	}

}

?>