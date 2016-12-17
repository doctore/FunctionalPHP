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

## Iterable in more detail

Now it is necessary to learn more about the **Iterable** hierarchy for this reason, the first thing is to see an image with all interfaces and classes included in the Iterable module: 

![Alt text](/documentation/IterableUML.png?raw=true "Hierarchy of Iterable")

As you can see it is a little bit complex than the "list of iterables" in the previous section however, we only need to know (and use) that previous list: **ArrayList**, **HashMap**, **HashSet**, **PriorityQueue** and **SortedSet**, that is, the final classes.

## Basic use case

### Previous definitions

Firstly, we will define some example classes that we will use to explain what we can do with this module.

```php
<?php

namespace FunctionalPHP\example;

use FunctionalPHP\common\Object;

/**
 * Subclass of Object used in this example (used only for testing purpose).
 */
class DummyObject extends Object {

	protected $intProperty;
	protected $stringProperty;

	public function __construct (int $intValue, string $stringValue) {
		$this->intProperty    = $intValue;
		$this->stringProperty = $stringValue;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::equals()
	 */
	public function equals (DummyObject $object): bool {
		return $this->intProperty == $object->intProperty &&
			   $this->stringProperty == $object->stringProperty;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::hashCode()
	 */
	public function hashCode(): int {e tests related with collections and maps
		return $this->intProperty % 10;
	}

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Object::compareTo()
	 */
	public function compareTo (Object $object): int {
		return $this->intProperty - $object->intProperty;
	}
}

?>
```

We have defined a "dummy subclass" of **Object** with two properties. As you can see, the method *compareTo* is used to compare the current **DummyObject** with another one. For this reason  we are going to define an instance of **Comparator** used to compare two **DummyObject**s at the inverse way as *compareTo* method (used for testing purpose):


```php
<?php

namespace FunctionalPHP\example;

use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;

/**
 * Comparator instance used to compare two different DummyObjects (used only for testing purpose).
 */
class DummyObjectComparator implements Comparator {


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\Comparator::compare()
	 */
	public function compare (Object $object1, Object $object2): int {

		/**
		 *    Assumes DummyObjects as parameters and returns the "inverse result" than
		 * $object1->compareTo ($object2)
		 */
		return $object2->intProperty - $object1->intProperty;
	}
}

?>
```

The last "previous step" is to define some **Predicates** that we will use when we explain the functional programming:

```php
<?php

namespace FunctionalPHP\example;

use FunctionalPHP\common\functional\Predicate;
use FunctionalPHP\exception\IllegalArgumentException;
use FunctionalPHP\example\DummyObject;


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
 * Uses to test if the intProperty of the given DummyObject is odd or not (used only for testing purpose).
 */
final class HasDummyObjectOddIntPropertyPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export($args));
		$dummyObject = $args[0];
		if ($dummyObject instanceof \DummyObject)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of DummyObject. ".DummyObject::class
					                               ." Its type is: ".gettype ($dummyObject));

		return ($dummyObject->intProperty % 2 != 0);
	}
}


/**
 * Uses to test if the stringProperty of the given DummyObject has two characters (used only for testing purpose).
 */
final class HasDummyObjectStringPropertyOfTwoCharactersPredicate implements Predicate {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Predicate::test()
	 */
	public function test (...$args) : bool {

		if (count ($args) != 1)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The method has received more than one argument: "
					                               .var_export($args));
		$dummyObject = $args[0];
		if ($dummyObject instanceof \DummyObject)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given parameter is not an instance of DummyObject. "
					                               ." Its type is: ".gettype ($dummyObject));

		return (strlen ($dummyObject->stringProperty)  == 2);
	}
}

?>
```

### Basic use of Iterable

Now we are going to learn how we can use classes like: **ArrayList**, **HashSet**, etc using the previous defined classes:

```php
<?php

namespace FunctionalPHP\example;

use FunctionalPHP\example\DummyObject;
use FunctionalPHP\example\DummyObjectComparator;
use FunctionalPHP\iterable\map\Map;
use FunctionalPHP\iterable\map\HashMap;
use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\set\SortedSet;

$dummyObject1 = new DummyObject (1, "a");
$dummyObject2 = new DummyObject (2, "bb");
$dummyObjectEqualsTo1 = new DummyObject (1, "a");


// ArrayList
$arrayList1 = new ArrayList();
$arrayList1->add ($dummyObject1);
$arrayList1->add ($dummyObjectEqualsTo1);   // Permits duplicates

$arrayList2 = new ArrayList ($arrayList1);   // Contains the same elements of $arrayList1
$arrayList2->remove ($dummyObject2);         // Returns FALSE, $dummyObject2 does not exists in $arrayList2
$arrayList2->remove ($dummyObject1);         // Returns TRUE, now $arrayList2 only has one element: $dummyObjectEqualsTo1


// SortedSet
$sortedSet1 = new SortedSet();
$sortedSet1->add ($dummyObject2);
$sortedSet1->add ($dummyObject1);
$sortedSet1->add ($dummyObjectEqualsTo1);   // This object will not inserted in the SortedSet

$sortedSet2 = new SortedSet ($sortedSet1, new DummyObjectComparator());

var_export ($sortedSet1->toArray());   // Returns the elements in the following order: $dummyObject1, $dummyObject2
var_export ($sortedSet2->toArray());   // Returns the elements in the following order: $dummyObject2, $dummyObject1


// We can iterate over any Iterable class using foreach loop
foreach ($sortedSet1->iterator() as $element) {

   echo "Int property", $element->intProperty;
   echo "String property", $element->stringProperty;
}


// HashMap
$hashMap = new HashMap (Map::KEY_STRING_TYPE);
$hashMap->put ($dummyObject1->stringProperty, $dummyObject1);
$hashMap->put ($dummyObject2->stringProperty, $dummyObject2);

foreach ($hashMap->iterator() as $internalKey => $internalValue) {

    echo "\nKey of current element of Map", $internalKey;
    echo "\nValue of current element of Map: ",$internalValue->intProperty, " - "
                                              ,$internalValue->stringProperty;
}
?>
```

### Basic use of Predicate

Now we are going to start to use the functional programming, beginning with the functionality related with **Predicate** and for this purpose will be used the previous example predicates:

```php
<?php

namespace FunctionalPHP\example;

use FunctionalPHP\common\functional\CompositePredicate;
use FunctionalPHP\example\IsIntAndPairPredicate;
use FunctionalPHP\example\IsIntPredicate;


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

In the last section of this file we will learn how to use the functionality provided by **Stream** interface, implemented by **BasicStream** class: 

```php
<?php

namespace FunctionalPHP\example;

use FunctionalPHP\common\Optional;
use FunctionalPHP\common\functional\BasicStream;
use FunctionalPHP\example\DummyObject;
use FunctionalPHP\example\DummyObjectComparator;
use FunctionalPHP\example\HasDummyObjectOddIntPropertyPredicate;
use FunctionalPHP\example\HasDummyObjectStringPropertyOfTwoCharactersPredicate;


$dummyObject1 = new DummyObject (1, "a");
$dummyObject2 = new DummyObject (2, "b");
$dummyObject3 = new DummyObject (3, "c");
$dummyObject4 = new DummyObject (4, "d");

$arrayList = new ArrayList();
$arrayList->add ($dummyObject1);
$arrayList->add ($dummyObject2);
$arrayList->add ($dummyObject3);
$arrayList->add ($dummyObject4);

$arrayList->stream()->allMatch (new HasDummyObjectOddIntPropertyPredicate());   // Return FALSE
$arrayList->stream()->anyMatch (new HasDummyObjectOddIntPropertyPredicate());   // Return TRUE

$arrayList->stream()->noneMatch (new HasDummyObjectStringPropertyOfTwoCharactersPredicate());   // Return TRUE



$arrayList->stream()->filter (new HasDummyObjectOddIntPropertyPredicate())
                    ->toArray();   // Return [$dummyObject1, $dummyObject3]                    

$arrayList->stream()->filterByLambda (function (DummyObject $dummyObject) : bool {
                              	                   return strcmp ($dummyObject->stringProperty, "a") == 0;
                                                })
                    ->toArray();   // Return [$dummyObject1]
                    
$arrayList->stream()->sortedByComparator (new DummyObjectComparator())
                    ->toArray();   // Return [$dummyObject4, $dummyObject3, $dummyObject2, $dummyObject1]
                    
                    
$stream = $arrayList->stream();
$stream->forEach (function (DummyObject $dummyObject) {
	                 $dummyObject->intProperty *= 2;
                  });
$stream->toArray();   // Return an array on which all intProperty values has been multiplied by 2
                    
                    
$arrayList->stream()->sortedByLambda (function (DummyObject $dummyObject1, DummyObject $dummyObject2): int {
		                                 return $dummyObject1->intProperty - $dummyObject2->intProperty;
	                                  })
	                ->toArray();   // Return [$dummyObject1, $dummyObject2, $dummyObject3, $dummyObject4]
                    

$arrayList->stream()->map (function (DummyObject $dummyObject) : int {
			                            return $dummyObject->intProperty;
		                   })
	                ->toArray();   // Return [2, 4, 6, 8] due to we have modified intProperty in the previous forEach
	                
	                
// And a last more complex example
$arrayList->clear();
$arrayList->add ($dummyObject1);
$arrayList->add ($dummyObject2);
$arrayList->add ($dummyObject3);
$arrayList->add ($dummyObject4);
$arrayList->add ($dummyObject1);

// We want to get the list of different values of intProperty (only odd values) with ascending ordination
$arrayList->stream()->filter (new HasDummyObjectOddIntPropertyPredicate())
                    ->map (function (DummyObject $dummyObject) : int {
			                  return $dummyObject->intProperty;
		                   })
		            ->distinct()
		            ->sorted()
		            ->toArray();   // Return [1, 3]
?>
```


