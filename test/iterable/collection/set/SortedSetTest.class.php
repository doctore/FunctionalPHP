<?php

namespace FunctionalPHP\test\iterable\collection\set;

require_once '../../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\queue\PriorityQueue;
use FunctionalPHP\iterable\collection\set\HashSet;
use FunctionalPHP\iterable\collection\set\SortedSet;
use FunctionalPHP\test\Person;
use FunctionalPHP\test\PersonComparator;

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

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet1->add ($person3);
		$sortedSet1->add ($person1);
		$sortedSet1->add ($person2);

		$sortedSet2 = new SortedSet ($sortedSet1);
		$this->assertFalse ($sortedSet2->isEmpty());
		$this->assertEquals ($sortedSet1->size(), $sortedSet2->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet1, array ($person1, $person2, $person3));
		$this->checksOrdination ($sortedSet2, array ($person1, $person2, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::__construct
	 */
	public function testCreateNotEmptySortedSetWithComparator() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet1->add ($person3);
		$sortedSet1->add ($person1);
		$sortedSet1->add ($person2);

		// Uses a comparator that ordering in reverse order
		$sortedSet2 = new SortedSet ($sortedSet1, new PersonComparator());
		$this->assertFalse ($sortedSet2->isEmpty());
		$this->assertEquals ($sortedSet1->size(), $sortedSet2->size());

		foreach ($sortedSet1->iterator() as $element)
			$this->assertTrue ($sortedSet2->contains ($element));

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet1, array ($person1, $person2, $person3));
		$this->checksOrdination ($sortedSet2, array ($person3, $person2, $person1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::add
	 */
	public function testAddElements() {

		$person1      = new Person ("John", 18, TRUE);
		$person1Clone = new Person ("John", 18, FALSE);
		$person2      = new Person ("Mary", 20, FALSE);
		$person3      = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());

		$this->assertTrue ($sortedSet->add ($person1));

		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (1, $sortedSet->size());
		$this->assertTrue ($sortedSet->contains ($person1));

		foreach ($sortedSet->iterator() as $element) {

			$this->assertEquals ($person1->age, $element->age);
			$this->assertEquals ($person1->name, $element->name);
			$this->assertEquals ($person1->isMale, $element->isMale);
		}

		// Adds an "equal object"
		$this->assertFalse ($sortedSet->add ($person1Clone));
		$this->assertEquals (1, $sortedSet->size());
		$this->assertTrue ($sortedSet->contains ($person1));
		$this->assertTrue ($sortedSet->contains ($person1Clone));

		// Checks that $person1 was not replaced by $person1Clone
		foreach ($sortedSet->iterator() as $element) {

			$this->assertEquals ($person1Clone->age, $element->age);
			$this->assertEquals ($person1Clone->name, $element->name);
			$this->assertNotEquals ($person1Clone->isMale, $element->isMale);
		}

		// Adds the "rest of persons"
		$this->assertTrue ($sortedSet->add ($person3));
		$this->assertTrue ($sortedSet->add ($person2));

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet, array ($person1, $person2, $person3));
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

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet1 = new SortedSet();
		$this->assertTrue ($sortedSet1->isEmpty());

		$sortedSet2 = new SortedSet();
		$sortedSet2->add ($person2);
		$sortedSet2->add ($person1);
		$sortedSet2->add ($person3);
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
		$this->checksOrdination ($sortedSet1, array ($person1, $person2, $person3));
		$this->checksOrdination ($sortedSet2, array ($person1, $person2, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::addAll
	 */
	public function testAddAllElementsWithArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());

		$arrayList = new ArrayList();
		$arrayList->add ($person2);
		$arrayList->add ($person3);
		$arrayList->add ($person1);
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
		$this->checksOrdination ($sortedSet, array ($person1, $person2, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::addAll
	 */
	public function testAddAllElementsWithHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet (new HashSet(), new PersonComparator());
		$this->assertTrue ($sortedSet->isEmpty());

		$hashSet = new HashSet();
		$hashSet->add ($person3);
		$hashSet->add ($person2);
		$hashSet->add ($person1);
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
		$this->checksOrdination ($sortedSet, array ($person3, $person2, $person1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::addAll
	 */
	public function testAddAllElementsWithPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet (new HashSet(), new PersonComparator());
		$this->assertTrue ($sortedSet->isEmpty());

		$priorityQueue = new PriorityQueue();
		$priorityQueue->add ($person3);
		$priorityQueue->add ($person2);
		$priorityQueue->add ($person1);
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
		$this->checksOrdination ($sortedSet, array ($person3, $person2, $person1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::clear
	 */
	public function testClearSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());
		$this->assertEquals (0, $sortedSet->size());

		$sortedSet->add ($person1);
		$sortedSet->add ($person2);
		$sortedSet->add ($person3);
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

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$this->assertFalse ($sortedSet->contains ($person1));
		$this->assertFalse ($sortedSet->contains ($person2));
		$this->assertFalse ($sortedSet->contains ($person3));

		$sortedSet->add ($person1);
		$this->assertTrue ($sortedSet->contains ($person1));
		$this->assertFalse ($sortedSet->contains ($person2));
		$this->assertFalse ($sortedSet->contains ($person3));

		$sortedSet->add ($person2);
		$this->assertTrue ($sortedSet->contains ($person1));
		$this->assertTrue ($sortedSet->contains ($person2));
		$this->assertFalse ($sortedSet->contains ($person3));

		$sortedSet->add ($person3);
		$this->assertTrue ($sortedSet->contains ($person1));
		$this->assertTrue ($sortedSet->contains ($person2));
		$this->assertTrue ($sortedSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet2 = new SortedSet();
		$this->assertTrue ($sortedSet1->containsAll ($sortedSet2));

		$sortedSet1->add ($person1);
		$sortedSet2->add ($person1);
		$this->assertTrue ($sortedSet1->containsAll ($sortedSet2));
		$this->assertTrue ($sortedSet2->containsAll ($sortedSet1));

		$sortedSet1->add ($person2);
		$this->assertTrue ($sortedSet1->containsAll ($sortedSet2));
		$this->assertFalse ($sortedSet2->containsAll ($sortedSet1));

		$sortedSet2->add ($person3);
		$this->assertFalse ($sortedSet1->containsAll ($sortedSet2));
		$this->assertFalse ($sortedSet2->containsAll ($sortedSet1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$arrayList = new ArrayList();
		$this->assertTrue ($sortedSet->containsAll ($arrayList));

		$sortedSet->add ($person1);
		$arrayList->add ($person2);
		$this->assertFalse ($sortedSet->containsAll ($arrayList));

		$sortedSet->add ($person2);
		$this->assertTrue ($sortedSet->containsAll ($arrayList));

		$arrayList->add ($person3);
		$this->assertFalse ($sortedSet->containsAll ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$hashSet = new HashSet();
		$this->assertTrue ($sortedSet->containsAll ($hashSet));

		$sortedSet->add ($person1);
		$hashSet->add ($person2);
		$this->assertFalse ($sortedSet->containsAll ($hashSet));

		$sortedSet->add ($person2);
		$this->assertTrue ($sortedSet->containsAll ($hashSet));

		$hashSet->add ($person3);
		$this->assertFalse ($sortedSet->containsAll ($hashSet));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::containsAll
	 */
	public function testCheckElementsContainedInAGivenPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$priorityQueue = new PriorityQueue();
		$this->assertTrue ($sortedSet->containsAll ($priorityQueue));

		$sortedSet->add ($person1);
		$priorityQueue->add ($person2);
		$this->assertFalse ($sortedSet->containsAll ($priorityQueue));

		$sortedSet->add ($person2);
		$this->assertTrue ($sortedSet->containsAll ($priorityQueue));

		$priorityQueue->add ($person3);
		$this->assertFalse ($sortedSet->containsAll ($priorityQueue));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::equals
	 */
	public function testCheckEqualityWithSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet2 = new SortedSet();
		$this->assertTrue ($sortedSet1->equals ($sortedSet1));
		$this->assertTrue ($sortedSet1->equals ($sortedSet2));
		$this->assertTrue ($sortedSet2->equals ($sortedSet1));

		$sortedSet1->add ($person1);
		$this->assertFalse ($sortedSet1->equals ($sortedSet2));
		$this->assertFalse ($sortedSet2->equals ($sortedSet1));

		$sortedSet2->add ($person2);
		$this->assertFalse ($sortedSet1->equals ($sortedSet2));
		$this->assertFalse ($sortedSet2->equals ($sortedSet1));

		// The set have the same elements but added in different order
		$sortedSet1->add ($person3);
		$sortedSet1->add ($person2);
		$sortedSet2->add ($person1);
		$sortedSet2->add ($person3);
		$this->assertEquals (3, $sortedSet1->size());
		$this->assertEquals (3, $sortedSet2->size());

		$this->assertTrue ($sortedSet1->equals ($sortedSet2));
		$this->assertTrue ($sortedSet2->equals ($sortedSet1));

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet1, array ($person1, $person2, $person3));
		$this->checksOrdination ($sortedSet2, array ($person1, $person2, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::equals
	 */
	public function testCheckEqualityWithArrayList() {

		$person = new Person ("John", 18, TRUE);

		$sortedSet = new SortedSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($sortedSet->equals ($arrayList));

		$sortedSet->add ($person);
		$this->assertFalse ($sortedSet->equals ($arrayList));

		$arrayList->add ($person);
		$this->assertFalse ($sortedSet->equals ($arrayList));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::equals
	 */
	public function testCheckEqualityWithHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$hashSet = new HashSet();
		$this->assertTrue ($sortedSet->equals ($hashSet));
		$this->assertTrue ($hashSet->equals ($sortedSet));

		$sortedSet->add ($person1);
		$this->assertFalse ($sortedSet->equals ($hashSet));
		$this->assertFalse ($hashSet->equals ($sortedSet));

		$hashSet->add ($person1);
		$this->assertTrue ($sortedSet->equals ($hashSet));
		$this->assertTrue ($hashSet->equals ($sortedSet));

		foreach ($sortedSet->iterator() as $element)
			$this->assertTrue ($hashSet->contains ($element));

		foreach ($hashSet->iterator() as $element)
			$this->assertTrue ($sortedSet->contains ($element));

		// Adds $person2
		$sortedSet->add ($person2);
		$this->assertFalse ($sortedSet->equals ($hashSet));
		$this->assertFalse ($hashSet->equals ($sortedSet));

		$hashSet->add ($person2);
		$this->assertTrue ($sortedSet->equals ($hashSet));
		$this->assertTrue ($hashSet->equals ($sortedSet));

		// Adds $person3
		$sortedSet->add ($person3);
		$this->assertFalse ($sortedSet->equals ($hashSet));
		$this->assertFalse ($hashSet->equals ($sortedSet));

		$hashSet->add ($person3);
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

		$person = new Person ("John", 18, TRUE);

		$sortedSet = new SortedSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($sortedSet->equals ($priorityQueue));

		$sortedSet->add ($person);
		$this->assertFalse ($sortedSet->equals ($priorityQueue));

		$priorityQueue->add ($person);
		$this->assertFalse ($sortedSet->equals ($priorityQueue));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::first
	 */
	public function testFirstElementOfSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$this->assertFalse ($sortedSet->first()->isPresent());

		$sortedSet->add ($person3);
		$this->assertEquals (1, $sortedSet->size());
		$this->assertEquals ($person3, $sortedSet->first()->get());

		$sortedSet->add ($person1);
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals ($person1, $sortedSet->first()->get());

		$sortedSet->add ($person2);
		$this->assertEquals (3, $sortedSet->size());
		$this->assertEquals ($person1, $sortedSet->first()->get());

		// Checks reverse ordination
		$sortedSet = new SortedSet (new SortedSet(), new PersonComparator());

		$sortedSet->add ($person1);
		$this->assertEquals (1, $sortedSet->size());
		$this->assertEquals ($person1, $sortedSet->first()->get());

		$sortedSet->add ($person3);
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals ($person3, $sortedSet->first()->get());

		$sortedSet->add ($person2);
		$this->assertEquals (3, $sortedSet->size());
		$this->assertEquals ($person3, $sortedSet->first()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::hashCode
	 */
	public function testHashCodeOfSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$this->assertEquals (0, $sortedSet->hashCode());

		$sortedSet->add ($person1);
		$this->assertEquals ($person1->hashCode(), $sortedSet->hashCode());

		$sortedSet->add ($person2);
		$this->assertEquals ($person1->hashCode() + $person2->hashCode()
				            ,$sortedSet->hashCode());

		$sortedSet->add ($person3);
		$this->assertEquals ($person1->hashCode() + $person2->hashCode() + $person3->hashCode()
				            ,$sortedSet->hashCode());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::headSet
	 */
	public function testHeadSetUsingCompareToOfStoredObjects() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();

		$headSortedSet = $sortedSet->headSet ($person1);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person1, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person2);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person2, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person3);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person3, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		// Adds $person2 in the "origin set"
		$sortedSet->add ($person2);

		$headSortedSet = $sortedSet->headSet ($person1);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person1, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person2);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person2, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($person2));

		$headSortedSet = $sortedSet->headSet ($person3);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($person2));

		$headSortedSet = $sortedSet->headSet ($person3, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($person2));

		// Adds "the rest" of persons
		$sortedSet->add ($person1);
		$sortedSet->add ($person3);

		$headSortedSet = $sortedSet->headSet ($person1);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person1, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($person1));

		$headSortedSet = $sortedSet->headSet ($person2);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($person1));

		$headSortedSet = $sortedSet->headSet ($person2, TRUE);
		$this->assertEquals (2, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($person1, $person2));

		$headSortedSet = $sortedSet->headSet ($person3);
		$this->assertEquals (2, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($person1, $person2));

		$headSortedSet = $sortedSet->headSet ($person3, TRUE);
		$this->assertEquals (3, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($person1, $person2, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::headSet
	 */
	public function testHeadSetUsingGivenComparator() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet (new SortedSet(), new PersonComparator());

		$headSortedSet = $sortedSet->headSet ($person1);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person1, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person2);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person2, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person3);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person3, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		// Adds $person2 in the "origin set"
		$sortedSet->add ($person2);

		$headSortedSet = $sortedSet->headSet ($person1);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($person2));

		$headSortedSet = $sortedSet->headSet ($person1, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($person2));

		$headSortedSet = $sortedSet->headSet ($person2);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person2, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($person2));

		$headSortedSet = $sortedSet->headSet ($person3);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person3, TRUE);
		$this->assertTrue ($headSortedSet->isEmpty());

		// Adds "the rest" of persons
		$sortedSet->add ($person1);
		$sortedSet->add ($person3);

		$headSortedSet = $sortedSet->headSet ($person1);
		$this->assertEquals (2, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($person3, $person2));

		$headSortedSet = $sortedSet->headSet ($person1, TRUE);
		$this->assertEquals (3, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($person3, $person2, $person1));

		$headSortedSet = $sortedSet->headSet ($person2);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($person3));

		$headSortedSet = $sortedSet->headSet ($person2, TRUE);
		$this->assertEquals (2, $headSortedSet->size());
		$this->checksOrdination ($headSortedSet, array ($person3, $person2));

		$headSortedSet = $sortedSet->headSet ($person3);
		$this->assertTrue ($headSortedSet->isEmpty());

		$headSortedSet = $sortedSet->headSet ($person3, TRUE);
		$this->assertEquals (1, $headSortedSet->size());
		$this->assertTrue ($headSortedSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::isEmpty
	 */
	public function testEmptySortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());
		$this->assertEquals (0, $sortedSet->size());

		$sortedSet->add ($person1);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (1, $sortedSet->size());

		$sortedSet->add ($person2);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (2, $sortedSet->size());

		$sortedSet->add ($person3);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (3, $sortedSet->size());

		// Does not permit duplicate elements
		$sortedSet->add ($person2);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (3, $sortedSet->size());

		// Removes every element
		$sortedSet->remove ($person3);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (2, $sortedSet->size());

		$sortedSet->remove ($person2);
		$this->assertFalse ($sortedSet->isEmpty());
		$this->assertEquals (1, $sortedSet->size());

		$sortedSet->remove ($person1);
		$this->assertTrue ($sortedSet->isEmpty());
		$this->assertEquals (0, $sortedSet->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::iterator
	 */
	public function testIterateOverSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$this->assertTrue ($sortedSet->isEmpty());

		foreach ($sortedSet->iterator() as $element) {

			// This code should not be executed
			$this->assertTrue (FALSE);
		}

		// Adds $person1
		$sortedSet->add ($person1);
		$this->assertEquals (1, $sortedSet->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Adds another person
		$sortedSet->add ($person2);
		$sortedSet->add ($person3);
		$this->assertEquals (3, $sortedSet->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet, array ($person1, $person2, $person3));

		// Checks reverse comparator
		$sortedSet = new SortedSet (new SortedSet(), new PersonComparator());
		$sortedSet->add ($person2);
		$sortedSet->add ($person1);
		$sortedSet->add ($person3);

		$this->checksOrdination ($sortedSet, array ($person3, $person2, $person1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::last
	 */
	public function testLastElementOfSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$this->assertFalse ($sortedSet->last()->isPresent());

		$sortedSet->add ($person1);
		$this->assertEquals (1, $sortedSet->size());
		$this->assertEquals ($person1, $sortedSet->last()->get());

		$sortedSet->add ($person3);
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals ($person3, $sortedSet->last()->get());

		$sortedSet->add ($person2);
		$this->assertEquals (3, $sortedSet->size());
		$this->assertEquals ($person3, $sortedSet->last()->get());

		// Checks reverse ordination
		$sortedSet = new SortedSet (new SortedSet(), new PersonComparator());

		$sortedSet->add ($person2);
		$this->assertEquals (1, $sortedSet->size());
		$this->assertEquals ($person2, $sortedSet->last()->get());

		$sortedSet->add ($person3);
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals ($person2, $sortedSet->last()->get());

		$sortedSet->add ($person1);
		$this->assertEquals (3, $sortedSet->size());
		$this->assertEquals ($person1, $sortedSet->last()->get());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::remove
	 */
	public function testRemoveElements() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$this->assertFalse ($sortedSet->remove ($person1));
		$this->assertFalse ($sortedSet->remove ($person2));
		$this->assertFalse ($sortedSet->remove ($person3));

		$sortedSet->add ($person1);
		$sortedSet->add ($person2);
		$sortedSet->add ($person3);
		$this->assertEquals (3, $sortedSet->size());

		$this->assertTrue ($sortedSet->remove ($person1));
		$this->assertEquals (2, $sortedSet->size());
		$this->checksOrdination ($sortedSet, array ($person2, $person3));

		$this->assertTrue ($sortedSet->remove ($person2));
		$this->assertEquals (1, $sortedSet->size());

		$this->assertFalse ($sortedSet->remove ($person1));
		$this->assertFalse ($sortedSet->remove ($person2));
		$this->assertTrue ($sortedSet->remove ($person3));
		$this->assertEquals (0, $sortedSet->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet2 = new SortedSet();
		$this->assertFalse ($sortedSet1->removeAll ($sortedSet2));

		$sortedSet1->add ($person1);
		$sortedSet1->add ($person3);
		$sortedSet2->add ($person2);

		$this->assertFalse ($sortedSet1->removeAll ($sortedSet2));
		$this->assertEquals (2, $sortedSet1->size());

		$this->assertFalse ($sortedSet2->removeAll ($sortedSet1));
		$this->assertEquals (1, $sortedSet2->size());

		// Adds $person1 twice
		$sortedSet1->add ($person1);
		$sortedSet2->add ($person1);

		$this->assertEquals (2, $sortedSet1->size());
		$this->assertEquals (2, $sortedSet2->size());
		$this->checksOrdination ($sortedSet1, array ($person1, $person3));
		$this->checksOrdination ($sortedSet2, array ($person1, $person2));

		$this->assertTrue ($sortedSet1->removeAll ($sortedSet2));
		$this->assertEquals (1, $sortedSet1->size());
		$this->assertTrue ($sortedSet1->contains ($person3));

		$this->assertEquals (2, $sortedSet2->size());
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($sortedSet->removeAll ($arrayList));

		$sortedSet->add ($person1);
		$sortedSet->add ($person3);
		$arrayList->add ($person2);

		$this->assertFalse ($sortedSet->removeAll ($arrayList));
		$this->assertEquals (2, $sortedSet->size());

		// $sortedSet $person1 twice
    	$sortedSet->add ($person1);
    	$arrayList->add ($person1);

		$this->assertTrue ($sortedSet->removeAll ($arrayList));
		$this->assertEquals (1, $sortedSet->size());
    	$this->assertTrue ($sortedSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$hashSet = new HashSet();
		$this->assertFalse ($sortedSet->removeAll ($hashSet));

		$sortedSet->add ($person1);
		$sortedSet->add ($person3);
		$hashSet->add ($person2);

		$this->assertFalse ($sortedSet->removeAll ($hashSet));
		$this->assertEquals (2, $sortedSet->size());

		// $sortedSet $person1 twice
		$sortedSet->add ($person1);
		$hashSet->add ($person1);

		$this->assertTrue ($sortedSet->removeAll ($hashSet));
		$this->assertEquals (1, $sortedSet->size());
		$this->assertTrue ($sortedSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::removeAll
	 */
	public function testRemoveAllElementsOfGivenPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($sortedSet->removeAll ($priorityQueue));

		$sortedSet->add ($person1);
		$sortedSet->add ($person3);
		$priorityQueue->add ($person2);

		$this->assertFalse ($sortedSet->removeAll ($priorityQueue));
		$this->assertEquals (2, $sortedSet->size());

		// $sortedSet $person1 twice
		$sortedSet->add ($person1);
		$priorityQueue->add ($person1);

		$this->assertTrue ($sortedSet->removeAll ($priorityQueue));
		$this->assertEquals (1, $sortedSet->size());
		$this->assertTrue ($sortedSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::retainAll
	 */
	public function testRetainAllElementsOfGivenSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet1 = new SortedSet();
		$sortedSet2 = new SortedSet();
		$this->assertFalse ($sortedSet1->retainAll ($sortedSet2));

		// Retains all elements of an empty set
		$sortedSet1->add ($person1);
		$this->assertTrue ($sortedSet1->retainAll ($sortedSet2));
		$this->assertTrue ($sortedSet1->isEmpty());

		// Both sets shared $person1
		$sortedSet1->add ($person1);
		$sortedSet1->add ($person2);
		$sortedSet2->add ($person1);

		$this->assertEquals (2, $sortedSet1->size());
		$this->assertEquals (1, $sortedSet2->size());

		$this->assertTrue ($sortedSet1->retainAll ($sortedSet2));
		$this->assertEquals (1, $sortedSet1->size());

		foreach ($sortedSet1->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Both sets have the same elements
		$sortedSet1->add ($person2);
		$sortedSet2->add ($person2);

		$this->assertEquals (2, $sortedSet1->size());
		$this->assertEquals (2, $sortedSet2->size());

		$this->assertFalse ($sortedSet1->retainAll ($sortedSet2));
		$this->assertEquals (2, $sortedSet1->size());
		$this->assertEquals (2, $sortedSet2->size());

		// Checks the ordination of stored objects
		$this->checksOrdination ($sortedSet1, array ($person1, $person2));
		$this->checksOrdination ($sortedSet2, array ($person1, $person2));

		// Adds $person3
		$sortedSet1->add ($person3);
		$this->assertTrue ($sortedSet1->retainAll ($sortedSet2));

		$this->assertEquals (2, $sortedSet1->size());
		$this->checksOrdination ($sortedSet1, array ($person1, $person2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::retainAll
	 */
	public function testRetainAllElementsOfGivenArrayList() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$arrayList = new ArrayList();
		$this->assertFalse ($sortedSet->retainAll ($arrayList));

		// Retains all elements of an empty list
		$sortedSet->add ($person1);
		$this->assertTrue ($sortedSet->retainAll ($arrayList));
		$this->assertTrue ($sortedSet->isEmpty());

		// Both collections shared $person1
		$sortedSet->add ($person1);
		$sortedSet->add ($person2);
		$arrayList->add ($person1);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (1, $arrayList->size());

		$this->assertTrue ($sortedSet->retainAll ($arrayList));
		$this->assertEquals (1, $sortedSet->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Both collections have the same elements
		$sortedSet->add ($person2);
		$arrayList->add ($person2);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $arrayList->size());

		$this->assertFalse ($sortedSet->retainAll ($arrayList));
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $arrayList->size());

		$this->assertEquals ($person1, $arrayList->get(0));
		$this->assertEquals ($person2, $arrayList->get(1));
		$this->checksOrdination ($sortedSet, array ($person1, $person2));

		// Adds $person3
		$sortedSet->add ($person3);
		$this->assertTrue ($sortedSet->retainAll ($arrayList));

		$this->assertEquals (2, $sortedSet->size());
		$this->checksOrdination ($sortedSet, array ($person1, $person2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::retainAll
	 */
	public function testRetainAllElementsOfGivenHashSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$hashSet = new HashSet();
		$this->assertFalse ($sortedSet->retainAll ($hashSet));

		// Retains all elements of an empty list
		$sortedSet->add ($person1);
		$this->assertTrue ($sortedSet->retainAll ($hashSet));
		$this->assertTrue ($sortedSet->isEmpty());

		// Both collections shared $person1
		$sortedSet->add ($person1);
		$sortedSet->add ($person2);
		$hashSet->add ($person1);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (1, $hashSet->size());

		$this->assertTrue ($sortedSet->retainAll ($hashSet));
		$this->assertEquals (1, $sortedSet->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Both collections have the same elements
		$sortedSet->add ($person2);
		$hashSet->add ($person2);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $hashSet->size());

		$this->assertFalse ($sortedSet->retainAll ($hashSet));
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $hashSet->size());

		$this->assertTrue ($hashSet->contains ($person1));
		$this->assertTrue ($hashSet->contains ($person2));
		$this->checksOrdination ($sortedSet, array ($person1, $person2));

		// Adds $person3
		$sortedSet->add ($person3);
		$this->assertTrue ($sortedSet->retainAll ($hashSet));

		$this->assertEquals (2, $sortedSet->size());
		$this->checksOrdination ($sortedSet, array ($person1, $person2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::retainAll
	 */
	public function testRetainAllElementsOfGivenPriorityQueue() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();
		$priorityQueue = new PriorityQueue();
		$this->assertFalse ($sortedSet->retainAll ($priorityQueue));

		// Retains all elements of an empty list
		$sortedSet->add ($person1);
		$this->assertTrue ($sortedSet->retainAll ($priorityQueue));
		$this->assertTrue ($sortedSet->isEmpty());

		// Both collections shared $person1
		$sortedSet->add ($person1);
		$sortedSet->add ($person2);
		$priorityQueue->add ($person1);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (1, $priorityQueue->size());

		$this->assertTrue ($sortedSet->retainAll ($priorityQueue));
		$this->assertEquals (1, $sortedSet->size());

		foreach ($sortedSet->iterator() as $element)
			$this->assertEquals ($person1, $element);

		// Both collections have the same elements
		$sortedSet->add ($person2);
		$priorityQueue->add ($person2);

		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $priorityQueue->size());

		$this->assertFalse ($sortedSet->retainAll ($priorityQueue));
		$this->assertEquals (2, $sortedSet->size());
		$this->assertEquals (2, $priorityQueue->size());

		$this->assertTrue ($priorityQueue->contains ($person1));
		$this->assertTrue ($priorityQueue->contains ($person2));
		$this->checksOrdination ($sortedSet, array ($person1, $person2));

		// Adds $person3
		$sortedSet->add ($person3);
		$this->assertTrue ($sortedSet->retainAll ($priorityQueue));

		$this->assertEquals (2, $sortedSet->size());
		$this->checksOrdination ($sortedSet, array ($person1, $person2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::size
	 */
	public function testSizeOfSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

    	$sortedSet = new SortedSet();
    	$this->assertEquals (0, $sortedSet->size());
    	$this->assertTrue ($sortedSet->isEmpty());

    	$sortedSet->add ($person1);
    	$this->assertEquals (1, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	$sortedSet->add ($person2);
    	$this->assertEquals (2, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	$sortedSet->add ($person3);
    	$this->assertEquals (3, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	// Does not permit duplicate elements
    	$sortedSet->add ($person2);
    	$this->assertEquals (3, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	// Removes every element
    	$sortedSet->remove ($person2);
    	$this->assertEquals (2, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	$sortedSet->remove ($person1);
    	$this->assertEquals (1, $sortedSet->size());
    	$this->assertFalse ($sortedSet->isEmpty());

    	$sortedSet->remove ($person3);
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
		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sorted = new SortedSet();
		$sorted->add ($person1);
		$sorted->add ($person2);
		$sorted->add ($person3);

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

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$sortedSet = new SortedSet();
		$subSet = $sortedSet->subSet ($person2, $person1);
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::subSet
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testSubSetWithFromElementGreaterThanToElementUsingGivenComparator() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);

		$sortedSet = new SortedSet (new SortedSet(), new PersonComparator());
		$subSet = $sortedSet->subSet ($person1, $person2);
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::subSet
	 */
	public function testSubSetUsingCompareToOfStoredObjects() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);
		$person5 = new Person ("Zach", 19, TRUE);

		$sortedSet = new SortedSet();
		$sortedSet->add ($person2);
		$this->assertEquals (1, $sortedSet->size());

		// The given range is not contained in $sortedSet
		$subSet = $sortedSet->subSet ($person3, $person4);
		$this->assertTrue ($subSet->isEmpty());

		// By default, $fromElement is not included in the range
		$subSet = $sortedSet->subSet ($person2, $person3);
		$this->assertTrue ($subSet->isEmpty());

		$subSet = $sortedSet->subSet ($person2, $person3, TRUE);
		$this->assertFalse ($subSet->isEmpty());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Same result if we included the $toElement
		$subSet = $sortedSet->subSet ($person2, $person3, TRUE, TRUE);
		$this->assertFalse ($subSet->isEmpty());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Adds several more elements
		$sortedSet->add ($person3);
		$sortedSet->add ($person4);

		// Uses a $fromElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($person1, $person3);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person2));

		$subSet = $sortedSet->subSet ($person1, $person3, TRUE, FALSE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person2));

		$subSet = $sortedSet->subSet ($person1, $person3, FALSE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($person2, $person3));

		$subSet = $sortedSet->subSet ($person1, $person3, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($person2, $person3));

		// Uses a $toElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($person3, $person5);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person4));

		$subSet = $sortedSet->subSet ($person3, $person5, TRUE, FALSE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($person3, $person4));

		$subSet = $sortedSet->subSet ($person3, $person5, FALSE, TRUE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person4));

		$subSet = $sortedSet->subSet ($person3, $person5, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($person3, $person4));

		// Uses a $toElement and $toElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($person1, $person5);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($person1, $person5, TRUE, FALSE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($person1, $person5, FALSE, TRUE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($person1, $person5, TRUE, TRUE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Uses a $toElement and $toElement stored in $sortedSet
		$subSet = $sortedSet->subSet ($person2, $person3);
		$this->assertTrue ($subSet->isEmpty());

		$subSet = $sortedSet->subSet ($person2, $person3, TRUE, FALSE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person2));

		$subSet = $sortedSet->subSet ($person2, $person3, FALSE, TRUE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person3));

		$subSet = $sortedSet->subSet ($person2, $person3, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($person2, $person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::subSet
	 */
	public function testSubSetUsingGivenComparator() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);
		$person4 = new Person ("Will", 30, TRUE);
		$person5 = new Person ("Zach", 19, TRUE);

		$sortedSet = new SortedSet (new SortedSet(), new PersonComparator());
		$sortedSet->add ($person4);
		$this->assertEquals (1, $sortedSet->size());

		// The given range is not contained in $sortedSet
		$subSet = $sortedSet->subSet ($person3, $person2);
		$this->assertTrue ($subSet->isEmpty());

		// By default, $fromElement is not included in the range
		$subSet = $sortedSet->subSet ($person4, $person3);
		$this->assertTrue ($subSet->isEmpty());

		$subSet = $sortedSet->subSet ($person4, $person3, TRUE);
		$this->assertFalse ($subSet->isEmpty());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Same result if we included the $toElement
		$subSet = $sortedSet->subSet ($person4, $person3, TRUE, TRUE);
		$this->assertFalse ($subSet->isEmpty());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Adds several more elements
		$sortedSet->add ($person3);
		$sortedSet->add ($person2);

		// Uses a $fromElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($person5, $person3);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person4));

		$subSet = $sortedSet->subSet ($person5, $person3, TRUE, FALSE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person4));

		$subSet = $sortedSet->subSet ($person5, $person3, FALSE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($person4, $person3));

		$subSet = $sortedSet->subSet ($person5, $person3, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($person4, $person3));

		// Uses a $toElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($person3, $person1);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person2));

		$subSet = $sortedSet->subSet ($person3, $person1, TRUE, FALSE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($person3, $person2));

		$subSet = $sortedSet->subSet ($person3, $person1, FALSE, TRUE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person2));

		$subSet = $sortedSet->subSet ($person3, $person1, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($person3, $person2));

		// Uses a $toElement and $toElement not stored in $sortedSet
		$subSet = $sortedSet->subSet ($person5, $person1);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($person5, $person1, TRUE, FALSE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($person5, $person1, FALSE, TRUE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		$subSet = $sortedSet->subSet ($person5, $person1, TRUE, TRUE);
		$this->assertEquals (3, $subSet->size());
		$this->assertTrue ($sortedSet->equals ($subSet));

		// Uses a $toElement and $toElement stored in $sortedSet
		$subSet = $sortedSet->subSet ($person3, $person2);
		$this->assertTrue ($subSet->isEmpty());

		$subSet = $sortedSet->subSet ($person3, $person2, TRUE, FALSE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person3));

		$subSet = $sortedSet->subSet ($person3, $person2, FALSE, TRUE);
		$this->assertEquals (1, $subSet->size());
		$this->assertTrue ($sortedSet->contains ($person2));

		$subSet = $sortedSet->subSet ($person3, $person2, TRUE, TRUE);
		$this->assertEquals (2, $subSet->size());
		$this->checksOrdination ($subSet, array ($person3, $person2));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::tailSet
	 */
	public function testTailSetUsingCompareToOfStoredObjects() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();

		$tailSortedSet = $sortedSet->tailSet ($person1);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person1, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person2);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person2, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person3);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person3, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		// Adds $person2 in the "origin set"
		$sortedSet->add ($person2);

		$tailSortedSet = $sortedSet->tailSet ($person1);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($person2));

		$tailSortedSet = $sortedSet->tailSet ($person1, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($person2));

		$tailSortedSet = $sortedSet->tailSet ($person2);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person2, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($person2));

		$tailSortedSet = $sortedSet->tailSet ($person3);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person3, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		// Adds "the rest" of persons
		$sortedSet->add ($person1);
		$sortedSet->add ($person3);

		$tailSortedSet = $sortedSet->tailSet ($person1);
		$this->assertEquals (2, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($person2, $person3));

		$tailSortedSet = $sortedSet->tailSet ($person1, TRUE);
		$this->assertEquals (3, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($person1, $person2, $person3));

		$tailSortedSet = $sortedSet->tailSet ($person2);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($person3));

		$tailSortedSet = $sortedSet->tailSet ($person2, TRUE);
		$this->assertEquals (2, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($person2, $person3));

		$tailSortedSet = $sortedSet->tailSet ($person3);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person3, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($person3));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::tailSet
	 */
	public function testTailSetUsingGivenComparator() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet (new SortedSet(), new PersonComparator());

		$tailSortedSet = $sortedSet->tailSet ($person1);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person1, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person2);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person2, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person3);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person3, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		// Adds $person2 in the "origin set"
		$sortedSet->add ($person2);

		$tailSortedSet = $sortedSet->tailSet ($person1);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person1, TRUE);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person2);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person2, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($person2));

		$tailSortedSet = $sortedSet->tailSet ($person3);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($person2));

		$tailSortedSet = $sortedSet->tailSet ($person3, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($person2));

		// Adds "the rest" of persons
		$sortedSet->add ($person1);
		$sortedSet->add ($person3);

		$tailSortedSet = $sortedSet->tailSet ($person1);
		$this->assertTrue ($tailSortedSet->isEmpty());

		$tailSortedSet = $sortedSet->tailSet ($person1, TRUE);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($person1));

		$tailSortedSet = $sortedSet->tailSet ($person2);
		$this->assertEquals (1, $tailSortedSet->size());
		$this->assertTrue ($tailSortedSet->contains ($person1));

		$tailSortedSet = $sortedSet->tailSet ($person2, TRUE);
		$this->assertEquals (2, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($person2, $person1));

		$tailSortedSet = $sortedSet->tailSet ($person3);
		$this->assertEquals (2, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($person2, $person1));

		$tailSortedSet = $sortedSet->tailSet ($person3, TRUE);
		$this->assertEquals (3, $tailSortedSet->size());
		$this->checksOrdination ($tailSortedSet, array ($person3, $person2, $person1));
	}


	/**
	 * @covers FunctionalPHP\collection\set\SortedSet::toArray
	 */
	public function testToArrayOfSortedSet() {

		$person1 = new Person ("John", 18, TRUE);
		$person2 = new Person ("Mary", 20, FALSE);
		$person3 = new Person ("Sara", 25, FALSE);

		$sortedSet = new SortedSet();

		$array = $sortedSet->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (0, count ($array));

		$sortedSet->add ($person2);
		$sortedSet->add ($person1);
		$sortedSet->add ($person3);

		$array = $sortedSet->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (3, count ($array));

		$this->assertEquals ($person1, $array[0]);
		$this->assertEquals ($person2, $array[1]);
		$this->assertEquals ($person3, $array[2]);

		// Checks reverse ordination
		$sortedSet = new SortedSet (new SortedSet(), new PersonComparator());

		$sortedSet->add ($person2);
		$sortedSet->add ($person1);
		$sortedSet->add ($person3);

		$array = $sortedSet->toArray();
		$this->assertTrue (is_array ($array));
		$this->assertEquals (3, count ($array));

		$this->assertEquals ($person3, $array[0]);
		$this->assertEquals ($person2, $array[1]);
		$this->assertEquals ($person1, $array[2]);
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