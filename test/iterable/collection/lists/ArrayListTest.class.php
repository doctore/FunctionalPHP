<?php

namespace FunctionalPHP\test\iterable\collection\lists;

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
 * Class used to test FunctionalPHP\collection\lists\ArrayList
 */
final class ArrayListTest extends TestCase {


	/**
	 * @covers FunctionalPHP\iterable\collection\lists\ArrayList::__construct
	 */
    public function testCreateEmptyArrayList() {

    	$arrayList = new ArrayList();
        $this->assertTrue ($arrayList->isEmpty());
        $this->assertEquals (0, $arrayList->size());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::__construct
     */
    public function testCreateNotEmptyArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList1 = new ArrayList();
    	$arrayList1->add ($person1);
    	$arrayList1->add ($person2);
    	$arrayList1->add ($person3);

    	$arrayList2 = new ArrayList ($arrayList1);
    	$this->assertFalse ($arrayList2->isEmpty());
    	$this->assertEquals ($arrayList1->size(), $arrayList2->size());

    	for ($i = 0; $i < $arrayList1->size(); $i++)
    		$this->assertEquals ($arrayList2->get($i), $arrayList1->get($i));
    }


	/**
	 * @covers FunctionalPHP\iterable\collection\lists\ArrayList::add
	 */
    public function testAddElements() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	$this->assertTrue ($arrayList->add ($person1));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($person1, $arrayList->get(0));

    	$this->assertTrue ($arrayList->add ($person2));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2));

    	$this->assertTrue ($arrayList->add ($person3));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (3, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2, $person3));

    	// Permits duplicate elements
    	$this->assertTrue ($arrayList->add ($person1));
    	$this->assertTrue ($arrayList->add ($person3));
    	$this->assertEquals (5, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2, $person3, $person1, $person3));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::addByIndex
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testAddByNegativeIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->addByIndex (-1, new Person ("John", 18 , TRUE));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::addByIndex
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testAddByIndexGreaterThanSize() {

    	$arrayList = new ArrayList();
    	$arrayList->addByIndex ($arrayList->size() + 1, new Person ("John", 18 , TRUE));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::addByIndex
     */
    public function testAddElementsByIndex() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	$this->assertTrue ($arrayList->addByIndex (0, $person1));
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($person1, $arrayList->get(0));

    	// Overwrites the existing $person1
    	$this->assertTrue ($arrayList->addByIndex (0, $person2));
    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person2, $person1));

    	// Overwrites the existing $person1
    	$this->assertTrue ($arrayList->addByIndex (1, $person3));
    	$this->assertEquals (3, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person2, $person3, $person1));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::addAll
     */
    public function testAddAllElementsOfEmptyCollection() {

    	$arrayList = new ArrayList();
    	$this->assertFalse ($arrayList->addAll (new ArrayList()));
    	$this->assertTrue ($arrayList->isEmpty());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::addAll
     */
    public function testAddAllElementsWithArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList1 = new ArrayList();
    	$this->assertTrue ($arrayList1->isEmpty());

    	$arrayList2 = new ArrayList();
    	$arrayList2->add ($person3);
    	$arrayList2->add ($person1);
    	$arrayList2->add ($person2);
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
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::addAll
     */
    public function testAddAllElementsWithHashSet() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	$hashSet = new HashSet();
		$hashSet->add ($person1);
		$hashSet->add ($person2);
		$hashSet->add ($person3);
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
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::addAll
     */
    public function testAddAllElementsWithSortedSet() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	$sortedSet = new SortedSet();
    	$sortedSet->add ($person3);
    	$sortedSet->add ($person1);
    	$sortedSet->add ($person2);
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
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::addAll
     */
    public function testAddAllElementsWithPriorityQueue() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	$priorityQueue = new PriorityQueue();
    	$priorityQueue->add ($person3);
    	$priorityQueue->add ($person1);
    	$priorityQueue->add ($person2);
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
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::clear
     */
    public function testClearArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());
    	$this->assertEquals (0, $arrayList->size());

    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$arrayList->add ($person2);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (3, $arrayList->size());

    	$arrayList->clear();
    	$this->assertTrue ($arrayList->isEmpty());
    	$this->assertEquals (0, $arrayList->size());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::contains
     */
    public function testCheckElementsContainedInArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertFalse ($arrayList->contains ($person1));
    	$this->assertFalse ($arrayList->contains ($person2));
    	$this->assertFalse ($arrayList->contains ($person3));

    	$arrayList->add ($person1);
    	$this->assertTrue ($arrayList->contains ($person1));
    	$this->assertFalse ($arrayList->contains ($person2));
    	$this->assertFalse ($arrayList->contains ($person3));

    	$arrayList->add ($person2);
    	$this->assertTrue ($arrayList->contains ($person1));
    	$this->assertTrue ($arrayList->contains ($person2));
    	$this->assertFalse ($arrayList->contains ($person3));

    	$arrayList->add ($person3);
    	$this->assertTrue ($arrayList->contains ($person1));
    	$this->assertTrue ($arrayList->contains ($person2));
    	$this->assertTrue ($arrayList->contains ($person3));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::containsAll
     */
    public function testCheckElementsContainedInAGivenArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList1 = new ArrayList();
    	$arrayList2 = new ArrayList();
    	$this->assertTrue ($arrayList1->containsAll ($arrayList2));

    	$arrayList1->add ($person1);
    	$arrayList2->add ($person1);
    	$this->assertTrue ($arrayList1->containsAll ($arrayList2));
    	$this->assertTrue ($arrayList2->containsAll ($arrayList1));

    	$arrayList1->add ($person2);
    	$this->assertTrue ($arrayList1->containsAll ($arrayList2));
    	$this->assertFalse ($arrayList2->containsAll ($arrayList1));

    	$arrayList2->add ($person3);
    	$this->assertFalse ($arrayList1->containsAll ($arrayList2));
    	$this->assertFalse ($arrayList2->containsAll ($arrayList1));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::containsAll
     */
    public function testCheckElementsContainedInAGivenHashSet() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$hashSet = new HashSet();
    	$this->assertTrue ($arrayList->containsAll ($hashSet));

    	$arrayList->add ($person1);
    	$hashSet->add ($person2);
    	$this->assertFalse ($arrayList->containsAll ($hashSet));

    	$arrayList->add ($person2);
    	$this->assertTrue ($arrayList->containsAll ($hashSet));

    	$hashSet->add ($person3);
    	$this->assertFalse ($arrayList->containsAll ($hashSet));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::containsAll
     */
    public function testCheckElementsContainedInAGivenSortedSet() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$sortedSet = new SortedSet();
    	$this->assertTrue ($arrayList->containsAll ($sortedSet));

    	$arrayList->add ($person1);
    	$sortedSet->add ($person2);
    	$this->assertFalse ($arrayList->containsAll ($sortedSet));

    	$arrayList->add ($person2);
    	$this->assertTrue ($arrayList->containsAll ($sortedSet));

    	$sortedSet->add ($person3);
    	$this->assertFalse ($arrayList->containsAll ($sortedSet));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::containsAll
     */
    public function testCheckElementsContainedInAGivenPriorityQueue() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$priorityQueue = new PriorityQueue();
    	$this->assertTrue ($arrayList->containsAll ($priorityQueue));

    	$arrayList->add ($person1);
    	$priorityQueue->add ($person2);
    	$this->assertFalse ($arrayList->containsAll ($priorityQueue));

    	$arrayList->add ($person2);
    	$this->assertTrue ($arrayList->containsAll ($priorityQueue));

    	$priorityQueue->add ($person3);
    	$this->assertFalse ($arrayList->containsAll ($priorityQueue));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::equals
     */
    public function testCheckEqualityWithArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList1 = new ArrayList();
    	$arrayList2 = new ArrayList();
    	$this->assertTrue ($arrayList1->equals ($arrayList1));
    	$this->assertTrue ($arrayList1->equals ($arrayList2));
    	$this->assertTrue ($arrayList2->equals ($arrayList1));

    	$arrayList1->add ($person1);
    	$this->assertFalse ($arrayList1->equals ($arrayList2));
    	$this->assertFalse ($arrayList2->equals ($arrayList1));

    	$arrayList2->add ($person2);
    	$this->assertFalse ($arrayList1->equals ($arrayList2));
    	$this->assertFalse ($arrayList2->equals ($arrayList1));

    	// The lists have the same elements but in different order
    	$arrayList1->add ($person2);
    	$arrayList2->add ($person1);
    	$this->assertEquals (2, $arrayList1->size());
    	$this->assertEquals (2, $arrayList2->size());

    	$this->assertEquals ($person1, $arrayList1->get(0));
    	$this->assertEquals ($person2, $arrayList1->get(1));
    	$this->assertEquals ($person2, $arrayList2->get(0));
    	$this->assertEquals ($person1, $arrayList2->get(1));

    	$this->assertFalse ($arrayList1->equals ($arrayList2));
    	$this->assertFalse ($arrayList2->equals ($arrayList1));

    	// The lists have the same elements in the same order
    	$arrayList1->clear();
    	$arrayList2->clear();

    	$arrayList1->add ($person1);
    	$arrayList1->add ($person2);
    	$arrayList2->add ($person1);
    	$arrayList2->add ($person2);

    	$this->assertTrue ($arrayList1->equals ($arrayList2));
    	$this->assertTrue ($arrayList2->equals ($arrayList1));

    	// Adds the $person3
    	$arrayList1->add ($person3);
    	$this->assertFalse ($arrayList1->equals ($arrayList2));
    	$this->assertFalse ($arrayList2->equals ($arrayList1));

    	$arrayList2->add ($person3);
    	$this->assertTrue ($arrayList1->equals ($arrayList2));
    	$this->assertTrue ($arrayList2->equals ($arrayList1));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::equals
     */
    public function testCheckEqualityWithHashSet() {

    	$person = new Person ("John", 18, TRUE);

    	$arrayList = new ArrayList();
    	$hashSet = new HashSet();
    	$this->assertFalse ($arrayList->equals ($hashSet));

    	$arrayList->add ($person);
    	$this->assertFalse ($arrayList->equals ($hashSet));

    	$hashSet->add ($person);
    	$this->assertFalse ($arrayList->equals ($hashSet));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::equals
     */
    public function testCheckEqualityWithSortedSet() {

    	$person = new Person ("John", 18, TRUE);

    	$arrayList = new ArrayList();
    	$sortedSet = new SortedSet();
    	$this->assertFalse ($arrayList->equals ($sortedSet));

    	$arrayList->add ($person);
    	$this->assertFalse ($arrayList->equals ($sortedSet));

    	$sortedSet->add ($person);
    	$this->assertFalse ($arrayList->equals ($sortedSet));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::equals
     */
    public function testCheckEqualityWithPriorityQueue() {

    	$person = new Person ("John", 18, TRUE);

    	$arrayList = new ArrayList();
    	$priorityQueue = new PriorityQueue();
    	$this->assertFalse ($arrayList->equals ($priorityQueue));

    	$arrayList->add ($person);
    	$this->assertFalse ($arrayList->equals ($priorityQueue));

    	$priorityQueue->add ($person);
    	$this->assertFalse ($arrayList->equals ($priorityQueue));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::filter
     */
    public function testFilterOfEmptyArrayList() {

    	$arrayList = new ArrayList();
    	$this->assertEquals (0, $arrayList->size());

    	$filteredArrayList = $arrayList->filter (new HasPersonOddAgePredicate());
    	$this->assertEquals (0, $filteredArrayList->size());

    	$filteredArrayList = $arrayList->filter (new HasPersonMoreThanOneWordAsNamePredicate());
    	$this->assertEquals (0, $filteredArrayList->size());

    	$filteredArrayList = $arrayList->filter (new HasPersonNoAgeValuePredicate());
    	$this->assertEquals (0, $filteredArrayList->size());

    	$filteredArrayList = $arrayList->filter (new HasPersonNameWithValuePredicate());
    	$this->assertEquals (0, $filteredArrayList->size());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::filter
     */
    public function testFilterOnePredicate() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary Swan", 20, FALSE);
    	$person4 = new Person ("Seth", 31, TRUE);
    	$person5 = new Person ("Marc", 11, TRUE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);
    	$arrayList->add ($person4);
    	$arrayList->add ($person5);

    	// HasPersonOddAgePredicate
    	$filteredArrayList = $arrayList->filter (new HasPersonOddAgePredicate());
    	$this->assertGreaterThan (0, $filteredArrayList->size());

    	foreach ($filteredArrayList->iterator() as $person)
    		$this->assertTrue ($person->age % 2 != 0);

    	// HasPersonMoreThanOneWordAsNamePredicate
    	$filteredArrayList = $arrayList->filter (new HasPersonMoreThanOneWordAsNamePredicate());
    	$this->assertGreaterThan (0, $filteredArrayList->size());

    	foreach ($filteredArrayList->iterator() as $person)
    		$this->assertTrue (str_word_count ($person->name) > 1);

    	// HasPersonNoAgeValuePredicate
   		$filteredArrayList = $arrayList->filter (new HasPersonNoAgeValuePredicate());
   		$this->assertEquals (0, $filteredArrayList->size());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::filter
     */
    public function testFilterMoreThanOnePredicate() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary Swan", 21, FALSE);
    	$person4 = new Person ("Seth", 31, TRUE);
    	$person5 = new Person ("Marc", 11, TRUE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);
    	$arrayList->add ($person4);
    	$arrayList->add ($person5);

    	// HasPersonOddAgePredicate && HasPersonMoreThanOneWordAsNamePredicate
    	$filteredArrayList = $arrayList->filter (new HasPersonOddAgePredicate())
    	                               ->filter (new HasPersonMoreThanOneWordAsNamePredicate());

    	$this->assertGreaterThan (0, $filteredArrayList->size());

    	foreach ($filteredArrayList->iterator() as $person) {

    		$this->assertTrue ($person->age % 2 != 0);
    		$this->assertTrue (str_word_count ($person->name) > 1);
    	}
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::filterByLambda
     *
     * @expectedException FunctionalPHP\exception\UnsupportedOperationException
     */
    public function testFilterByLambdaWithClosureWithMoreThanOneParameter() {

    	$arrayList = new ArrayList();

    	$arrayList->filterByLambda (function (int $p1, string $p2) {
    		                           $p1 += 1;
    		                           $p2 .= "_test";
    	                            });
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::filterByLambda
     *
     * @expectedException FunctionalPHP\exception\UnsupportedOperationException
     */
    public function testFilterByLambdaWithClosureWithParameterTypeDifferentOfStreamElements() {

    	$arrayList = new ArrayList();

    	$arrayList->filterByLambda (function (string $p1) {
    		                           $p1 .= "_test";
    	                            });
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::filterByLambda
     *
     * @expectedException FunctionalPHP\exception\UnsupportedOperationException
     */
    public function testFilterByLambdaWithClosureWithInvalidReturnedType() {

    	$arrayList = new ArrayList();

    	$arrayList->filterByLambda (function (Person $person) : Person {
    		                           $person->age *= 2;
    	                            });
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::filterByLambda
     */
    public function testFilterByLambdaByOneIteration() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary Swan", 21, FALSE);
    	$person4 = new Person ("Seth", 31, TRUE);
    	$person5 = new Person ("Son", 11, TRUE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);
    	$arrayList->add ($person4);
    	$arrayList->add ($person5);

    	// Filter even age values
    	$filteredArrayList = $arrayList->filterByLambda (function (Person $person) : bool {
    		                                                return $person->age % 2 == 0;
    	                                                 });

   		$this->assertGreaterThan (0, $filteredArrayList->size());

   		foreach ($filteredArrayList->iterator() as $person)
    		$this->assertEquals (0, $person->age % 2);

    	// Filter name values with lenght = 3
    	$filteredArrayList = $arrayList->filterByLambda (function (Person $person) : bool {
    				                                        return strlen ($person->name) == 3;
    			                                         });

    	$this->assertGreaterThan (0, $filteredArrayList->size());
    	foreach ($filteredArrayList->iterator() as $person)
    		$this->assertEquals (3, strlen ($person->name));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::filterByLambda
     */
    public function testFilterByLambdaByMoreThanOneIteration() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary Swan", 21, FALSE);
    	$person4 = new Person ("Seth", 31, TRUE);
    	$person5 = new Person ("Son", 12, TRUE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);
    	$arrayList->add ($person4);
    	$arrayList->add ($person5);

    	// Filter even age values and name values with lenght = 3
    	$filteredArrayList = $arrayList->filterByLambda (function (Person $person) : bool {
    		                                                return $person->age % 2 == 0;
    	                                                 })
    	                               ->filterByLambda (function (Person $person) : bool {
    		                                                return strlen ($person->name) == 3;
    	                                                 });

    	$this->assertGreaterThan (0, $filteredArrayList->size());
    	foreach ($filteredArrayList->iterator() as $person) {

    		$this->assertEquals (0, $person->age % 2);
    		$this->assertEquals (3, strlen ($person->name));
   		}
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::forEach
     *
     * @expectedException FunctionalPHP\exception\UnsupportedOperationException
     */
    public function testForEachWithClosureWithMoreThanOneParameter() {

    	$arrayList = new ArrayList();

    	$arrayList->forEach (function (int $p1, string $p2) {
    		                    $p1 += 1;
    		                    $p2 .= "_test";
    	                     });
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::forEach
     *
     * @expectedException FunctionalPHP\exception\UnsupportedOperationException
     */
    public function testForEachWithClosureWithParameterTypeDifferentOfObject() {

    	$arrayList = new ArrayList();

    	$arrayList->forEach (function (int $p1) {
    		                    $p1 += 1;
    	                     });
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::forEach
     *
     * @expectedException FunctionalPHP\exception\UnsupportedOperationException
     */
    public function testForEachWithClosureWithInvalidReturnedType() {

    	$arrayList = new ArrayList();
    	$arrayList->add (new Person ("John", 18, TRUE));

    	$arrayList->forEach (function (Person $person) : int {
    		                    $person->age *= 2;
    	                     });
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::forEach
     */
    public function testForEachOfValidClosureFunction() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person1Clone = new Person ("John", 18, TRUE);

    	$person2 = new Person ("Sara", 25, FALSE);
    	$person2Clone = new Person ("Sara", 25, FALSE);

    	$person3 = new Person ("Mary", 20, FALSE);
    	$person3Clone = new Person ("Mary", 20, FALSE);

    	$arrayListOriginal = new ArrayList();
    	$arrayListOriginal->add ($person1);
    	$arrayListOriginal->add ($person2);
    	$arrayListOriginal->add ($person3);

    	$arrayListToModified = new ArrayList();
    	$arrayListToModified->add ($person1Clone);
    	$arrayListToModified->add ($person2Clone);
    	$arrayListToModified->add ($person3Clone);

    	$arrayListToModified->forEach (function (Person $person) {
    		                              $person->age *= 2;
    	                               });

    	$this->assertGreaterThan (0, $arrayListToModified->size());
    	$this->assertEquals ($arrayListOriginal->size(), $arrayListToModified->size());

    	// Checks that only has changed the value of age property
    	for ($i = 0; $i < $arrayListOriginal->size(); $i++) {

    		$this->assertEquals ($arrayListOriginal->get($i)->name, $arrayListToModified->get($i)->name);
    		$this->assertEquals ($arrayListOriginal->get($i)->age * 2, $arrayListToModified->get($i)->age);
    		$this->assertEquals ($arrayListOriginal->get($i)->isMale, $arrayListToModified->get($i)->isMale);
    	}
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::get
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testGetByNegativeIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->get(-1);
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::get
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testGetByIndexEqualsThanSize() {

    	$arrayList = new ArrayList();
    	$arrayList->add (new Person ("John", 18, TRUE));

    	$arrayList->get ($arrayList->size());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::get
     */
    public function testGetElementsByIndex() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);

    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2));

    	$arrayList->add ($person3);
    	$this->assertEquals (3, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2, $person3));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::hashCode
     */
    public function testHashCodeOfArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertEquals (0, $arrayList->hashCode());

    	$arrayList->add ($person1);
    	$this->assertEquals ($person1->hashCode(), $arrayList->hashCode());

    	$arrayList->add ($person2);
    	$this->assertEquals ($person1->hashCode() + $person2->hashCode()
    			            ,$arrayList->hashCode());

    	$arrayList->add ($person3);
    	$this->assertEquals ($person1->hashCode() + $person2->hashCode() + $person3->hashCode()
    			            ,$arrayList->hashCode());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::indexOf
     */
    public function testFindFirstOccurrenceOfElements() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertEquals (-1, $arrayList->indexOf ($person1));
    	$this->assertEquals (-1, $arrayList->indexOf ($person2));
    	$this->assertEquals (-1, $arrayList->indexOf ($person3));

    	$arrayList->add ($person1);
    	$this->assertEquals (0, $arrayList->indexOf ($person1));
    	$this->assertEquals (-1, $arrayList->indexOf ($person2));
    	$this->assertEquals (-1, $arrayList->indexOf ($person3));

    	$arrayList->add ($person2);
    	$this->assertEquals (0, $arrayList->indexOf ($person1));
    	$this->assertEquals (1, $arrayList->indexOf ($person2));
    	$this->assertEquals (-1, $arrayList->indexOf ($person3));

    	$arrayList->add ($person1);
    	$this->assertEquals (0, $arrayList->indexOf ($person1));
    	$this->assertEquals (1, $arrayList->indexOf ($person2));
    	$this->assertEquals (-1, $arrayList->indexOf ($person3));

    	$arrayList->add ($person3);
    	$this->assertEquals (0, $arrayList->indexOf ($person1));
    	$this->assertEquals (1, $arrayList->indexOf ($person2));
    	$this->assertEquals (3, $arrayList->indexOf ($person3));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::isEmpty
     */
    public function testEmptyArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());
    	$this->assertEquals (0, $arrayList->size());

    	$arrayList->add ($person1);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (1, $arrayList->size());

    	$arrayList->add ($person2);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (2, $arrayList->size());

    	$arrayList->add ($person3);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (3, $arrayList->size());

    	// Permits duplicate elements
    	$arrayList->add ($person2);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (4, $arrayList->size());

    	// Removes every element
    	$arrayList->remove ($person3);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (3, $arrayList->size());

    	$arrayList->remove ($person2);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (2, $arrayList->size());

    	$arrayList->remove ($person1);
    	$this->assertFalse ($arrayList->isEmpty());
    	$this->assertEquals (1, $arrayList->size());

    	$arrayList->remove ($person2);
    	$this->assertTrue ($arrayList->isEmpty());
    	$this->assertEquals (0, $arrayList->size());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::iterator
     */
    public function testIterateOverArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertTrue ($arrayList->isEmpty());

    	foreach ($arrayList->iterator() as $element) {

    		// This code should not be executed
    		$this->assertTrue (FALSE);
    	}

    	// Adds $person1
    	$arrayList->add ($person1);
    	$this->assertEquals (1, $arrayList->size());

    	foreach ($arrayList->iterator() as $element)
    		$this->assertEquals ($person1, $element);

    	// Adds more persons
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);
    	$this->assertEquals (3, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2, $person3));

    	// Permits duplicate elements
    	$arrayList->add ($person1);
    	$this->assertEquals (4, $arrayList->size());
   		$this->checksOrdination ($arrayList, array ($person1, $person2, $person3
   				                                   ,$person1));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::lastIndexOf
     */
    public function testFindLastOccurrenceOfElements() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($person1));
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($person2));
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($person3));

    	$arrayList->add ($person1);
    	$this->assertEquals (0, $arrayList->lastIndexOf ($person1));
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($person2));
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($person3));

    	$arrayList->add ($person2);
    	$this->assertEquals (0, $arrayList->lastIndexOf ($person1));
    	$this->assertEquals (1, $arrayList->lastIndexOf ($person2));
    	$this->assertEquals (-1, $arrayList->lastIndexOf ($person3));

    	$arrayList->add ($person3);
    	$this->assertEquals (0, $arrayList->lastIndexOf ($person1));
    	$this->assertEquals (1, $arrayList->lastIndexOf ($person2));
    	$this->assertEquals (2, $arrayList->lastIndexOf ($person3));

    	// Adds $person1 again
    	$arrayList->add ($person1);
    	$this->assertEquals (3, $arrayList->lastIndexOf ($person1));
    	$this->assertEquals (1, $arrayList->lastIndexOf ($person2));
    	$this->assertEquals (2, $arrayList->lastIndexOf ($person3));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::remove
     */
    public function testRemoveElements() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertFalse ($arrayList->remove ($person1));
    	$this->assertFalse ($arrayList->remove ($person2));
    	$this->assertFalse ($arrayList->remove ($person3));

    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);
    	$this->assertEquals (3, $arrayList->size());

    	$this->assertTrue ($arrayList->remove ($person1));
    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person2, $person3));

    	// Adds $person2 again
    	$arrayList->add ($person2);
    	$this->checksOrdination ($arrayList, array ($person2, $person3, $person2));

    	$this->assertTrue ($arrayList->remove ($person2));
    	$this->assertEquals (2, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person3, $person2));

    	$this->assertFalse ($arrayList->remove ($person1));
    	$this->assertTrue ($arrayList->remove ($person2));
    	$this->assertTrue ($arrayList->remove ($person3));
    	$this->assertEquals (0, $arrayList->size());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::removeAll
     */
    public function testRemoveAllElementsOfGivenArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList1 = new ArrayList();
    	$arrayList2 = new ArrayList();
    	$this->assertFalse ($arrayList1->removeAll ($arrayList2));

    	$arrayList1->add ($person1);
    	$arrayList1->add ($person3);
    	$arrayList2->add ($person2);

    	$this->assertFalse ($arrayList1->removeAll ($arrayList2));
    	$this->assertEquals (2, $arrayList1->size());

    	$this->assertFalse ($arrayList2->removeAll ($arrayList1));
    	$this->assertEquals (1, $arrayList2->size());

    	// Adds $person1 twice
    	$arrayList1->add ($person1);
    	$arrayList2->add ($person1);

    	$this->assertEquals (3, $arrayList1->size());
    	$this->assertEquals (2, $arrayList2->size());
    	$this->checksOrdination ($arrayList1, array ($person1, $person3, $person1));
    	$this->checksOrdination ($arrayList2, array ($person2, $person1));

    	$this->assertTrue ($arrayList1->removeAll ($arrayList2));

    	$this->assertEquals (1, $arrayList1->size());
    	$this->assertEquals ($person3, $arrayList1->get(0));

    	$this->assertEquals (2, $arrayList2->size());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::removeAll
     */
    public function testRemoveAllElementsOfGivenHashSet() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$hashSet = new HashSet();
    	$this->assertFalse ($arrayList->removeAll ($hashSet));

    	$arrayList->add ($person1);
    	$arrayList->add ($person3);
    	$hashSet->add ($person2);

    	$this->assertFalse ($arrayList->removeAll ($hashSet));
    	$this->assertEquals (2, $arrayList->size());

    	// Adds $person1 twice
    	$arrayList->add ($person1);
    	$hashSet->add ($person1);

    	$this->assertTrue ($arrayList->removeAll ($hashSet));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($person3, $arrayList->get(0));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::removeAll
     */
    public function testRemoveAllElementsOfGivenSortedSet() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$sortedSet = new SortedSet();
    	$this->assertFalse ($arrayList->removeAll ($sortedSet));

    	$arrayList->add ($person1);
    	$arrayList->add ($person3);
    	$sortedSet->add ($person2);

    	$this->assertFalse ($arrayList->removeAll ($sortedSet));
    	$this->assertEquals (2, $arrayList->size());

    	// Adds $person1 twice
    	$arrayList->add ($person1);
    	$sortedSet->add ($person1);

    	$this->assertTrue ($arrayList->removeAll ($sortedSet));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($person3, $arrayList->get(0));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::removeAll
     */
    public function testRemoveAllElementsOfGivenPriorityQueue() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$priorityQueue = new PriorityQueue();
    	$this->assertFalse ($arrayList->removeAll ($priorityQueue));

    	$arrayList->add ($person1);
    	$arrayList->add ($person3);
    	$priorityQueue->add ($person2);

    	$this->assertFalse ($arrayList->removeAll ($priorityQueue));
    	$this->assertEquals (2, $arrayList->size());

    	// Adds $person1 twice
    	$arrayList->add ($person1);
    	$priorityQueue->add ($person1);

    	$this->assertEquals (3, $arrayList->size());
    	$this->assertEquals (2, $priorityQueue->size());

    	$this->assertTrue ($arrayList->removeAll ($priorityQueue));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($person3, $arrayList->get(0));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::removeByIndex
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testRemoveByNegativeIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->removeByIndex(-1);
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::removeByIndex
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testRemoveByIndexEqualsThanSize() {

    	$arrayList = new ArrayList();
    	$arrayList->add (new Person ("John", 18, TRUE));

    	$arrayList->removeByIndex ($arrayList->size());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::removeByIndex
     */
    public function testRemoveElementsByIndex() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);
        $person4 = new Person ("Mark", 30, TRUE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);
    	$arrayList->add ($person4);

    	// Removes $person4
    	$this->assertEquals ($person4, $arrayList->removeByIndex(3));
    	$this->assertEquals (3, $arrayList->size());

    	$this->assertEquals ($person1, $arrayList->get(0));
    	$this->assertEquals ($person2, $arrayList->get(1));
    	$this->assertEquals ($person3, $arrayList->get(2));

    	// Removes $person2
    	$this->assertEquals ($person2, $arrayList->removeByIndex(1));
    	$this->assertEquals (2, $arrayList->size());

    	$this->assertEquals ($person1, $arrayList->get(0));
    	$this->assertEquals ($person3, $arrayList->get(1));

    	// Removes $person1
    	$this->assertEquals ($person1, $arrayList->removeByIndex(0));
    	$this->assertEquals (1, $arrayList->size());

    	$this->assertEquals ($person3, $arrayList->get(0));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::retainAll
     */
    public function testRetainAllElementsOfGivenArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList1 = new ArrayList();
    	$arrayList2 = new ArrayList();
    	$this->assertFalse ($arrayList1->retainAll ($arrayList2));

    	// Retains all elements of an empty list
    	$arrayList1->add ($person1);
    	$this->assertTrue ($arrayList1->retainAll ($arrayList2));
    	$this->assertTrue ($arrayList1->isEmpty());

    	// Both lists shared $person1
    	$arrayList1->add ($person1);
    	$arrayList1->add ($person2);
    	$arrayList2->add ($person1);

    	$this->assertEquals (2, $arrayList1->size());
    	$this->assertEquals (1, $arrayList2->size());

    	$this->assertTrue ($arrayList1->retainAll ($arrayList2));
    	$this->assertEquals (1, $arrayList1->size());
    	$this->assertEquals ($person1, $arrayList1->get(0));

    	// Both lists have the same elements
    	$arrayList1->add ($person2);
    	$arrayList2->add ($person2);

    	$this->assertEquals (2, $arrayList1->size());
    	$this->assertEquals (2, $arrayList2->size());
    	$this->checksOrdination ($arrayList1, array ($person1, $person2));
    	$this->checksOrdination ($arrayList2, array ($person1, $person2));

    	$this->assertFalse ($arrayList1->retainAll ($arrayList2));
    	$this->assertEquals (2, $arrayList1->size());
    	$this->assertEquals (2, $arrayList2->size());

    	$this->checksOrdination ($arrayList1, array ($person1, $person2));

    	// Permits dulicate elements
    	$arrayList1->add ($person2);
    	$arrayList2->add ($person3);

    	$this->assertEquals (3, $arrayList1->size());
    	$this->assertEquals (3, $arrayList2->size());
    	$this->checksOrdination ($arrayList1, array ($person1, $person2, $person2));
    	$this->checksOrdination ($arrayList2, array ($person1, $person2, $person3));

    	$this->assertFalse ($arrayList1->retainAll ($arrayList2));
    	$this->assertEquals (3, $arrayList1->size());
    	$this->checksOrdination ($arrayList1, array ($person1, $person2, $person2));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::retainAll
     */
    public function testRetainAllElementsOfGivenHashSet() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$hashSet = new HashSet();
    	$this->assertFalse ($arrayList->retainAll ($hashSet));

    	// Retains all elements of an empty set
    	$arrayList->add ($person1);
    	$this->assertTrue ($arrayList->retainAll ($hashSet));
    	$this->assertTrue ($arrayList->isEmpty());

    	// Both collections shared $person1
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$hashSet->add ($person1);

    	$this->assertEquals (2, $arrayList->size());
    	$this->assertEquals (1, $hashSet->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2));

    	$this->assertTrue ($arrayList->retainAll ($hashSet));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($person1, $arrayList->get(0));

    	// Both collections have the same elements
    	$arrayList->add ($person2);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);
    	$hashSet->add ($person2);
    	$hashSet->add ($person3);

    	$this->assertEquals (4, $arrayList->size());
    	$this->assertEquals (3, $hashSet->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2, $person2
    			                                   ,$person3));

    	$this->assertFalse ($arrayList->retainAll ($hashSet));
    	$this->assertEquals (4, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2, $person2
    			                                   ,$person3));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::retainAll
     */
    public function testRetainAllElementsOfGivenSortedSet() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$sortedSet = new SortedSet();
    	$this->assertFalse ($arrayList->retainAll ($sortedSet));

    	// Retains all elements of an empty set
    	$arrayList->add ($person1);
    	$this->assertTrue ($arrayList->retainAll ($sortedSet));
    	$this->assertTrue ($arrayList->isEmpty());

    	// Both collections shared $person1
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$sortedSet->add ($person1);

    	$this->assertEquals (2, $arrayList->size());
    	$this->assertEquals (1, $sortedSet->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2));

    	$this->assertTrue ($arrayList->retainAll ($sortedSet));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($person1, $arrayList->get(0));

    	// Both collections have the same elements
    	$arrayList->add ($person2);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);
    	$sortedSet->add ($person2);
    	$sortedSet->add ($person3);

    	$this->assertEquals (4, $arrayList->size());
    	$this->assertEquals (3, $sortedSet->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2, $person2
    			                                   ,$person3));

    	$this->assertFalse ($arrayList->retainAll ($sortedSet));
    	$this->assertEquals (4, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2, $person2
    			                ,$person3));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::retainAll
     */
    public function testRetainAllElementsOfGivenPriorityQueue() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$priorityQueue = new PriorityQueue();
    	$this->assertFalse ($arrayList->retainAll ($priorityQueue));

    	// Retains all elements of an empty set
    	$arrayList->add ($person1);
    	$this->assertTrue ($arrayList->retainAll ($priorityQueue));
    	$this->assertTrue ($arrayList->isEmpty());

    	// Both collections shared $person1
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$priorityQueue->add ($person1);

    	$this->assertEquals (2, $arrayList->size());
    	$this->assertEquals (1, $priorityQueue->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2));

    	$this->assertTrue ($arrayList->retainAll ($priorityQueue));
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertEquals ($person1, $arrayList->get(0));

    	// Both collections have the same elements
    	$arrayList->add ($person2);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);
    	$priorityQueue->add ($person2);
    	$priorityQueue->add ($person3);

    	$this->assertEquals (4, $arrayList->size());
    	$this->assertEquals (3, $priorityQueue->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2, $person2
    			                                   ,$person3));

    	$this->assertFalse ($arrayList->retainAll ($priorityQueue));
    	$this->assertEquals (4, $arrayList->size());
    	$this->checksOrdination ($arrayList, array ($person1, $person2, $person2
    			                                   ,$person3));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::set
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testSetByNegativeIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->set (-1, new Person ("John", 18, TRUE));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::set
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testSetByIndexEqualsThanSize() {

    	$person = new Person ("John", 18, TRUE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($person);

    	$arrayList->set ($arrayList->size(), $person);
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::set
     */
    public function testSetElementsByIndex() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);

    	// Replaces $person1 by $person3
    	$oldElement = $arrayList->set (0, $person3);

    	$this->assertEquals ($person1, $oldElement);
    	$this->assertEquals (2, $arrayList->size());
    	$this->assertEquals ($person3, $arrayList->get(0));
    	$this->assertEquals ($person2, $arrayList->get(1));

    	// Replaces $person2 by $person3
    	$oldElement = $arrayList->set (1, $person3);

    	$this->assertEquals ($person2, $oldElement);
    	$this->assertEquals (2, $arrayList->size());
    	$this->assertEquals ($person3, $arrayList->get(0));
    	$this->assertEquals ($person3, $arrayList->get(1));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::size
     */
    public function testSizeOfArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$this->assertEquals (0, $arrayList->size());
    	$this->assertTrue ($arrayList->isEmpty());

    	$arrayList->add ($person1);
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	$arrayList->add ($person2);
    	$this->assertEquals (2, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	$arrayList->add ($person3);
    	$this->assertEquals (3, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	// Permits duplicate elements
    	$arrayList->add ($person2);
    	$this->assertEquals (4, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	// Removes every element
    	$arrayList->remove ($person2);
    	$this->assertEquals (3, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	$arrayList->remove ($person1);
    	$this->assertEquals (2, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	$arrayList->remove ($person2);
    	$this->assertEquals (1, $arrayList->size());
    	$this->assertFalse ($arrayList->isEmpty());

    	$arrayList->remove ($person3);
    	$this->assertEquals (0, $arrayList->size());
    	$this->assertTrue ($arrayList->isEmpty());
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::stream
     */
    public function testStreamOfArrayList() {

    	// Empty ArrayList
    	$arrayList = new ArrayList();
    	$stream = $arrayList->stream();

    	$this->assertNotNull ($stream);
    	$this->assertEquals (0, $stream->count());
    	$this->assertEmpty ($stream->toArray());

    	// Not empty ArrayList
    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);

    	$stream = $arrayList->stream();

    	$this->assertNotNull ($stream);
    	$this->assertEquals ($arrayList->size(), $stream->count());
    	$this->assertNotEmpty ($stream->toArray());

    	$contentOfStream = $stream->toArray();

    	for ($i = 0; $i < count ($contentOfStream); $i++)
    		$this->assertTrue ($arrayList->contains ($contentOfStream[$i]));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::subList
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testSubListByNegativeFromIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->add (new Person ("John", 18, TRUE));

    	$arrayList->subList (-1, 0);
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::subList
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testSubListByToIndexGreaterThanSize() {

    	$arrayList = new ArrayList();
    	$arrayList->add (new Person ("John", 18, TRUE));

    	$arrayList->subList (0, $arrayList->size() + 1);
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::subList
     *
     * @expectedException FunctionalPHP\exception\IllegalArgumentException
     */
    public function testSubListByFromIndexGreaterThanToIndex() {

    	$arrayList = new ArrayList();
    	$arrayList->add (new Person ("John", 18, TRUE));

    	$arrayList->subList (1, 0);
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::subList
     */
    public function testSubListOfArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();
    	$arrayList->add ($person1);
    	$arrayList->add ($person2);
    	$arrayList->add ($person3);

    	// Returns empty list
    	$arraySubList = $arrayList->subList (1, 1);
    	$this->assertTrue ($arraySubList->isEmpty());

    	// Returns non empty list
    	$arraySubList = $arrayList->subList (0, 1);
    	$this->assertEquals (1, $arraySubList->size());
    	$this->assertEquals ($person1, $arraySubList->get(0));

    	// Returns the last two elements
    	$arraySubList = $arrayList->subList (1, $arrayList->size());
    	$this->assertEquals (2, $arraySubList->size());
    	$this->assertEquals ($person2, $arraySubList->get(0));
    	$this->assertEquals ($person3, $arraySubList->get(1));

    	// Returns all elements
    	$arraySubList = $arrayList->subList (0, $arrayList->size());
    	$this->assertEquals ($arrayList->size(), $arraySubList->size());
    	$this->assertEquals ($person1, $arraySubList->get(0));
    	$this->assertEquals ($person2, $arraySubList->get(1));
    	$this->assertEquals ($person3, $arraySubList->get(2));
    }


    /**
     * @covers FunctionalPHP\iterable\collection\lists\ArrayList::toArray
     */
    public function testToArrayOfArrayList() {

    	$person1 = new Person ("John", 18, TRUE);
    	$person2 = new Person ("Sara", 25, FALSE);
    	$person3 = new Person ("Mary", 20, FALSE);

    	$arrayList = new ArrayList();

    	$array = $arrayList->toArray();
    	$this->assertTrue (is_array ($array));
    	$this->assertEquals (0, count ($array));

    	$arrayList->add ($person1);
    	$arrayList->add ($person3);
    	$arrayList->add ($person2);
    	$arrayList->add ($person1);

    	$array = $arrayList->toArray();
    	$this->assertTrue (is_array ($array));
    	$this->assertEquals (4, count ($array));

    	$this->assertEquals ($person1, $array[0]);
    	$this->assertEquals ($person3, $array[1]);
    	$this->assertEquals ($person2, $array[2]);
    	$this->assertEquals ($person1, $array[3]);
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