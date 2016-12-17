<?php

namespace FunctionalPHP\test\collection\lists;

require_once '../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\collection\lists\ArrayList;
use FunctionalPHP\collection\queue\PriorityQueue;
use FunctionalPHP\collection\set\HashSet;
use FunctionalPHP\collection\set\SortedSet;
use FunctionalPHP\test\DummyObject;

/**
 * Class used to test FunctionalPHP\collection\lists\ArrayList
 */
final class ArrayListTest extends TestCase {


	/**
	 * @covers FunctionalPHP\collection\lists\ArrayList::__construct
	 */
    public function testCreateEmptyArrayList() {

    	$arrayList = new ArrayList();
        $this->assertTrue ($arrayList->isEmpty());
        $this->assertEquals (0, $arrayList->size());
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::__construct
     */
    public function testCreateNotEmptyArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList1 = new ArrayList();
    	$arrayList1->add ($dummyObject1);
    	$arrayList1->add ($dummyObject3);
    	$arrayList1->add ($dummyObject2);

    	$arrayList2 = new ArrayList ($arrayList1);
    	$this->assertFalse ($arrayList2->isEmpty());
    	$this->assertEquals ($arrayList1->size(), $arrayList2->size());

    	for ($i = 0; $i < $arrayList1->size(); $i++)
    		$this->assertEquals ($arrayList2->get($i), $arrayList1->get($i));
    }


	/**
	 * @covers FunctionalPHP\collection\lists\ArrayList::add
	 */
    public function testAddElements() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	$this->assertTrue ($arrayList->add ($dummyObject1));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($dummyObject1, $arrayList->get(0));

    	$this->assertTrue ($arrayList->add ($dummyObject2));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2));

    	$this->assertTrue ($arrayList->add ($dummyObject3));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (3, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject3));

    	// Permits duplicate elements
    	$this->assertTrue ($arrayList->add ($dummyObject1));
    	$this->assertTrue ($arrayList->add ($dummyObject3));
    	$this->assertEquals (5, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject3
    			                                   ,$dummyObject1, $dummyObject3));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::addByIndex
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testAddByNegativeIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->addByIndex (-1, new DummyObject (1, "a", FALSE));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::addByIndex
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testAddByIndexGreaterThanSize() {

    	$arrayList = new ArrayList();
    	$arrayList->addByIndex ($arrayList->size() + 1, new DummyObject (1, "a", FALSE));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::addByIndex
     */
    public function testAddElementsByIndex() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	$this->assertTrue ($arrayList->addByIndex (0, $dummyObject1));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($dummyObject1, $arrayList->get(0));

    	// Overwrites the existing $dummyObject1
    	$this->assertTrue ($arrayList->addByIndex (0, $dummyObject2));
    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject2, $dummyObject1));

    	// Overwrites the existing $dummyObject1
    	$this->assertTrue ($arrayList->addByIndex (1, $dummyObject3));
    	$this->assertEquals (3, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject2, $dummyObject3, $dummyObject1));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::addAll
     */
    public function testAddAllElementsOfEmptyCollection() {

    	$arrayList = new ArrayList();
    	$this->assertFalse ($arrayList->addAll (new ArrayList()));
    	$this->assertTrue ($arrayList->isEmpty());
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::addAll
     */
    public function testAddAllElementsWithArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList1 = new ArrayList();
    	$this->assertTrue ($arrayList1->isEmpty());

    	$arrayList2 = new ArrayList();
    	$arrayList2->add ($dummyObject3);
    	$arrayList2->add ($dummyObject1);
    	$arrayList2->add ($dummyObject2);
    	$this->assertEquals (3, $arrayList2->size());

    	// Adds elements of $arrayList2 inside $arrayList1
    	$this->assertTrue ($arrayList1->addAll ($arrayList2));
    	$this->assertFalse ($arrayList1->isEmpty());
    	$this->assertEquals ($arrayList1->size(), $arrayList2->size());

    	for ($i = 0; $i < $arrayList1->size(); $i++)
    		$this->assertEquals ($arrayList2->get($i), $arrayList1->get($i));

    	// Adds again all elements of $arrayList2 inside $arrayList1
    	$this->assertTrue ($arrayList1->addAll ($arrayList2));
    	$this->assertEquals ($arrayList2->size() * 2, $arrayList1->size());

    	for ($i = 0; $i < $arrayList1->size(); $i++)
    		$this->assertEquals ($arrayList2->get($i % 3), $arrayList1->get($i));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::addAll
     */
    public function testAddAllElementsWithHashSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	$hashSet = new HashSet();
		$hashSet->add ($dummyObject1);
		$hashSet->add ($dummyObject2);
		$hashSet->add ($dummyObject3);
		$this->assertEquals (3, $hashSet->size());

    	// Adds elements of $hashSet inside $arrayList
    	$this->assertTrue ($arrayList->addAll ($hashSet));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals ($hashSet->size(), $arrayList->size());

    	foreach ($hashSet->iterator() as $element)
    		$this->assertTrue ($arrayList->contains ($element));

    	// Adds again all elements of $hashSet inside $arrayList
    	$arrayList->addAll ($hashSet);
    	$this->assertEquals ($hashSet->size() * 2, $arrayList->size());

    	foreach ($hashSet->iterator() as $element)
    		$this->assertTrue ($arrayList->contains ($element));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::addAll
     */
    public function testAddAllElementsWithSortedSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	$sortedSet = new SortedSet();
    	$sortedSet->add ($dummyObject3);
    	$sortedSet->add ($dummyObject1);
    	$sortedSet->add ($dummyObject2);
    	$this->assertEquals (3, $sortedSet->size());

    	// Adds elements of $sortedSet inside $arrayList
    	$this->assertTrue ($arrayList->addAll ($sortedSet));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals ($sortedSet->size(), $arrayList->size());

    	foreach ($sortedSet->iterator() as $element)
    		$this->assertTrue ($arrayList->contains ($element));

    	// Adds again all elements of $sortedSet inside $arrayList
    	$this->assertTrue ($arrayList->addAll ($sortedSet));
    	$this->assertEquals ($sortedSet->size() * 2, $arrayList->size());

    	foreach ($sortedSet->iterator() as $element)
    		$this->assertTrue ($arrayList->contains ($element));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::addAll
     */
    public function testAddAllElementsWithPriorityQueue() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	$priorityQueue = new PriorityQueue();
    	$priorityQueue->add ($dummyObject3);
    	$priorityQueue->add ($dummyObject1);
    	$priorityQueue->add ($dummyObject2);
    	$this->assertEquals (3, $priorityQueue->size());

    	// Adds elements of $priorityQueue inside $arrayList
    	$this->assertTrue ($arrayList->addAll ($priorityQueue));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals ($priorityQueue->size(), $priorityQueue->size());

    	foreach ($priorityQueue->iterator() as $element)
    		$this->assertTrue ($arrayList->contains ($element));

    	// Adds again all elements of $priorityQueue inside $arrayList
    	$this->assertTrue ($arrayList->addAll ($priorityQueue));
    	$this->assertEquals ($priorityQueue->size() * 2, $arrayList->size());

    	foreach ($priorityQueue->iterator() as $element)
    		$this->assertTrue ($arrayList->contains ($element));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::clear
     */
    public function testClearArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());
    	$this->assertEquals (0, $arrayList->size());

    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject3);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (3, $arrayList->size());

    	$arrayList->clear();
    	$this->assertTrue ($arrayList->isEmpty());
    	$this->assertEquals (0, $arrayList->size());
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::contains
     */
    public function testCheckElementsContainedInArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertFalse ($arrayList->contains ($dummyObject1));
    	$this->assertFalse ($arrayList->contains ($dummyObject2));
    	$this->assertFalse ($arrayList->contains ($dummyObject3));

    	$arrayList->add ($dummyObject1);
    	$this->assertTrue ($arrayList->contains ($dummyObject1));
    	$this->assertFalse ($arrayList->contains ($dummyObject2));
    	$this->assertFalse ($arrayList->contains ($dummyObject3));

    	$arrayList->add ($dummyObject2);
    	$this->assertTrue ($arrayList->contains ($dummyObject1));
    	$this->assertTrue ($arrayList->contains ($dummyObject2));
    	$this->assertFalse ($arrayList->contains ($dummyObject3));

    	$arrayList->add ($dummyObject3);
    	$this->assertTrue ($arrayList->contains ($dummyObject1));
    	$this->assertTrue ($arrayList->contains ($dummyObject2));
    	$this->assertTrue ($arrayList->contains ($dummyObject3));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::containsAll
     */
    public function testCheckElementsContainedInAGivenArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList1 = new ArrayList();
    	$arrayList2 = new ArrayList();
    	$this->assertTrue ($arrayList1->containsAll ($arrayList2));

    	$arrayList1->add ($dummyObject1);
    	$arrayList2->add ($dummyObject1);
    	$this->assertTrue ($arrayList1->containsAll ($arrayList2));
    	$this->assertTrue ($arrayList2->containsAll ($arrayList1));

    	$arrayList1->add ($dummyObject2);
    	$this->assertTrue ($arrayList1->containsAll ($arrayList2));
    	$this->assertFalse ($arrayList2->containsAll ($arrayList1));

    	$arrayList2->add ($dummyObject3);
    	$this->assertFalse ($arrayList1->containsAll ($arrayList2));
    	$this->assertFalse ($arrayList2->containsAll ($arrayList1));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::containsAll
     */
    public function testCheckElementsContainedInAGivenHashSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$hashSet = new HashSet();
    	$this->assertTrue ($arrayList->containsAll ($hashSet));

    	$arrayList->add ($dummyObject1);
    	$hashSet->add ($dummyObject2);
    	$this->assertFalse ($arrayList->containsAll ($hashSet));

    	$arrayList->add ($dummyObject2);
    	$this->assertTrue ($arrayList->containsAll ($hashSet));

    	$hashSet->add ($dummyObject3);
    	$this->assertFalse ($arrayList->containsAll ($hashSet));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::containsAll
     */
    public function testCheckElementsContainedInAGivenSortedSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$sortedSet = new SortedSet();
    	$this->assertTrue ($arrayList->containsAll ($sortedSet));

    	$arrayList->add ($dummyObject1);
    	$sortedSet->add ($dummyObject2);
    	$this->assertFalse ($arrayList->containsAll ($sortedSet));

    	$arrayList->add ($dummyObject2);
    	$this->assertTrue ($arrayList->containsAll ($sortedSet));

    	$sortedSet->add ($dummyObject3);
    	$this->assertFalse ($arrayList->containsAll ($sortedSet));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::containsAll
     */
    public function testCheckElementsContainedInAGivenPriorityQueue() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$priorityQueue = new PriorityQueue();
    	$this->assertTrue ($arrayList->containsAll ($priorityQueue));

    	$arrayList->add ($dummyObject1);
    	$priorityQueue->add ($dummyObject2);
    	$this->assertFalse ($arrayList->containsAll ($priorityQueue));

    	$arrayList->add ($dummyObject2);
    	$this->assertTrue ($arrayList->containsAll ($priorityQueue));

    	$priorityQueue->add ($dummyObject3);
    	$this->assertFalse ($arrayList->containsAll ($priorityQueue));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::equals
     */
    public function testCheckEqualityWithArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList1 = new ArrayList();
    	$arrayList2 = new ArrayList();
    	$this->assertTrue ($arrayList1->equals ($arrayList1));
    	$this->assertTrue ($arrayList1->equals ($arrayList2));
    	$this->assertTrue ($arrayList2->equals ($arrayList1));

    	$arrayList1->add ($dummyObject1);
    	$this->assertFalse ($arrayList1->equals ($arrayList2));
    	$this->assertFalse ($arrayList2->equals ($arrayList1));

    	$arrayList2->add ($dummyObject2);
    	$this->assertFalse ($arrayList1->equals ($arrayList2));
    	$this->assertFalse ($arrayList2->equals ($arrayList1));

    	// The lists have the same elements but in different order
    	$arrayList1->add ($dummyObject2);
    	$arrayList2->add ($dummyObject1);
    	$this->assertEquals (2, $arrayList1->size());
    	$this->assertEquals (2, $arrayList2->size());

    	$this->assertEquals ($dummyObject1, $arrayList1->get(0));
    	$this->assertEquals ($dummyObject2, $arrayList1->get(1));
    	$this->assertEquals ($dummyObject2, $arrayList2->get(0));
    	$this->assertEquals ($dummyObject1, $arrayList2->get(1));

    	$this->assertFalse ($arrayList1->equals ($arrayList2));
    	$this->assertFalse ($arrayList2->equals ($arrayList1));

    	// The lists have the same elements in the same order
    	$arrayList1->clear();
    	$arrayList2->clear();

    	$arrayList1->add ($dummyObject1);
    	$arrayList1->add ($dummyObject2);
    	$arrayList2->add ($dummyObject1);
    	$arrayList2->add ($dummyObject2);

    	$this->assertTrue ($arrayList1->equals ($arrayList2));
    	$this->assertTrue ($arrayList2->equals ($arrayList1));

    	// Adds the $dummyObject3
    	$arrayList1->add ($dummyObject3);
    	$this->assertFalse ($arrayList1->equals ($arrayList2));
    	$this->assertFalse ($arrayList2->equals ($arrayList1));

    	$arrayList2->add ($dummyObject3);
    	$this->assertTrue ($arrayList1->equals ($arrayList2));
    	$this->assertTrue ($arrayList2->equals ($arrayList1));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::equals
     */
    public function testCheckEqualityWithHashSet() {

    	$dummyObject = new DummyObject (1, "a", FALSE);

    	$arrayList = new ArrayList();
    	$hashSet = new HashSet();
    	$this->assertFalse ($arrayList->equals ($hashSet));

    	$arrayList->add ($dummyObject);
    	$this->assertFalse ($arrayList->equals ($hashSet));

    	$hashSet->add ($dummyObject);
    	$this->assertFalse ($arrayList->equals ($hashSet));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::equals
     */
    public function testCheckEqualityWithSortedSet() {

    	$dummyObject = new DummyObject (1, "a", FALSE);

    	$arrayList = new ArrayList();
    	$sortedSet = new SortedSet();
    	$this->assertFalse ($arrayList->equals ($sortedSet));

    	$arrayList->add ($dummyObject);
    	$this->assertFalse ($arrayList->equals ($sortedSet));

    	$sortedSet->add ($dummyObject);
    	$this->assertFalse ($arrayList->equals ($sortedSet));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::equals
     */
    public function testCheckEqualityWithPriorityQueue() {

    	$dummyObject = new DummyObject (1, "a", FALSE);

    	$arrayList = new ArrayList();
    	$priorityQueue = new PriorityQueue();
    	$this->assertFalse ($arrayList->equals ($priorityQueue));

    	$arrayList->add ($dummyObject);
    	$this->assertFalse ($arrayList->equals ($priorityQueue));

    	$priorityQueue->add ($dummyObject);
    	$this->assertFalse ($arrayList->equals ($priorityQueue));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::get
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testGetByNegativeIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->get(-1);
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::get
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testGetByIndexEqualsThanSize() {

    	$arrayList = new ArrayList();
    	$arrayList->add (new DummyObject (1, "a", FALSE));

    	$arrayList->get ($arrayList->size());
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::get
     */
    public function testGetElementsByIndex() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject2);

    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2));

    	$arrayList->add ($dummyObject3);
    	$this->assertEquals (3, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject3));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::hashCode
     */
    public function testHashCodeOfArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertEquals (0, $arrayList->hashCode());

    	$arrayList->add ($dummyObject1);
    	$this->assertEquals ($dummyObject1->hashCode(), $arrayList->hashCode());

    	$arrayList->add ($dummyObject2);
    	$this->assertEquals ($dummyObject1->hashCode() + $dummyObject2->hashCode()
    			            ,$arrayList->hashCode());

    	$arrayList->add ($dummyObject3);
    	$this->assertEquals ($dummyObject1->hashCode() + $dummyObject2->hashCode() + $dummyObject3->hashCode()
    			            ,$arrayList->hashCode());
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::indexOf
     */
    public function testFindFirstOccurrenceOfElements() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertEquals (-1, $arrayList->indexOf ($dummyObject1));
    	$this->assertEquals (-1, $arrayList->indexOf ($dummyObject2));
    	$this->assertEquals (-1, $arrayList->indexOf ($dummyObject3));

    	$arrayList->add ($dummyObject1);
    	$this->assertEquals (0, $arrayList->indexOf ($dummyObject1));
    	$this->assertEquals (-1, $arrayList->indexOf ($dummyObject2));
    	$this->assertEquals (-1, $arrayList->indexOf ($dummyObject3));

    	$arrayList->add ($dummyObject2);
    	$this->assertEquals (0, $arrayList->indexOf ($dummyObject1));
    	$this->assertEquals (1, $arrayList->indexOf ($dummyObject2));
    	$this->assertEquals (-1, $arrayList->indexOf ($dummyObject3));

    	$arrayList->add ($dummyObject1);
    	$this->assertEquals (0, $arrayList->indexOf ($dummyObject1));
    	$this->assertEquals (1, $arrayList->indexOf ($dummyObject2));
    	$this->assertEquals (-1, $arrayList->indexOf ($dummyObject3));

    	$arrayList->add ($dummyObject3);
    	$this->assertEquals (0, $arrayList->indexOf ($dummyObject1));
    	$this->assertEquals (1, $arrayList->indexOf ($dummyObject2));
    	$this->assertEquals (3, $arrayList->indexOf ($dummyObject3));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::isEmpty
     */
    public function testEmptyArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());
    	$this->assertEquals (0, $arrayList->size());

    	$arrayList->add ($dummyObject1);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (1, $arrayList->size());

    	$arrayList->add ($dummyObject2);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (2, $arrayList->size());

    	$arrayList->add ($dummyObject3);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (3, $arrayList->size());

    	// Permits duplicate elements
    	$arrayList->add ($dummyObject2);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (4, $arrayList->size());

    	// Removes every element
    	$arrayList->remove ($dummyObject3);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (3, $arrayList->size());

    	$arrayList->remove ($dummyObject2);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (2, $arrayList->size());

    	$arrayList->remove ($dummyObject1);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (1, $arrayList->size());

    	$arrayList->remove ($dummyObject2);
    	$this->assertTrue ($arrayList->isEmpty());
    	$this->assertEquals (0, $arrayList->size());
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::iterator
     */
    public function testIterateOverArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	foreach ($arrayList->iterator() as $element) {

    		// This code should not be executed
    		$this->assertTrue (FALSE);
    	}

    	// Adds $dummyObject1
    	$arrayList->add ($dummyObject1);
    	$this->assertEquals (1, $arrayList->size());

    	foreach ($arrayList->iterator() as $element)
    		$this->assertEquals ($dummyObject1, $element);

    	// Adds more dummy objects
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject3);
    	$this->assertEquals (3, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject3));

    	// Permits duplicate elements
    	$arrayList->add ($dummyObject1);
    	$this->assertEquals (4, $arrayList->size());
   		$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject3
   				                                   ,$dummyObject1));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::lastIndexOf
     */
    public function testFindLastOccurrenceOfElements() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($dummyObject1));
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($dummyObject2));
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($dummyObject3));

    	$arrayList->add ($dummyObject1);
    	$this->assertEquals (0, $arrayList->lastIndexOf ($dummyObject1));
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($dummyObject2));
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($dummyObject3));

    	$arrayList->add ($dummyObject2);
    	$this->assertEquals (0, $arrayList->lastIndexOf ($dummyObject1));
    	$this->assertEquals (1, $arrayList->lastIndexOf ($dummyObject2));
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($dummyObject3));

    	$arrayList->add ($dummyObject3);
    	$this->assertEquals (0, $arrayList->lastIndexOf ($dummyObject1));
    	$this->assertEquals (1, $arrayList->lastIndexOf ($dummyObject2));
    	$this->assertEquals (2, $arrayList->lastIndexOf ($dummyObject3));

    	// Adds $dummyObject1 again
    	$arrayList->add ($dummyObject1);
    	$this->assertEquals (3, $arrayList->lastIndexOf ($dummyObject1));
    	$this->assertEquals (1, $arrayList->lastIndexOf ($dummyObject2));
    	$this->assertEquals (2, $arrayList->lastIndexOf ($dummyObject3));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::remove
     */
    public function testRemoveElements() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertFalse ($arrayList->remove ($dummyObject1));
    	$this->assertFalse ($arrayList->remove ($dummyObject2));
    	$this->assertFalse ($arrayList->remove ($dummyObject3));

    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject3);
    	$this->assertEquals (3, $arrayList->size());

    	$this->assertTrue ($arrayList->remove ($dummyObject1));
    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject2, $dummyObject3));

    	// Adds $dummyObject2 again
    	$arrayList->add ($dummyObject2);
    	$this->checksOrdination ($arrayList, array ($dummyObject2, $dummyObject3, $dummyObject2));

    	$this->assertTrue ($arrayList->remove ($dummyObject2));
    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject3, $dummyObject2));

    	$this->assertFalse ($arrayList->remove ($dummyObject1));
    	$this->assertTrue ($arrayList->remove ($dummyObject2));
    	$this->assertTrue ($arrayList->remove ($dummyObject3));
    	$this->assertEquals (0, $arrayList->size());
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::removeAll
     */
    public function testRemoveAllElementsOfGivenArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList1 = new ArrayList();
    	$arrayList2 = new ArrayList();
    	$this->assertFalse ($arrayList1->removeAll ($arrayList2));

    	$arrayList1->add ($dummyObject1);
    	$arrayList1->add ($dummyObject3);
    	$arrayList2->add ($dummyObject2);

    	$this->assertFalse ($arrayList1->removeAll ($arrayList2));
    	$this->assertEquals (2, $arrayList1->size());

    	$this->assertFalse ($arrayList2->removeAll ($arrayList1));
    	$this->assertEquals (1, $arrayList2->size());

    	// Adds $dummyObject1 twice
    	$arrayList1->add ($dummyObject1);
    	$arrayList2->add ($dummyObject1);

    	$this->assertEquals (3, $arrayList1->size());
    	$this->assertEquals (2, $arrayList2->size());
    	$this->checksOrdination ($arrayList1, array ($dummyObject1, $dummyObject3, $dummyObject1));
    	$this->checksOrdination ($arrayList2, array ($dummyObject2, $dummyObject1));

    	$this->assertTrue ($arrayList1->removeAll ($arrayList2));

    	$this->assertEquals (1, $arrayList1->size());
    	$this->assertEquals ($dummyObject3, $arrayList1->get(0));

    	$this->assertEquals (2, $arrayList2->size());
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::removeAll
     */
    public function testRemoveAllElementsOfGivenHashSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$hashSet = new HashSet();
    	$this->assertFalse ($arrayList->removeAll ($hashSet));

    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject3);
    	$hashSet->add ($dummyObject2);

    	$this->assertFalse ($arrayList->removeAll ($hashSet));
    	$this->assertEquals (2, $arrayList->size());

    	// Adds $dummyObject1 twice
    	$arrayList->add ($dummyObject1);
    	$hashSet->add ($dummyObject1);

    	$this->assertTrue ($arrayList->removeAll ($hashSet));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($dummyObject3, $arrayList->get(0));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::removeAll
     */
    public function testRemoveAllElementsOfGivenSortedSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$sortedSet = new SortedSet();
    	$this->assertFalse ($arrayList->removeAll ($sortedSet));

    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject3);
    	$sortedSet->add ($dummyObject2);

    	$this->assertFalse ($arrayList->removeAll ($sortedSet));
    	$this->assertEquals (2, $arrayList->size());

    	// Adds $dummyObject1 twice
    	$arrayList->add ($dummyObject1);
    	$sortedSet->add ($dummyObject1);

    	$this->assertTrue ($arrayList->removeAll ($sortedSet));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($dummyObject3, $arrayList->get(0));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::removeAll
     */
    public function testRemoveAllElementsOfGivenPriorityQueue() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$priorityQueue = new PriorityQueue();
    	$this->assertFalse ($arrayList->removeAll ($priorityQueue));

    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject3);
    	$priorityQueue->add ($dummyObject2);

    	$this->assertFalse ($arrayList->removeAll ($priorityQueue));
    	$this->assertEquals (2, $arrayList->size());

    	// Adds a common dummy object twice
    	$arrayList->add ($dummyObject1);
    	$priorityQueue->add ($dummyObject1);

    	$this->assertEquals (3, $arrayList->size());
    	$this->assertEquals (2, $priorityQueue->size());

    	$this->assertTrue ($arrayList->removeAll ($priorityQueue));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($dummyObject3, $arrayList->get(0));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::removeByIndex
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testRemoveByNegativeIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->removeByIndex(-1);
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::removeByIndex
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testRemoveByIndexEqualsThanSize() {

    	$dummyObject = new DummyObject (1, "a", FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($dummyObject);

    	$arrayList->removeByIndex ($arrayList->size());
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::removeByIndex
     */
    public function testRemoveElementsByIndex() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);
    	$dummyObject4 = new DummyObject (4, "d", FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject3);
    	$arrayList->add ($dummyObject4);

    	// Removes dummyObject4
    	$this->assertEquals ($dummyObject4, $arrayList->removeByIndex(3));
    	$this->assertEquals (3, $arrayList->size());

    	$this->assertEquals ($dummyObject1, $arrayList->get(0));
    	$this->assertEquals ($dummyObject2, $arrayList->get(1));
    	$this->assertEquals ($dummyObject3, $arrayList->get(2));

    	// Removes dummyObject2
    	$this->assertEquals ($dummyObject2, $arrayList->removeByIndex(1));
    	$this->assertEquals (2, $arrayList->size());

    	$this->assertEquals ($dummyObject1, $arrayList->get(0));
    	$this->assertEquals ($dummyObject3, $arrayList->get(1));

    	// Removes dummyObject1
    	$this->assertEquals ($dummyObject1, $arrayList->removeByIndex(0));
    	$this->assertEquals (1, $arrayList->size());

    	$this->assertEquals ($dummyObject3, $arrayList->get(0));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::retainAll
     */
    public function testRetainAllElementsOfGivenArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList1 = new ArrayList();
    	$arrayList2 = new ArrayList();
    	$this->assertFalse ($arrayList1->retainAll ($arrayList2));

    	// Retains all elements of an empty list
    	$arrayList1->add ($dummyObject1);
    	$this->assertTrue ($arrayList1->retainAll ($arrayList2));
    	$this->assertTrue ($arrayList1->isEmpty());

    	// Both lists shared $dummyObject1
    	$arrayList1->add ($dummyObject1);
    	$arrayList1->add ($dummyObject2);
    	$arrayList2->add ($dummyObject1);

    	$this->assertEquals (2, $arrayList1->size());
    	$this->assertEquals (1, $arrayList2->size());

    	$this->assertTrue ($arrayList1->retainAll ($arrayList2));
    	$this->assertEquals (1, $arrayList1->size());
    	$this->assertEquals ($dummyObject1, $arrayList1->get(0));

    	// Both lists have the same elements
    	$arrayList1->add ($dummyObject2);
    	$arrayList2->add ($dummyObject2);

    	$this->assertEquals (2, $arrayList1->size());
    	$this->assertEquals (2, $arrayList2->size());
    	$this->checksOrdination ($arrayList1, array ($dummyObject1, $dummyObject2));
    	$this->checksOrdination ($arrayList2, array ($dummyObject1, $dummyObject2));

    	$this->assertFalse ($arrayList1->retainAll ($arrayList2));
    	$this->assertEquals (2, $arrayList1->size());
    	$this->assertEquals (2, $arrayList2->size());

    	$this->checksOrdination ($arrayList1, array ($dummyObject1, $dummyObject2));

    	// Permits dulicate elements
    	$arrayList1->add ($dummyObject2);
    	$arrayList2->add ($dummyObject3);

    	$this->assertEquals (3, $arrayList1->size());
    	$this->assertEquals (3, $arrayList2->size());
    	$this->checksOrdination ($arrayList1, array ($dummyObject1, $dummyObject2, $dummyObject2));
    	$this->checksOrdination ($arrayList2, array ($dummyObject1, $dummyObject2, $dummyObject3));

    	$this->assertFalse ($arrayList1->retainAll ($arrayList2));
    	$this->assertEquals (3, $arrayList1->size());
    	$this->checksOrdination ($arrayList1, array ($dummyObject1, $dummyObject2, $dummyObject2));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::retainAll
     */
    public function testRetainAllElementsOfGivenHashSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$hashSet = new HashSet();
    	$this->assertFalse ($arrayList->retainAll ($hashSet));

    	// Retains all elements of an empty set
    	$arrayList->add ($dummyObject1);
    	$this->assertTrue ($arrayList->retainAll ($hashSet));
    	$this->assertTrue ($arrayList->isEmpty());

    	// Both collections shared $dummyObject1
    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject2);
    	$hashSet->add ($dummyObject1);

    	$this->assertEquals (2, $arrayList->size());
    	$this->assertEquals (1, $hashSet->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2));

    	$this->assertTrue ($arrayList->retainAll ($hashSet));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($dummyObject1, $arrayList->get(0));

    	// Both collections have the same elements
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject3);
    	$hashSet->add ($dummyObject2);
    	$hashSet->add ($dummyObject3);

    	$this->assertEquals (4, $arrayList->size());
    	$this->assertEquals (3, $hashSet->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject2
    			                                   ,$dummyObject3));

    	$this->assertFalse ($arrayList->retainAll ($hashSet));
    	$this->assertEquals (4, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject2
    			                                   ,$dummyObject3));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::retainAll
     */
    public function testRetainAllElementsOfGivenSortedSet() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$sortedSet = new SortedSet();
    	$this->assertFalse ($arrayList->retainAll ($sortedSet));

    	// Retains all elements of an empty set
    	$arrayList->add ($dummyObject1);
    	$this->assertTrue ($arrayList->retainAll ($sortedSet));
    	$this->assertTrue ($arrayList->isEmpty());

    	// Both collections shared $dummyObject1
    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject2);
    	$sortedSet->add ($dummyObject1);

    	$this->assertEquals (2, $arrayList->size());
    	$this->assertEquals (1, $sortedSet->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2));

    	$this->assertTrue ($arrayList->retainAll ($sortedSet));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($dummyObject1, $arrayList->get(0));

    	// Both collections have the same elements
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject3);
    	$sortedSet->add ($dummyObject2);
    	$sortedSet->add ($dummyObject3);

    	$this->assertEquals (4, $arrayList->size());
    	$this->assertEquals (3, $sortedSet->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject2
    			                                   ,$dummyObject3));

    	$this->assertFalse ($arrayList->retainAll ($sortedSet));
    	$this->assertEquals (4, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject2
    			                ,$dummyObject3));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::retainAll
     */
    public function testRetainAllElementsOfGivenPriorityQueue() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$priorityQueue = new PriorityQueue();
    	$this->assertFalse ($arrayList->retainAll ($priorityQueue));

    	// Retains all elements of an empty set
    	$arrayList->add ($dummyObject1);
    	$this->assertTrue ($arrayList->retainAll ($priorityQueue));
    	$this->assertTrue ($arrayList->isEmpty());

    	// Both collections shared $dummyObject1
    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject2);
    	$priorityQueue->add ($dummyObject1);

    	$this->assertEquals (2, $arrayList->size());
    	$this->assertEquals (1, $priorityQueue->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2));

    	$this->assertTrue ($arrayList->retainAll ($priorityQueue));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($dummyObject1, $arrayList->get(0));

    	// Both collections have the same elements
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject3);
    	$priorityQueue->add ($dummyObject2);
    	$priorityQueue->add ($dummyObject3);

    	$this->assertEquals (4, $arrayList->size());
    	$this->assertEquals (3, $priorityQueue->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject2
    			                                   ,$dummyObject3));

    	$this->assertFalse ($arrayList->retainAll ($priorityQueue));
    	$this->assertEquals (4, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($dummyObject1, $dummyObject2, $dummyObject2
    			                                   ,$dummyObject3));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::set
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testSetByNegativeIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->set (-1, new DummyObject (1, "a", FALSE));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::set
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testSetByIndexEqualsThanSize() {

    	$dummyObject = new DummyObject (1, "a", FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($dummyObject);

    	$arrayList->set ($arrayList->size(), new DummyObject (1, "a", FALSE));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::set
     */
    public function testSetElementsByIndex() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject2);

    	// Replaces dummyObject1 by dummyObject3
    	$oldElement = $arrayList->set (0, $dummyObject3);

    	$this->assertEquals ($dummyObject1, $oldElement);
    	$this->assertEquals (2, $arrayList->size());
    	$this->assertEquals ($dummyObject3, $arrayList->get(0));
    	$this->assertEquals ($dummyObject2, $arrayList->get(1));

    	// Replaces dummyObject2 by dummyObject3
    	$oldElement = $arrayList->set (1, $dummyObject3);

    	$this->assertEquals ($dummyObject2, $oldElement);
    	$this->assertEquals (2, $arrayList->size());
    	$this->assertEquals ($dummyObject3, $arrayList->get(0));
    	$this->assertEquals ($dummyObject3, $arrayList->get(1));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::size
     */
    public function testSizeOfArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$this->assertEquals (0, $arrayList->size());
    	$this->assertTrue ($arrayList->isEmpty());

    	$arrayList->add ($dummyObject1);
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	$arrayList->add ($dummyObject2);
    	$this->assertEquals (2, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	$arrayList->add ($dummyObject3);
    	$this->assertEquals (3, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	// Permits duplicate elements
    	$arrayList->add ($dummyObject2);
    	$this->assertEquals (4, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	// Removes every element
    	$arrayList->remove ($dummyObject2);
    	$this->assertEquals (3, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	$arrayList->remove ($dummyObject1);
    	$this->assertEquals (2, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	$arrayList->remove ($dummyObject2);
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	$arrayList->remove ($dummyObject3);
    	$this->assertEquals (0, $arrayList->size());
    	$this->assertTrue ($arrayList->isEmpty());
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::stream
     */
    public function testStreamOfArrayList() {

    	// Empty ArrayList
    	$arrayList = new ArrayList();
    	$stream = $arrayList->stream();

    	$this->assertNotNull ($stream);
    	$this->assertEquals (0, $stream->count());
    	$this->assertEmpty ($stream->toArray());

    	// Not empty ArrayList
    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject3);

    	$stream = $arrayList->stream();

    	$this->assertNotNull ($stream);
    	$this->assertEquals ($arrayList->size(), $stream->count());
    	$this->assertNotEmpty ($stream->toArray());

    	$contentOfStream = $stream->toArray();

    	for ($i = 0; $i < count ($contentOfStream); $i++)
    		$this->assertTrue ($arrayList->contains ($contentOfStream[$i]));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::subList
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testSubListByNegativeFromIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->add (new DummyObject (1, "a", FALSE));

    	$arrayList->subList (-1, 0);
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::subList
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testSubListByToIndexGreaterThanSize() {

    	$arrayList = new ArrayList();
    	$arrayList->add (new DummyObject (1, "a", FALSE));

    	$arrayList->subList (0, $arrayList->size() + 1);
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::subList
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testSubListByFromIndexGreaterThanToIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->add (new DummyObject (1, "a", FALSE));

    	$arrayList->subList (1, 0);
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::subList
     */
    public function testSubListOfArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject3);

    	// Returns empty list
    	$arraySubList = $arrayList->subList (1, 1);
    	$this->assertTrue ($arraySubList->isEmpty());

    	// Returns non empty list
    	$arraySubList = $arrayList->subList (0, 1);
    	$this->assertEquals (1, $arraySubList->size());
    	$this->assertEquals ($dummyObject1, $arraySubList->get(0));

    	// Returns the last two elements
    	$arraySubList = $arrayList->subList (1, $arrayList->size());
    	$this->assertEquals (2, $arraySubList->size());
    	$this->assertEquals ($dummyObject2, $arraySubList->get(0));
    	$this->assertEquals ($dummyObject3, $arraySubList->get(1));

    	// Returns all elements
    	$arraySubList = $arrayList->subList (0, $arrayList->size());
    	$this->assertEquals ($arrayList->size(), $arraySubList->size());
    	$this->assertEquals ($dummyObject1, $arraySubList->get(0));
    	$this->assertEquals ($dummyObject2, $arraySubList->get(1));
    	$this->assertEquals ($dummyObject3, $arraySubList->get(2));
    }


    /**
     * @covers FunctionalPHP\collection\lists\ArrayList::toArray
     */
    public function testToArrayOfArrayList() {

    	$dummyObject1 = new DummyObject (1, "a", FALSE);
    	$dummyObject2 = new DummyObject (2, "b", FALSE);
    	$dummyObject3 = new DummyObject (3, "c", FALSE);

    	$arrayList = new ArrayList();

    	$array = $arrayList->toArray();
    	$this->assertTrue (is_array ($array));
    	$this->assertEquals (0, count ($array));

    	$arrayList->add ($dummyObject1);
    	$arrayList->add ($dummyObject3);
    	$arrayList->add ($dummyObject2);
    	$arrayList->add ($dummyObject1);

    	$array = $arrayList->toArray();
    	$this->assertTrue (is_array ($array));
    	$this->assertEquals (4, count ($array));

    	$this->assertEquals ($dummyObject1, $array[0]);
    	$this->assertEquals ($dummyObject3, $array[1]);
    	$this->assertEquals ($dummyObject2, $array[2]);
    	$this->assertEquals ($dummyObject1, $array[3]);
    }


    /**
     *    Checks if the elements stored in the given ArrayList are returned in the same order as
     * the given $elementsInExpectedOrder.
     *
     * @param ArrayList $arrayList
     *    ArrayList to check
     * @param array $elementsInExpectedOrder
     *    The expected result of the "returned elements" stored in the given list
     */
    private function checksOrdination (ArrayList $arrayList, array $elementsInExpectedOrder) {

    	$this->assertEquals ($arrayList->size(), count ($elementsInExpectedOrder));
    	$this->assertGreaterThan (0, $arrayList->size());

    	$i = 0;
    	foreach ($arrayList->iterator() as $element) {

    		$this->assertEquals ($elementsInExpectedOrder[$i], $element);
    		$i++;
    	}
    }

}

?>