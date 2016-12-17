<?php

namespace FunctionalPHP\test\collection\set;

require_once '../../LoadRequiredFiles.php';


use PHPUnit\Framework\TestCase;

use FunctionalPHP\collection\lists\ArrayList;
use FunctionalPHP\collection\queue\PriorityQueue;
use FunctionalPHP\collection\set\HashSet;
use FunctionalPHP\collection\set\SortedSet;
use FunctionalPHP\test\DummyObject;

/**
 * Class used to test FunctionalPHP\collection\set\HashSet
 */
final class HashSetTest extends TestCase {


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::__construct
	 */
	public function testCreateEmptyHashSet() {

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());
		$this->assertEquals (0, $hashSet->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::__construct
	 */
	public function testCreateNotEmptyHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet1 = new HashSet();
		$hashSet1->add ($dummyObject1);
		$hashSet1->add ($dummyObject2);
		$hashSet1->add ($dummyObject3);

		$hashSet2 = new HashSet ($hashSet1);
		$this->assertFalse ($hashSet2->isEmpty());
		$this->assertEquals ($hashSet1->size(), $hashSet2->size());

		foreach ($hashSet1->iterator() as $element)
			$this->assertTrue ($hashSet2->contains ($element));
	}



	/**
	 * @covers FunctionalPHP\collection\set\HashSet::add
	 */
	public function testAddElements() {

		$dummyObject1      = new DummyObject (1, "a", FALSE);
		$dummyObject1Clone = new DummyObject (1, "a", TRUE);
		$dummyObject2      = new DummyObject (2, "b", FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());

		$this->assertTrue ($hashSet->add ($dummyObject1));

		$this->assertFalse ($hashSet->isEmpty());
		$this->assertEquals (1, $hashSet->size());
		$this->assertTrue ($hashSet->contains ($dummyObject1));

		foreach ($hashSet->iterator() as $element) {

			$this->assertEquals ($dummyObject1->intProperty, $element->intProperty);
			$this->assertEquals ($dummyObject1->stringProperty, $element->stringProperty);
			$this->assertEquals ($dummyObject1->boolProperty, $element->boolProperty);
		}

		// Adds an "equal object"
		$this->assertTrue ($hashSet->add ($dummyObject1Clone));
		$this->assertEquals (1, $hashSet->size());
		$this->assertTrue ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject1Clone));

		// Checks that $dummyObject1 was replaced by $dummyObject1Clone
		foreach ($hashSet->iterator() as $element) {

			$this->assertEquals ($dummyObject1Clone->intProperty, $element->intProperty);
			$this->assertEquals ($dummyObject1Clone->stringProperty, $element->stringProperty);
			$this->assertEquals ($dummyObject1Clone->boolProperty, $element->boolProperty);
		}
		// Adds $dummyObject2
		$this->assertTrue ($hashSet->add ($dummyObject2));
		$this->assertEquals (2, $hashSet->size());
		$this->assertTrue ($hashSet->contains ($dummyObject1Clone));
		$this->assertTrue ($hashSet->contains ($dummyObject2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::addAll
	 */
	public function testAddAllElementsOfEmptyCollection() {

		$hashSet = new HashSet();
		$this->assertFalse ($hashSet->addAll (new HashSet()));
		$this->assertTrue ($hashSet->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::addAll
	 */
	public function testAddAllElementsWithHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet1 = new HashSet();
		$this->assertTrue ($hashSet1->isEmpty());

		$hashSet2 = new HashSet();
		$hashSet2->add ($dummyObject1);
		$hashSet2->add ($dummyObject2);
		$hashSet2->add ($dummyObject3);
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
	 * @covers FunctionalPHP\collection\set\HashSet::addAll
	 */
	public function testAddAllElementsWithArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());

		$arrayList = new ArrayList();
		$arrayList->add ($dummyObject1);
		$arrayList->add ($dummyObject2);
		$arrayList->add ($dummyObject3);
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
	 * @covers FunctionalPHP\collection\set\HashSet::addAll
	 */
	public function testAddAllElementsWithSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());

		$sortedSet = new SortedSet();
		$sortedSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject3);
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
	 * @covers FunctionalPHP\collection\set\HashSet::addAll
	 */
	public function testAddAllElementsWithPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());

		$priorityQueue = new PriorityQueue();
		$priorityQueue->add ($dummyObject1);
		$priorityQueue->add ($dummyObject2);
		$priorityQueue->add ($dummyObject3);
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
	 * @covers FunctionalPHP\collection\set\HashSet::clear
	 */
	public function testClearHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());
		$this->assertEquals (0, $hashSet->size());

		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject2);
		$hashSet->add ($dummyObject3);
		$this->assertFalse ($hashSet->isEmpty());
		$this->assertEquals (3, $hashSet->size());

		$hashSet->clear();
		$this->assertTrue ($hashSet->isEmpty());
		$this->assertEquals (0, $hashSet->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::contains
	 */
	public function testCheckElementsContainedInHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$this->assertFalse ($hashSet->contains ($dummyObject1));
		$this->assertFalse ($hashSet->contains ($dummyObject2));
		$this->assertFalse ($hashSet->contains ($dummyObject3));

		$hashSet->add ($dummyObject1);
		$this->assertTrue ($hashSet->contains ($dummyObject1));
		$this->assertFalse ($hashSet->contains ($dummyObject2));
		$this->assertFalse ($hashSet->contains ($dummyObject3));

		$hashSet->add ($dummyObject2);
		$this->assertTrue ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject2));
		$this->assertFalse ($hashSet->contains ($dummyObject3));

		$hashSet->add ($dummyObject3);
		$this->assertTrue ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject2));
		$this->assertTrue ($hashSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet1 = new HashSet();
		$hashSet2 = new HashSet();
		$this->assertTrue ($hashSet1->containsAll ($hashSet2));

		$hashSet1->add ($dummyObject1);
		$hashSet2->add ($dummyObject1);
		$this->assertTrue ($hashSet1->containsAll ($hashSet2));
		$this->assertTrue ($hashSet2->containsAll ($hashSet1));

		$hashSet1->add ($dummyObject2);
		$this->assertTrue ($hashSet1->containsAll ($hashSet2));
		$this->assertFalse ($hashSet2->containsAll ($hashSet1));

		$hashSet2->add ($dummyObject3);
		$this->assertFalse ($hashSet1->containsAll ($hashSet2));
		$this->assertFalse ($hashSet2->containsAll ($hashSet1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$arrayList = new ArrayList();
		$this->assertTrue ($hashSet->containsAll ($arrayList));

		$hashSet->add ($dummyObject1);
		$arrayList->add ($dummyObject2);
		$this->assertFalse ($hashSet->containsAll ($arrayList));

		$hashSet->add ($dummyObject2);
		$this->assertTrue ($hashSet->containsAll ($arrayList));

		$arrayList->add ($dummyObject3);
		$this->assertFalse ($hashSet->containsAll ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$sortedSet = new SortedSet();
		$this->assertTrue ($hashSet->containsAll ($sortedSet));

		$hashSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject2);
		$this->assertFalse ($hashSet->containsAll ($sortedSet));

		$hashSet->add ($dummyObject2);
		$this->assertTrue ($hashSet->containsAll ($sortedSet));

		$sortedSet->add ($dummyObject3);
		$this->assertFalse ($hashSet->containsAll ($sortedSet));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($hashSet->containsAll ($priorityQueue));

		$hashSet->add ($dummyObject1);
		$priorityQueue->add ($dummyObject2);
		$this->assertFalse ($hashSet->containsAll ($priorityQueue));

		$hashSet->add ($dummyObject2);
		$this->assertTrue ($hashSet->containsAll ($priorityQueue));

		$priorityQueue->add ($dummyObject3);
		$this->assertFalse ($hashSet->containsAll ($priorityQueue));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::equals
	 */
	public function testCheckEqualityWithHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet1 = new HashSet();
		$hashSet2 = new HashSet();
		$this->assertTrue ($hashSet1->equals ($hashSet1));
		$this->assertTrue ($hashSet1->equals ($hashSet2));
		$this->assertTrue ($hashSet2->equals ($hashSet1));

		$hashSet1->add ($dummyObject1);
		$this->assertFalse ($hashSet1->equals ($hashSet2));
		$this->assertFalse ($hashSet2->equals ($hashSet1));

		$hashSet2->add ($dummyObject2);
		$this->assertFalse ($hashSet1->equals ($hashSet2));
		$this->assertFalse ($hashSet2->equals ($hashSet1));

		// The set have the same elements but added in different order
		$hashSet1->add ($dummyObject2);
		$hashSet2->add ($dummyObject1);
		$this->assertEquals (2, $hashSet1->size());
		$this->assertEquals (2, $hashSet2->size());

		$this->assertTrue ($hashSet1->equals ($hashSet2));
		$this->assertTrue ($hashSet2->equals ($hashSet1));

		foreach ($hashSet1->iterator() as $element)
			$this->assertTrue ($hashSet2->contains ($element));

		foreach ($hashSet2->iterator() as $element)
			$this->assertTrue ($hashSet1->contains ($element));

		// Adds the $dummyObject3
		$hashSet1->add ($dummyObject3);
		$this->assertFalse ($hashSet1->equals ($hashSet2));
		$this->assertFalse ($hashSet2->equals ($hashSet1));

		$hashSet2->add ($dummyObject3);
		$this->assertTrue ($hashSet1->equals ($hashSet2));
		$this->assertTrue ($hashSet2->equals ($hashSet1));

		foreach ($hashSet1->iterator() as $element)
			$this->assertTrue ($hashSet2->contains ($element));

		foreach ($hashSet2->iterator() as $element)
			$this->assertTrue ($hashSet1->contains ($element));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::equals
	 */
	public function testCheckEqualityWithArrayList() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashSet = new HashSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($hashSet->equals ($arrayList));

		$hashSet->add ($dummyObject);
		$this->assertFalse ($hashSet->equals ($arrayList));

		$arrayList->add ($dummyObject);
		$this->assertFalse ($hashSet->equals ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::equals
	 */
	public function testCheckEqualityWithSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$sortedSet = new SortedSet();
		$this->assertTrue ($hashSet->equals ($sortedSet));
		$this->assertTrue ($sortedSet->equals ($hashSet));

		$hashSet->add ($dummyObject1);
		$this->assertFalse ($hashSet->equals ($sortedSet));
		$this->assertFalse ($sortedSet->equals ($hashSet));

		$sortedSet->add ($dummyObject1);
		$this->assertTrue ($hashSet->equals ($sortedSet));
		$this->assertTrue ($sortedSet->equals ($hashSet));

		foreach ($hashSet->iterator() as $element)
			$this->assertTrue ($sortedSet->contains ($element));

		foreach ($sortedSet->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));

		// Adds $dummyObject2
		$hashSet->add ($dummyObject2);
		$this->assertFalse ($hashSet->equals ($sortedSet));
		$this->assertFalse ($sortedSet->equals ($hashSet));

		$sortedSet->add ($dummyObject2);
		$this->assertTrue ($hashSet->equals ($sortedSet));
		$this->assertTrue ($sortedSet->equals ($hashSet));

		// Adds $dummyObject3
		$hashSet->add ($dummyObject3);
		$this->assertFalse ($hashSet->equals ($sortedSet));
		$this->assertFalse ($sortedSet->equals ($hashSet));

		$sortedSet->add ($dummyObject3);
		$this->assertTrue ($hashSet->equals ($sortedSet));
		$this->assertTrue ($sortedSet->equals ($hashSet));

		foreach ($hashSet->iterator() as $element)
			$this->assertTrue ($sortedSet->contains ($element));

		foreach ($sortedSet->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::equals
	 */
	public function testCheckEqualityWithPriorityQueue() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$hashSet = new HashSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($hashSet->equals ($priorityQueue));

		$hashSet->add ($dummyObject);
		$this->assertFalse ($hashSet->equals ($priorityQueue));

		$priorityQueue->add ($dummyObject);
		$this->assertFalse ($hashSet->equals ($priorityQueue));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::hashCode
	 */
	public function testHashCodeOfHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$this->assertEquals (0, $hashSet->hashCode());

		$hashSet->add ($dummyObject1);
		$this->assertEquals ($dummyObject1->hashCode(), $hashSet->hashCode());

		$hashSet->add ($dummyObject2);
		$this->assertEquals ($dummyObject1->hashCode() + $dummyObject2->hashCode()
				            ,$hashSet->hashCode());

		$hashSet->add ($dummyObject3);
		$this->assertEquals ($dummyObject1->hashCode() + $dummyObject2->hashCode() + $dummyObject3->hashCode()
				            ,$hashSet->hashCode());
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::isEmpty
	 */
	public function testEmptyHashSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$hashSet = new HashSet();
    	$this->assertTrue ($hashSet->isEmpty());
    	$this->assertEquals (0, $hashSet->size());

    	$hashSet->add ($dummyObject1);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (1, $hashSet->size());

    	$hashSet->add ($dummyObject2);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (2, $hashSet->size());

    	$hashSet->add ($dummyObject3);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (3, $hashSet->size());

    	// Does not accept duplicate elements
    	$hashSet->add ($dummyObject3);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (3, $hashSet->size());

    	// Removes every element
    	$hashSet->remove ($dummyObject3);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (2, $hashSet->size());

    	$hashSet->remove ($dummyObject2);
    	$this->assertFalse ($hashSet->isEmpty());
    	$this->assertEquals (1, $hashSet->size());

    	$hashSet->remove ($dummyObject1);
    	$this->assertTrue ($hashSet->isEmpty());
    	$this->assertEquals (0, $hashSet->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::iterator
	 */
	public function testIterateOverHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$this->assertTrue ($hashSet->isEmpty());

		foreach ($hashSet->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $dummyObject1
		$hashSet->add ($dummyObject1);
		$this->assertEquals (1, $hashSet->size());

		foreach ($hashSet->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Adds another dummy objects
		$hashSet->add ($dummyObject2);
		$hashSet->add ($dummyObject3);
		$this->assertEquals (3, $hashSet->size());

		$isDummyObjectsInHashSet = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($hashSet->iterator() as $element) {

			if ($element->equals ($dummyObject1))
				$isDummyObjectsInHashSet[1] = TRUE;

			elseif ($element->equals ($dummyObject2))
				$isDummyObjectsInHashSet[2] = TRUE;

			elseif ($element->equals ($dummyObject3))
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
	 * @covers FunctionalPHP\collection\set\HashSet::remove
	 */
	public function testRemoveElements() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (11, "k", FALSE);
		$dummyObject5 = new DummyObject (12, "l", FALSE);

		$hashSet = new HashSet();
		$this->assertFalse ($hashSet->remove ($dummyObject1));
		$this->assertFalse ($hashSet->remove ($dummyObject2));
		$this->assertFalse ($hashSet->remove ($dummyObject3));
		$this->assertFalse ($hashSet->remove ($dummyObject4));
		$this->assertFalse ($hashSet->remove ($dummyObject5));

		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject2);
		$hashSet->add ($dummyObject3);
		$hashSet->add ($dummyObject4);
		$hashSet->add ($dummyObject5);
		$this->assertEquals (5, $hashSet->size());
		$this->assertTrue ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject2));
		$this->assertTrue ($hashSet->contains ($dummyObject3));
		$this->assertTrue ($hashSet->contains ($dummyObject4));
		$this->assertTrue ($hashSet->contains ($dummyObject5));

		// Removes $dummyObject1
		$this->assertTrue ($hashSet->remove ($dummyObject1));
		$this->assertEquals (4, $hashSet->size());

		$this->assertFalse ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject2));
		$this->assertTrue ($hashSet->contains ($dummyObject3));
		$this->assertTrue ($hashSet->contains ($dummyObject4));
		$this->assertTrue ($hashSet->contains ($dummyObject5));

		// Removes $dummyObject2
		$this->assertFalse ($hashSet->remove ($dummyObject1));
		$this->assertTrue ($hashSet->remove ($dummyObject2));
		$this->assertEquals (3, $hashSet->size());

		$this->assertFalse ($hashSet->contains ($dummyObject1));
		$this->assertFalse ($hashSet->contains ($dummyObject2));
		$this->assertTrue ($hashSet->contains ($dummyObject3));
		$this->assertTrue ($hashSet->contains ($dummyObject4));
		$this->assertTrue ($hashSet->contains ($dummyObject5));

		// Removes $dummyObject3
		$this->assertFalse ($hashSet->remove ($dummyObject2));
		$this->assertTrue ($hashSet->remove ($dummyObject3));
		$this->assertEquals (2, $hashSet->size());

		$this->assertFalse ($hashSet->contains ($dummyObject1));
		$this->assertFalse ($hashSet->contains ($dummyObject2));
		$this->assertFalse ($hashSet->contains ($dummyObject3));
		$this->assertTrue ($hashSet->contains ($dummyObject4));
		$this->assertTrue ($hashSet->contains ($dummyObject5));

		// Removes $dummyObject4
		$this->assertFalse ($hashSet->remove ($dummyObject3));
		$this->assertTrue ($hashSet->remove ($dummyObject4));
		$this->assertEquals (1, $hashSet->size());

		$this->assertFalse ($hashSet->contains ($dummyObject1));
		$this->assertFalse ($hashSet->contains ($dummyObject2));
		$this->assertFalse ($hashSet->contains ($dummyObject3));
		$this->assertFalse ($hashSet->contains ($dummyObject4));
		$this->assertTrue ($hashSet->contains ($dummyObject5));

		// Removes $dummyObject5
		$this->assertFalse ($hashSet->remove ($dummyObject4));
		$this->assertTrue ($hashSet->remove ($dummyObject5));
		$this->assertEquals (0, $hashSet->size());
		$this->assertTrue ($hashSet->isEmpty());

		$this->assertFalse ($hashSet->contains ($dummyObject1));
		$this->assertFalse ($hashSet->contains ($dummyObject2));
		$this->assertFalse ($hashSet->contains ($dummyObject3));
		$this->assertFalse ($hashSet->contains ($dummyObject4));
		$this->assertFalse ($hashSet->contains ($dummyObject5));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet1 = new HashSet();
		$hashSet2 = new HashSet();
		$this->assertFalse ($hashSet1->removeAll ($hashSet2));

		$hashSet1->add ($dummyObject1);
		$hashSet1->add ($dummyObject3);
		$hashSet2->add ($dummyObject2);

		$this->assertFalse ($hashSet1->removeAll ($hashSet2));
		$this->assertEquals (2, $hashSet1->size());

		$this->assertFalse ($hashSet2->removeAll ($hashSet1));
		$this->assertEquals (1, $hashSet2->size());

		// Adds $dummyObject1 twice
    	$hashSet1->add ($dummyObject1);
    	$hashSet2->add ($dummyObject1);

		$this->assertTrue ($hashSet1->removeAll ($hashSet2));
		$this->assertEquals (1, $hashSet1->size());
		$this->assertEquals (2, $hashSet2->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($hashSet->removeAll ($arrayList));

		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject3);
		$arrayList->add ($dummyObject2);

		$this->assertFalse ($hashSet->removeAll ($arrayList));
		$this->assertEquals (2, $hashSet->size());

		// Adds $dummyObject1 twice
		$hashSet->add ($dummyObject1);
		$arrayList->add ($dummyObject1);

		$this->assertTrue ($hashSet->removeAll ($arrayList));
		$this->assertEquals (1, $hashSet->size());
		$this->assertFalse ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$sortedSet = new SortedSet();
		$this->assertFalse ($hashSet->removeAll ($sortedSet));

		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject3);
		$sortedSet->add ($dummyObject2);

		$this->assertFalse ($hashSet->removeAll ($sortedSet));
		$this->assertEquals (2, $hashSet->size());

		// Adds $dummyObject1 twice
		$hashSet->add ($dummyObject1);
		$sortedSet->add ($dummyObject1);

		$this->assertTrue ($hashSet->removeAll ($sortedSet));
		$this->assertEquals (1, $hashSet->size());
		$this->assertFalse ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($hashSet->removeAll ($priorityQueue));

		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject3);
		$priorityQueue->add ($dummyObject2);

		$this->assertFalse ($hashSet->removeAll ($priorityQueue));
		$this->assertEquals (2, $hashSet->size());

		// Adds $dummyObject1 twice
		$hashSet->add ($dummyObject1);
		$priorityQueue->add ($dummyObject1);

		$this->assertTrue ($hashSet->removeAll ($priorityQueue));
		$this->assertEquals (1, $hashSet->size());
		$this->assertFalse ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::retainAll
	 */
	public function testRetainAllElementsOfGivenHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet1 = new HashSet();
		$hashSet2 = new HashSet();
		$this->assertFalse ($hashSet1->retainAll ($hashSet2));

		// Retains all elements of an empty set
		$hashSet1->add ($dummyObject1);
		$this->assertTrue ($hashSet1->retainAll ($hashSet2));
		$this->assertTrue ($hashSet1->isEmpty());

		// Both sets shared $dummyObject1
		$hashSet1->add ($dummyObject1);
		$hashSet1->add ($dummyObject2);
		$hashSet2->add ($dummyObject1);

		$this->assertEquals (2, $hashSet1->size());
		$this->assertEquals (1, $hashSet2->size());

		$this->assertTrue ($hashSet1->retainAll ($hashSet2));
		$this->assertEquals (1, $hashSet1->size());

		foreach ($hashSet1->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Both sets have the same elements
		$hashSet1->add ($dummyObject2);
		$hashSet2->add ($dummyObject2);

		$this->assertEquals (2, $hashSet1->size());
		$this->assertEquals (2, $hashSet2->size());

		$this->assertFalse ($hashSet1->retainAll ($hashSet2));
		$this->assertEquals (2, $hashSet1->size());
		$this->assertEquals (2, $hashSet2->size());

		$isDummyObjectsInBothSets = array (1 => FALSE, 2 => FALSE);
		foreach ($hashSet1->iterator() as $element) {

			if ($element->equals ($dummyObject1) && $hashSet2->contains ($element))
				$isDummyObjectsInBothSets[1] = TRUE;

			elseif ($element->equals ($dummyObject2) && $hashSet2->contains ($element))
				$isDummyObjectsInBothSets[2] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInBothSets[1]);
		$this->assertTrue ($isDummyObjectsInBothSets[2]);

		// Adds $dummyObject3
		$hashSet1->add ($dummyObject3);
		$this->assertTrue ($hashSet1->retainAll ($hashSet2));
		$this->assertEquals (2, $hashSet1->size());

		$this->assertTrue ($hashSet1->contains ($dummyObject1));
		$this->assertTrue ($hashSet1->contains ($dummyObject2));
		$this->assertFalse ($hashSet1->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::retainAll
	 */
	public function testRetainAllElementsOfGivenArrayList() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($hashSet->retainAll ($arrayList));

		// Retains all elements of an empty list
		$hashSet->add ($dummyObject1);
		$this->assertTrue ($hashSet->retainAll ($arrayList));
		$this->assertTrue ($hashSet->isEmpty());

		// Both collections shared dummyObject1
		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject2);
		$arrayList->add ($dummyObject1);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (1, $arrayList->size());

		$this->assertTrue ($hashSet->retainAll ($arrayList));
		$this->assertEquals (1, $hashSet->size());

		foreach ($hashSet->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Both collections have the same elements
		$hashSet->add ($dummyObject2);
		$arrayList->add ($dummyObject2);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $arrayList->size());

		$this->assertFalse ($hashSet->retainAll ($arrayList));
		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $arrayList->size());

		$isDummyObjectsInBothCollections = array (1 => FALSE, 2 => FALSE);
		foreach ($hashSet->iterator() as $element) {

			if ($element->equals ($dummyObject1) && $arrayList->contains ($element))
				$isDummyObjectsInBothCollections[1] = TRUE;

			elseif ($element->equals ($dummyObject2) && $arrayList->contains ($element))
				$isDummyObjectsInBothCollections[2] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInBothCollections[1]);
		$this->assertTrue ($isDummyObjectsInBothCollections[2]);

		// Adds $dummyObject3
		$hashSet->add ($dummyObject3);
		$this->assertTrue ($hashSet->retainAll ($arrayList));
		$this->assertEquals (2, $hashSet->size());

		$this->assertTrue ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject2));
		$this->assertFalse ($hashSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::retainAll
	 */
	public function testRetainAllElementsOfGivenSortedSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$sortedSet = new SortedSet();
		$this->assertFalse ($hashSet->retainAll ($sortedSet));

		// Retains all elements of an empty list
		$hashSet->add ($dummyObject1);
		$this->assertTrue ($hashSet->retainAll ($sortedSet));
		$this->assertTrue ($hashSet->isEmpty());

		// Both collections shared dummyObject1
		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject1);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (1, $sortedSet->size());

		$this->assertTrue ($hashSet->retainAll ($sortedSet));
		$this->assertEquals (1, $hashSet->size());

		foreach ($hashSet->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Both collections have the same elements
		$hashSet->add ($dummyObject2);
		$sortedSet->add ($dummyObject2);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $sortedSet->size());

		$this->assertFalse ($hashSet->retainAll ($sortedSet));
		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $sortedSet->size());

		$isDummyObjectsInBothCollections = array (1 => FALSE, 2 => FALSE);
		foreach ($hashSet->iterator() as $element) {

			if ($element->equals ($dummyObject1) && $sortedSet->contains ($element))
				$isDummyObjectsInBothCollections[1] = TRUE;

			elseif ($element->equals ($dummyObject2) && $sortedSet->contains ($element))
				$isDummyObjectsInBothCollections[2] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInBothCollections[1]);
		$this->assertTrue ($isDummyObjectsInBothCollections[2]);

		// Adds $dummyObject3
		$hashSet->add ($dummyObject3);
		$this->assertTrue ($hashSet->retainAll ($sortedSet));
		$this->assertEquals (2, $hashSet->size());

		$this->assertTrue ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject2));
		$this->assertFalse ($hashSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::retainAll
	 */
	public function testRetainAllElementsOfGivenPriorityQueue() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($hashSet->retainAll ($priorityQueue));

		// Retains all elements of an empty list
		$hashSet->add ($dummyObject1);
		$this->assertTrue ($hashSet->retainAll ($priorityQueue));
		$this->assertTrue ($hashSet->isEmpty());

		// Both collections shared dummyObject1
		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject2);
		$priorityQueue->add ($dummyObject1);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (1, $priorityQueue->size());

		$this->assertTrue ($hashSet->retainAll ($priorityQueue));
		$this->assertEquals (1, $hashSet->size());

		foreach ($hashSet->iterator() as $element)
			$this->assertEquals ($dummyObject1, $element);

		// Both collections have the same elements
		$hashSet->add ($dummyObject2);
		$priorityQueue->add ($dummyObject2);

		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $priorityQueue->size());

		$this->assertFalse ($hashSet->retainAll ($priorityQueue));
		$this->assertEquals (2, $hashSet->size());
		$this->assertEquals (2, $priorityQueue->size());

		$isDummyObjectsInBothCollections = array (1 => FALSE, 2 => FALSE);
		foreach ($hashSet->iterator() as $element) {

			if ($element->equals ($dummyObject1) && $priorityQueue->contains ($element))
				$isDummyObjectsInBothCollections[1] = TRUE;

			elseif ($element->equals ($dummyObject2) && $priorityQueue->contains ($element))
				$isDummyObjectsInBothCollections[2] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInBothCollections[1]);
		$this->assertTrue ($isDummyObjectsInBothCollections[2]);

		// Adds $dummyObject3
		$hashSet->add ($dummyObject3);
		$this->assertTrue ($hashSet->retainAll ($priorityQueue));
		$this->assertEquals (2, $hashSet->size());

		$this->assertTrue ($hashSet->contains ($dummyObject1));
		$this->assertTrue ($hashSet->contains ($dummyObject2));
		$this->assertFalse ($hashSet->contains ($dummyObject3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::size
	 */
	public function testSizeOfHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$this->assertEquals (0, $hashSet->size());
		$this->assertTrue ($hashSet->isEmpty());

		$hashSet->add ($dummyObject1);
		$this->assertEquals (1, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		$hashSet->add ($dummyObject2);
		$this->assertEquals (2, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		$hashSet->add ($dummyObject3);
		$this->assertEquals (3, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		// Does not permits duplicates
		$hashSet->add ($dummyObject2);
		$this->assertEquals (3, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		// Removes every element
		$hashSet->remove ($dummyObject2);
		$this->assertEquals (2, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		$hashSet->remove ($dummyObject1);
		$this->assertEquals (1, $hashSet->size());
		$this->assertFalse ($hashSet->isEmpty());

		$hashSet->remove ($dummyObject3);
		$this->assertEquals (0, $hashSet->size());
		$this->assertTrue ($hashSet->isEmpty());
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::stream
	 */
	public function testStreamOfHashSet() {

		// Empty HashSet
		$hashSet = new HashSet();
		$stream = $hashSet->stream();

		$this->assertNotNull ($stream);
		$this->assertEquals (0, $stream->count());
		$this->assertEmpty ($stream->toArray());

		// Not empty HashSet
		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();
		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject2);
		$hashSet->add ($dummyObject3);

		$stream = $hashSet->stream();

		$this->assertNotNull ($stream);
		$this->assertEquals ($hashSet->size(), $stream->count());
		$this->assertNotEmpty ($stream->toArray());

		$contentOfStream = $stream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertTrue ($hashSet->contains ($contentOfStream[$i]));
	}


	/**
	 * @covers FunctionalPHP\collection\set\HashSet::toArray
	 */
	public function testToArrayOfHashSet() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);

		$hashSet = new HashSet();

		$array = $hashSet->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (0, count ($array));

		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject2);
		$hashSet->add ($dummyObject3);

		// Does not permits duplicates
		$hashSet->add ($dummyObject3);

		$array = $hashSet->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (3, count ($array));

		$isDummyObjectsInArray = array (1 => FALSE, 2 => FALSE, 3 => FALSE);
		foreach ($array as $element) {

			if ($element->equals ($dummyObject1))
				$isDummyObjectsInArray[1] = TRUE;

			elseif ($element->equals ($dummyObject2))
				$isDummyObjectsInArray[2] = TRUE;

			elseif ($element->equals ($dummyObject3))
				$isDummyObjectsInArray[3] = TRUE;

			// This code should not be executed
			else
				$this->assertTrue (FALSE);
		}
		$this->assertTrue ($isDummyObjectsInArray[1]);
		$this->assertTrue ($isDummyObjectsInArray[2]);
		$this->assertTrue ($isDummyObjectsInArray[3]);
	}

}

?>