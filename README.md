# FunctionalPHP

- [Why this project was created?](#why-this-project-was-created)
- [Elements included in this project](#elements-included-in-this-project)
    - [Iterable](#iterable)
    - [Common classes](#common-classes)
    - [Functional programming features](#functional-programming-features)
- [Iterable in more detail](#iterable-in-more-detail)
- [Basic use case](#basic-use-case)
    - [Previous definitions](#previous-definitions)
    - [Basic use of Iterable](#basic-use-of-iterable)
    - [Basic use of Predicate](#basic-use-of-predicate)    
    - [Basic use of Stream](#basic-use-of-stream)
    - [Basic use of Collectors](#basic-use-of-collectors)

## Why this project was created?

The main purpose of this module is to help to PHP 7/Java 8 developers use functional programming in a similar way in both languages. So, despite the differences between Java and PHP, a Java 8 developer will be comfortable when he/she wants to use functional programming in PHP 7, and a PHP 7 developer that uses this module won't need to learn a lot of things and new concepts when he/she wants to use functional programming in Java 8.

## Elements included in this project

Below is shown a brief introduction to the components included in this project:

### Iterable

* **ArrayList**: implementation of a list of elements (*permits duplicates*).
* **HashMap**: hash table used to store key-value elements (*duplicate keys are not allowed*).
* **HashSet**: implementation of a set of elements (*does not permit duplicates*).
* **PriorityQueue**: implementation of a queue that further provides a total ordering on its elements using its compareTo method (see **Comparable** interface) or an instance of **Comparator** (*permits duplicates*).
* **SortedSet**: implementation of a set of ordered elements using its compareTo method (see **Comparable** interface) or an instance of **Comparator** (*does not permit duplicates*).

### Common classes

* **Comparable**: this interface imposes a total ordering on the Objects of each class that implements it.
* **Comparator**: a comparison function, which imposes a total ordering on some collection of Objects.
* **Object**: root class of the hierarchy, that is, we only will be able to use ArrayList, HashSet, etc to store elements with Object as superclass.
* **Optional**: a container object which may or may not contain a non-null value. Useful to know if we can to access to the properties/method of the contained object due to it is not NULL

### Functional programming features

* **Predicate**: represents a predicate (boolean-valued function) of one argument.
* **CompositePredicate**: complex **Predicate** on which we can add several logical conditions like: AND, OR, etc
* **Stream**: interface used to provide of functional programming features to the collections: **ArrayList**, **HashSet**, **PriorityQueue** and **SortedSet**.
* **BasicStream**: implementation of the **Stream** interface.
* **Collectors**: utility class that implements various useful reduction operations, such as accumulating elements into collections, summarizing elements according to various criteria, etc.

## Iterable in more detail

Now it is necessary to learn more about the **Iterable** hierarchy for this reason, the first thing is to see an image with all interfaces and classes included in the Iterable module: 

![Alt text](/documentation/IterableUML.png?raw=true "Hierarchy of Iterable")

As you can see it is a little bit complex than the "list of iterables" in the previous section however, we only need to know (and use) that previous list: **ArrayList**, **HashMap**, **HashSet**, **PriorityQueue** and **SortedSet**, that is, the final classes.

## Basic use case

### Previous definitions

Firstly, we will define some example classes that we will use to explain what we can do with this module.

```php
<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\Object;
use FunctionalPHP\iterable\collection\lists\Lists;
use FunctionalPHP\iterable\collection\lists\ArrayList;

/**
 * Subclass of Object used only for testing purpose.
 */
class Car extends Object {

	protected $registration;
	protected $yearOfProduction;


	public function __construct (string $registration, int $yearOfProduction) {

		$this->registration = $registration;
		$this->yearOfProduction = $yearOfProduction;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::equals()
	 */
	public function equals (Object $otherCar): bool {
		return strcmp ($this->registration, $otherCar->registration) == 0;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::hashCode()
	 */
	public function hashCode(): int {
		return $this->yearOfProduction % 5;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::compareTo()
	 */
	public function compareTo (Object $otherCar): int {
		return strcmp ($this->registration, $otherCar->registration);
	}

}


/**
 * Subclass of Object used only for testing purpose.
 */
class Person extends Object {

	protected $name;
	protected $age;
	protected $isMale;
	protected $cars;

	public function __construct (string $name, int $age, bool $isMale, Lists $cars = NULL) {
		$this->name   = $name;
		$this->age    = $age;
		$this->isMale = $isMale;

		$this->cars = (is_null ($cars) ? new ArrayList() : $cars);
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::equals()
	 */
	public function equals (Object $otherPerson): bool {

		return (strcmp ($this->name, $$otherPerson->name) == 0) && 
		       $this->age == $otherPerson->age;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::hashCode()
	 */
	public function hashCode(): int {
		return $this->age % 5;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::compareTo()
	 */
	public function compareTo (Object $otherPerson): int {
		return strcmp ($this->name, $otherPerson->name);
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::__toString()
	 */
	public function __toString() {

		$resultString = "{".get_class ($this).": ";

		$properties = get_object_vars ($this);
		if (is_array ($properties)) {

			// Adds property name and property value to the result
			foreach ($properties as $name => $value) {

				$resultString .= "\n {$name} = ";

				if (strcmp ($name, "cars") == 0) {

					$resultString .= "[";
					if (!is_null ($value)) {
						foreach ($value as $car)
							$resultString .= $car."\n";
					}
					$resultString .= "]";
				}
				else
					$resultString .= $value;
			}
		}
		return $resultString."}";
	}

}

?>
```

We have defined a subclass of **Object** with two properties. As you can see, the method *compareTo* is used to compare the current **Person** with another one. For this reason  we are going to define an instance of **Comparator** used to compare two **Person**s at the inverse way as *compareTo* method (used for testing purpose):


```php
<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;


/**
 * Comparator instance used to compare two different Person objects, used only for testing purpose.
 */
class PersonComparator implements Comparator {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Comparator::compare()
	 */
	public function compare (Object $person1, Object $person2): int {

		/**
		 *    Assumes Person as parameters and returns the "inverse result" that
		 * $person1->compareTo ($person2)
		 */
		return strcmp ($person2->name, $person1->name);
	}

}


/**
 * Comparator instance used to compare two different Car objects, used only for testing purpose.
 */
class CarComparator implements Comparator {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Comparator::compare()
	 */
	public function compare (Object $person1, Object $person2): int {

		/**
		 *    Assumes Car as parameters and returns the "inverse result" that
		 * $car1->compareTo ($car2)
		 */
		return strcmp ($car2->registration, $car1->registration);
	}

}

?>
```

The last "previous step" is to define some **Predicates** that we will use when we explain the functional programming:

```php
<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\functional\Predicate;
use FunctionalPHP\exception\IllegalArgumentException;
use FunctionalPHP\test\Person;


/**
 * Uses to test if the given arguments are integer values (used only for testing purpose).
 */
final class IsIntPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) == 0)
			return FALSE;

		foreach ($args as $a) {

			if (is_int ($a) === FALSE)
				return FALSE;
		}
		return TRUE;
	}
}


/**
 * Uses to test if the given arguments are integer values (used only for testing purpose).
 */
final class IsIntAndPairPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) == 0)
			return FALSE;

			foreach ($args as $a) {

				if (is_int ($a) === FALSE || ($a % 2 != 0))
					return FALSE;
			}
			return TRUE;
	}
}


/**
 * Uses to test if the name of the given Person has more than one word (used only for testing purpose).
 */
final class HasPersonMoreThanOneWordAsNamePredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export ($args));
		$person = $args[0];
		if ($person instanceof \Person)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of ".Person::class
					                               ." Its type is: ".gettype ($person));

		return (str_word_count ($person->name) > 1);
	}
}


/**
 * Uses to test if the age of the given Person is odd or not (used only for testing purpose).
 */
final class HasPersonOddAgePredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export ($args));
		$person = $args[0];
		if ($person instanceof \Person)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of ".Person::class
					                               ." Its type is: ".gettype ($person));

		return ($person->age % 2 != 0);
	}
}

?>
```

### Basic use of Iterable

Now we are going to learn how we can use classes like: **ArrayList**, **HashSet**, etc using the previous defined classes:

```php
<?php

namespace FunctionalPHP\test;

use FunctionalPHP\test\Person;
use FunctionalPHP\test\PersonComparator;
use FunctionalPHP\iterable\map\Map;
use FunctionalPHP\iterable\map\HashMap;
use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\set\SortedSet;

$person1 = new Person ("John Snow", 23, TRUE);
$person2 = new Person ("Peter Pan", 11, TRUE);
$personEqualsTo1 = new Person ("John Snow", 23, TRUE);


// ArrayList
$arrayList1 = new ArrayList();
$arrayList1->add ($person1);
$arrayList1->add ($personEqualsTo1);   // Permits duplicates

$arrayList2 = new ArrayList ($arrayList1);   // Contains the same elements of $arrayList1
$arrayList2->remove ($person2);              // Returns FALSE, $person2 does not exists in $arrayList2
$arrayList2->remove ($person1);              // Returns TRUE, now $arrayList2 only has one element: $personEqualsTo1


// SortedSet
$sortedSet1 = new SortedSet();
$sortedSet1->add ($person2);
$sortedSet1->add ($person1);

$sortedSet1->add ($personEqualsTo1);   // This object will not inserted in the SortedSet

$sortedSet2 = new SortedSet ($sortedSet1, new PersonComparator());

var_export ($sortedSet1->toArray());   // Returns the elements in the following order: $person1, $person2
var_export ($sortedSet2->toArray());   // Returns the elements in the following order: $person2, $person1


// We can iterate over any Iterable class using foreach loop
foreach ($sortedSet1->iterator() as $element) {

	echo "\nName property: ", $element->name;
	echo "\nAge property: ", $element->age;
}


// HashMap
$hashMap = new HashMap (Map::KEY_STRING_TYPE);
$hashMap->put ($person1->name, $person1);
$hashMap->put ($person2->name, $person2);

foreach ($hashMap->iterator() as $internalKey => $internalValue) {

	echo "\nKey of current element of Map: ", $internalKey;
	echo "\nValue of current element of Map: ", $internalValue->name, " - "
			                                  , $internalValue->age;
}
?>
```

### Basic use of Predicate

Now we are going to start to use the functional programming, beginning with the functionality related with **Predicate** and for this purpose will be used the previous example predicates:

```php
<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\functional\CompositePredicate;
use FunctionalPHP\test\IsIntAndPairPredicate;
use FunctionalPHP\test\IsIntPredicate;


$isIntOfEmpty = (new IsIntPredicate())->test();   // Return FALSE

$isIntOfString = (new IsIntPredicate())->test ("A");                // Return FALSE
$isIntOfSeveralStrings = (new IsIntPredicate())->test ("A", "B");   // Return FALSE

$isIntOfInteger = (new IsIntPredicate())->test(1);               // Return TRUE
$isIntOfSeveralIntegers = (new IsIntPredicate())->test (1, 2);   // Return TRUE

$isIntOfMixedValues = (new IsIntPredicate())->test ("A", 2);   // Return FALSE


// CompositePredicate
$isIntAndPairOfEmpty = (new CompositePredicate (new IsIntPredicate()))
                                         ->and (new IsIntAndPairPredicate())->test();                 // Return FALSE

$isIntAndPairOfStrings = (new CompositePredicate (new IsIntPredicate()))
                                           ->and (new IsIntAndPairPredicate())->test ("A", "B");      // Return FALSE

$isIntAndPairOfIntegers = (new CompositePredicate (new IsIntPredicate()))
                                            ->and (new IsIntAndPairPredicate())->test (1, 2);         // Return FALSE

$isIntAndPairOfPairIntegers = (new CompositePredicate (new IsIntPredicate()))
                                                ->and (new IsIntAndPairPredicate())->test (2, 4, 6);  // Return TRUE

$isIntXorPairOfPairIntegers = (new CompositePredicate (new IsIntPredicate()))
                                                ->xor (new IsIntAndPairPredicate())->test (2, 4);    // Return FALSE

$isIntXorPairOfMixedIntegers = (new CompositePredicate (new IsIntPredicate()))
                                                 ->xor (new IsIntAndPairPredicate())->test (1, 2);   // Return TRUE
?>
``` 

### Basic use of Stream

In the following section we will learn how to use the functionality provided by **Stream** interface, implemented by **BasicStream** class: 

```php
<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\Optional;
use FunctionalPHP\common\functional\BasicStream;

use FunctionalPHP\test\Car;
use FunctionalPHP\test\Person;
use FunctionalPHP\test\PersonComparator;
use FunctionalPHP\test\HasPersonOddAgePredicate;
use FunctionalPHP\test\HasPersonMoreThanOneWordAsNamePredicate;


$car1 = new Car ('A-2134', 2015);
$car2 = new Car ('B-9999', 2015);
$car3 = new Car ('C-4567', 2010);

$arrayListOfCars1 = new ArrayList();
$arrayListOfCars1->add ($car1);
$arrayListOfCars1->add ($car3);

$arrayListOfCars2 = new ArrayList();
$arrayListOfCars2->add ($car2);

$person1 = new Person ("Alba", 11, FALSE);
$person2 = new Person ("Albert", 18, TRUE, $arrayListOfCars2);
$person3 = new Person ("Bob", 9, TRUE, $arrayListOfCars1);
$person4 = new Person ("Clark Smith", 34, TRUE);
$person5 = new Person ("Dalia", 19, FALSE, $arrayListOfCars1);

$arrayListOfPersons = new ArrayList();
$arrayListOfPersons->add ($person1);
$arrayListOfPersons->add ($person2);
$arrayListOfPersons->add ($person3);
$arrayListOfPersons->add ($person4);
$arrayListOfPersons->add ($person5);


$arrayListOfPersons->stream()->allMatch (new HasPersonOddAgePredicate());   // Return FALSE
$arrayListOfPersons->stream()->anyMatch (new HasPersonOddAgePredicate());   // Return TRUE

$arrayListOfPersons->stream()->noneMatch (new HasPersonMoreThanOneWordAsNamePredicate());   // Return FALSE

// filter
$arrayListOfPersons->stream()->filter (new HasPersonOddAgePredicate())
                             ->toArray();   // Return [$person1, $person3, $person5]
                             
// filterByLambda
$arrayListOfPersons->stream()->filterByLambda (function (Person $person) : bool {
	                                              return strcmp ($person->name, "Clark Smith") == 0;
                                               })
                             ->toArray();   // Return [$person4]
                             
// forEach
$stream = $$arrayListOfPersons->stream();
$stream->forEach (function (Person $person) {
	                 $person->age *= 2;
                  });
$stream->toArray();                // Return an array on which all age values has been multiplied by 2
$$arrayListOfPersons->toArray();   // Return the same result.

// sortedByComparator
$arrayListOfPersons->stream()->sortedByComparator (new PersonComparator())
	                         ->toArray();   // Return [$person5, $person4, $person3, $person2, $person1]

// sortedByLambda
$arrayListOfPersons->stream()->sortedByLambda (function (Person $person1, Person $person2): int {
				                         return strcmp ($person1->name, $person2->name);
			                          })
			                 ->toArray();   // Return [$person1, $person2, $person3, $person4, $person5]

// map
$arrayListOfPersons->stream()->map (function (Person $person1) : int {
	                                   return $person1->age;
                                    })
                             ->toArray();   // Return [22, 36, 18, 68, 38] due to we have modified age in the previous forEach
                  
// flatMap                             
$arrayListOfPersons->stream()->flatMap (function (Person $person) : Stream {
                    	                   return $person->cars->stream();
                                        })
                             ->toArray();   // Return [$car2, $car1, $car3, $car1, $car3]
                             
// reduce
$result = $arrayListOfPersons->stream()->reduce (function (int $accumulatedValue, Person $person) : int {
	                                                return $person->age + $accumulatedValue;
                                                 }
		                                         ,0);   // Return 182, due to we have modified age in the previous forEach
	                
// And more complex examples
$arrayListOfPersons->clear();
$arrayListOfPersons->add ($person1);
$arrayListOfPersons->add ($person2);
$arrayListOfPersons->add ($person3);
$arrayListOfPersons->add ($person4);
$arrayListOfPersons->add ($person5);
$arrayListOfPersons->add ($person1);


// We want to get the list of different values of age (only odd values) with ascending ordination
$arrayListOfPersons->stream()->filter (new HasPersonOddAgePredicate())
                             ->map (function (Person $person) : int {
	                                   return $person->age;
                                    })
                             ->distinct()
                             ->sorted()
                             ->toArray();   // Return [9, 11, 19]
                             
// Now we want to know the distinct car's registrations of all persons
$arrayListOfPersons->stream()->flatMap (function (Person $person) : Stream {
			                               return $person->cars->stream()
			                                                   ->map (function (Car $car) : string {
			                                                             return $car->registration;
			                                                          });
		                                })
		                     ->distinct()
		                     ->toArray();   // Return ['B-9999', 'A-2134', 'C-4567']                          
                             
?>
```

It is important to have one thing in mind and we have seen in the previous example, due to a performance reason, **the elements from the "original Collection" are the same that we stored in the "destination Stream"**, that is, we don't create copies of them.

For this reason, *forEach* method modifies the elements stored in "both classes": original Collection and destination Stream. This does not happen anymore when *map* method changes the type of the stored elements in the Stream.

Java 8 works in the same way, but if you want you can change this behaviuor modifying how the elements of the **Collection** are stored in the **Stream** (in this case *BasicStream::_construct*)

### Basic use of Collectors

At this point we will learn how to use the useful functionality provided by **Collectiors** class, that allow us, for example, accumulating elements into collections or summarizing elements according to various criteria:

```php
<?php

namespace FunctionalPHP\test;

use FunctionalPHP\common\Optional;
use FunctionalPHP\common\functional\BasicStream;
use FunctionalPHP\common\functional\Collectors;

use FunctionalPHP\test\Car;
use FunctionalPHP\test\Person;
use FunctionalPHP\test\HasPersonOddAgePredicate;


$car1 = new Car ('A-2134', 2015);
$car2 = new Car ('B-9999', 2015);
$car3 = new Car ('C-4567', 2010);

$arrayListOfCars1 = new ArrayList();
$arrayListOfCars1->add ($car1);
$arrayListOfCars1->add ($car3);

$arrayListOfCars2 = new ArrayList();
$arrayListOfCars2->add ($car2);

$person1 = new Person ("Alba", 11, FALSE);
$person2 = new Person ("Albert", 18, TRUE, $arrayListOfCars2);
$person3 = new Person ("Bob", 9, TRUE, $arrayListOfCars1);
$person4 = new Person ("Clark Smith", 34, TRUE);
$person5 = new Person ("Dalia", 19, FALSE, $arrayListOfCars1);

$arrayListOfPersons = new ArrayList();
$arrayListOfPersons->add ($person1);
$arrayListOfPersons->add ($person2);
$arrayListOfPersons->add ($person3);
$arrayListOfPersons->add ($person4);
$arrayListOfPersons->add ($person5);


// toList
$arrayListOfAllCars = $arrayListOfPersons->stream()->flatMap (function (Person $person) : Stream {
	                                                             return $person->cars->stream();
                                                              })
                                                   ->collect (Collectors::toList());   // Return an ArrayList with: $car2, $car1, $car3, $car1, $car3
                                                   
// toSet
$hashSetOfAllCars = $arrayListOfPersons->stream()->flatMap (function (Person $person) : Stream {
	                                                           return $person->cars->stream();
                                                            })
                                                 ->collect (Collectors::toSet());   // Return a HashSet with: $car2, $car1, $car3
                                                 
// partitioningBy                                                 
$hashMap = $arrayListOfPersons->stream()->collect (Collectors::partitioningBy (new HasPersonOddAgePredicate()));

/**
 * Return a HashMap with the following information:
 *   
 *   $hashMap->get (FALSE) => return an Optional of ObjectWithIterable with the elements of the Stream that does not verify the given predicate  
 *   $hashMap->get (TRUE)  => return an Optional of ObjectWithIterable with the elements of the Stream that verify the given predicate
 */
$arrayListOfPersonDoesNotVerifyPredicate = $hashMap->get (FALSE)->get()->getIterable();   // Return an ArrayList with: $person2, $person4
$arrayListOfPersonVerifyPredicate        = $hashMap->get (TRUE)->get()->getIterable();    // Return an ArrayList with: $person1, $person3, $person5, $person1
 

// partitioningByUsingCollection                                                 
$hashMap = $arrayListOfPersons->stream()->collect (Collectors::partitioningByUsingCollection (new HasPersonOddAgePredicate(), new HashSet()));

$hashSetOfPersonDoesNotVerifyPredicate = $hashMap->get (FALSE)->get()->getIterable();   // Return a HashSet with: $person2, $person4
$hashSetOfPersonVerifyPredicate        = $hashMap->get (TRUE)->get()->getIterable();    // Return a HashSet with: $person1, $person3, $person5
           
// groupingBy
$hashMap = $arrayListOfPersons->stream()->flatMap (function (Person $person) : Stream {
	                                                  return $person->cars->stream();
                                                   })
                                        ->collect (Collectors::groupingBy (function (Car $car) : int {
                                        	                                  return $car->yearOfProduction;
                                                                           }));
/**
 * Return a HashMap with the following information:
 *   
 *   $hashMap->get (2010) => return an Optional of ObjectWithIterable with the cars with a yearOfProduction = 2010
 *   $hashMap->get (2015) => return an Optional of ObjectWithIterable with the cars with a yearOfProduction = 2015
 */
$arrayListOfCarsOf2010 = $hashMap->get (2010)->get()->getIterable();   // Return an ArrayList with: $car3, $car3
$arrayListOfCarsOf2015 = $hashMap->get (2015)->get()->getIterable();   // Return an ArrayList with: $car2, $car1, $car1

// groupingByUsingCollection
$hashMap = $arrayListOfPersons->stream()->flatMap (function (Person $person) : Stream {
	                                                  return $person->cars->stream();
                                                   })
                                        ->collect (Collectors::groupingByUsingCollection (function (Car $car) : int {
	                                                                                         return $car->yearOfProduction;
                                                                                          }
                                                                                         ,new HashSet()));

$hashSetOfCarsOf2010 = $hashMap->get (2010)->get()->getIterable();   // Return a HashSet with: $car3
$hashSetOfCarsOf2015 = $hashMap->get (2015)->get()->getIterable();   // Return a HashSet with: $car2, $car1

?>
```