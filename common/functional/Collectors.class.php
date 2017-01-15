<?php

namespace FunctionalPHP\common\functional;

use FunctionalPHP\common\Object;
use FunctionalPHP\common\ObjectWithIterable;
use FunctionalPHP\common\functional\Predicate;

use FunctionalPHP\common\util\ReflectionUtil;
use FunctionalPHP\common\util\ReflectionFunctionInformation;

use FunctionalPHP\exception\UnsupportedOperationException;

use FunctionalPHP\iterable\Iterable;

use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\queue\PriorityQueue;
use FunctionalPHP\iterable\collection\set\HashSet;
use FunctionalPHP\iterable\collection\set\SortedSet;

use FunctionalPHP\iterable\map\HashMap;
use FunctionalPHP\iterable\map\Map;
use FunctionalPHP\common\Optional;


/**
 *    Utility class that implements various useful reduction operations, such as accumulating elements
 * into collections, summarizing elements according to various criteria, etc.
 */
final class Collectors {

	// Used to check that given class name of several methods is a valid implementation of Collection interface
	const VALID_COLLECTION_IMPLEMENTATIONS  = array (ArrayList::class, HashSet::class, PriorityQueue::class, SortedSet::class);

	// Permited "returned types" of the closure function received groupingBy as parameter
	const VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_GROUPINGBY = array ("bool", "float", "int", "string");


	/**
	 * Returns a CollectorImpl used to accumulate the input elements into a Lists.
	 *
	 * @return CollectorImpl
	 *    Object with the information used to know how to accumulate several elements inside a Lists
	 */
	public static function toList() : CollectorImpl {

		$collectorImpl = new CollectorImpl (new ArrayList());
		$collectorImpl->setAccumulator (function (Object $object) use (&$collectorImpl) {
			                               $collectorImpl->getSupplier()->add ($object);
	 	                                });
		return $collectorImpl;
	}


	/**
	 * Returns a CollectorImpl used to accumulate the input elements into a Set.
	 *
	 * @return CollectorImpl
	 *    Object with the information used to know how to accumulate several elements inside a Set
	 */
	public static function toSet() : CollectorImpl {

		$collectorImpl = new CollectorImpl (new HashSet());
		$collectorImpl->setAccumulator (function (Object $object) use (&$collectorImpl) {
			                               $collectorImpl->getSupplier()->add ($object);
		                                });
		return $collectorImpl;
	}


	/**
	 *    Returns a CollectorImpl which partitions the input elements according to a Predicate, and
	 * organizes them into a HashMap with Map::KEY_BOOLEAN_TYPE as key and ObjectWithIterable as
	 * value (whose innerIterable property is an ArrayList)
	 *
	 * @param Predicate $predicate
	 *    Predicate used for classifying input elements.
	 *
	 * @return CollectorImpl
	 *    Object with the information used to know how to accumulate several elements inside a HashMap
	 */
	public static function partitioningBy (Predicate $predicate) : CollectorImpl {

		return Collectors::partitioningByUsingCollection ($predicate, new ArrayList());
	}


	/**
	 *    Returns a CollectorImpl which partitions the input elements according to a Predicate, and
	 * organizes them into a HashMap with Map::KEY_BOOLEAN_TYPE as key and ObjectWithIterable as
	 * value (whose innerIterable property is the given collection)
	 *
	 * @param Predicate $predicate
	 *    Predicate used for classifying input elements.
	 * @param Collection $collection
	 *    An instance of a class that implements Collection interface used to store the returned values
	 *
	 * @return CollectorImpl
	 *    Object with the information used to know how to accumulate several elements inside a HashMap
	 *
	 * @throws UnsupportedOperationException if the given $collection is not a valid implementation of
	 *                                       Collection interface (see Collectiors::VALID_COLLECTION_IMPLEMENTATIONS)
	 */
	public static function partitioningByUsingCollection (Predicate $predicate, Collection $collection) : CollectorImpl {

		if (!Collectors::isValidImplementationOfCollection ($collection))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                               ,"The given collection is not a valid implementation of interface ".Collection::class
					                                   .", its type is ".get_class ($collection).". Only the following implementations "
					                                   ."are allowed: ".var_export (Collectors::VALID_COLLECTION_IMPLEMENTATIONS, TRUE));

		$collectorImpl = new CollectorImpl (new HashMap (Map::KEY_BOOLEAN_TYPE));

		// Creates the collections used to store values that verify (and not) the given predicate
		$objectsVerifyPredicate    = new ObjectWithIterable ($collection);
		$objectsNotVerifyPredicate = new ObjectWithIterable (clone $collection);

		$collectorImpl->getSupplier()->put (FALSE, $objectsVerifyPredicate);
		$collectorImpl->getSupplier()->put (TRUE, $objectsNotVerifyPredicate);

		// Defines the function used to filter the given values
		$collectorImpl->setAccumulator (function (Object $object) use (&$collectorImpl, &$predicate) {

			                               $keyValue         = $predicate->test ($object) ? TRUE : FALSE;
			                               $collectionValues = $collectorImpl->getSupplier()->get ($keyValue)->get()->getIterable();
			                               $collectionValues->add ($object);
		                                });
		return $collectorImpl;
	}


	/**
	 *    Returns a CollectorImpl implementing a "group by" operation on input elements according
	 * to a Closure function. And organizes them into a HashMap with a key of the same type returned
	 * by given function and ObjectWithIterable as value (whose innerIterable property is an ArrayList).
	 * The given function must satisfies the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to Object (or subclass of it).
	 *   3. The returned type is not empty and valid, that is:
	 *        3.1 One of Collectors::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_GROUPINGBY
	 *        3.2 Object (or subclass of it).
	 *
	 * @example
	 *	 Collectors::groupingBy (function (MyObject $myObject) : string {
	 *                              return $myObject->stringProperty;
     *                           });
	 *
	 * @param \Closure $closure
	 *    Function to apply to each element.
	 *
	 * @return CollectorImpl
	 *    Object with the information used to know how to accumulate several elements inside a HashMap
	 *
	 * @throws UnsupportedOperationException if the given function is not valid
	 */
	public static function groupingBy (\Closure $closureFunction) : CollectorImpl {

		return Collectors::groupingByUsingCollection ($closureFunction, new ArrayList());
	}


	/**
	 *    Returns a CollectorImpl implementing a "group by" operation on input elements according
	 * to a Closure function. And organizes them into a HashMap with a key of the same type returned
	 * by given function and ObjectWithIterable as value (whose innerIterable property is an ArrayList).
	 * The given function must satisfies the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to Object (or subclass of it).
	 *   3. The returned type is not empty and valid, that is:
	 *        3.1 One of Collectors::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_GROUPINGBY
	 *        3.2 Object (or subclass of it).
	 *
	 * @example
	 *	 Collectors::groupingByUsingCollection (function (MyObject $myObject) : string {
	 *                                             return $myObject->stringProperty;
	 *                                          }, new HashSet());
	 * @param \Closure $closure
	 *    Function to apply to each element.
	 * @param Collection $collection
	 *    An instance of a class that implements Collection interface used to store the returned values
	 *
	 * @return CollectorImpl
	 *    Object with the information used to know how to accumulate several elements inside a HashMap
	 *
	 * @throws UnsupportedOperationException if the given function is not valid or the given $collection
	 *                                       is not a valid implementation of Collection interface
	 *                                       (see Collectiors::VALID_COLLECTION_IMPLEMENTATIONS)
	 */
	public static function groupingByUsingCollection (\Closure $closureFunction, Collection $collection) : CollectorImpl {

		$reflectionFunctionInformation = Collectors::checkClosureFunctionOfGroupingBy ($closureFunction);

		if (!Collectors::isValidImplementationOfCollection ($collection))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The given collection is not a valid implementation of interface ".Collection::class
					                                    .", its type is ".get_class ($collection).". Only the following implementations "
					                                    ."are allowed: ".var_export (Collectors::VALID_COLLECTION_IMPLEMENTATIONS, TRUE));

		$collectorImpl = new CollectorImpl (Collectors::createHashMapForGroupingBy ($reflectionFunctionInformation->typeOfReturnedValue));

		// Defines the function used to filter the given values
		$collectorImpl->setAccumulator (function (Object $object) use (&$collectorImpl, &$closureFunction, &$collection) {

										   $keyValue    = $closureFunction ($object);
										   $valuesOfKey = $collectorImpl->getSupplier()->get ($keyValue);

										   if (!$valuesOfKey->isPresent()) {

										      $newObjectWithIterable = new ObjectWithIterable (clone $collection);

											  $collectorImpl->getSupplier()->put ($keyValue, $newObjectWithIterable);
											  $valuesOfKey = new Optional ($newObjectWithIterable);
										   }
										   $valuesOfKey->get()->getIterable()->add ($object);
		                                });
		return $collectorImpl;
	}


	/**
	 * Checks if the given closure in groupingByp function verify the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to Object (or subclass of it).
	 *   3. The returned type is not empty and valid, that is:
	 *        3.1 One of Collectors::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_GROUPINGBY
	 *        3.2 Object (or subclass of it).
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @return ReflectionFunctionInformation with useful information about closure function
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private static function checkClosureFunctionOfGroupingBy (\Closure $closureFunction) : ReflectionFunctionInformation {

		// Gets information about the given closure (returned type, types of the parameters, etc)
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure ($closureFunction);

		// Checks 1.
		if ($reflectionFunctionInformation->numberOfParameters != 1)
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__." [groupingBy] "
					                                ,"The given closure function has ".$reflectionFunctionInformation->numberOfParameters
					                                    ." parameters, however only one is permitted");
		// Checks 2.
		$parameterType = $reflectionFunctionInformation->typesOfParameters[0];

		if (empty ($parameterType) || !ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($parameterType, Object::class))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__." [groupingBy] "
					                                ,"The parameter of the given closure function: ".$parameterType." is not "
					                                    ."valid. Please use ".Object::class." or a subclass of it");
		// Checks 3.
		$returnType = $reflectionFunctionInformation->typeOfReturnedValue;

		if (empty ($returnType))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__." [groupingBy] "
					                                ,"The returned type of the given closure function can not be null or empty");

		if (!in_array ($returnType, Collectors::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_GROUPINGBY) &&
				!ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($returnType, Object::class))

			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__." [groupingBy] "
								                    ,"The returned type of the given closure function: ".$returnType." is not "
								                        ."valid. Please use ".Object::class." (or a subclass of it) or one of the following: "
								                        .var_export (Collectors::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_GROUPINGBY, TRUE));
		return $reflectionFunctionInformation;
	}


	/**
	 * Returns the correct HashMap instance taking into account the given type of "original closure function"
	 *
	 * @param string $returnedTypeOfClosureFunction
	 *    Type returned by the "original closure function"
	 *
	 * @return HashMap
	 *
	 * @throws UnsupportedOperationException if the given returned type is not allowed
	 */
	private static function createHashMapForGroupingBy (string $returnedTypeOfClosureFunction) : HashMap {

		switch ($returnedTypeOfClosureFunction) {

			case 'bool':
				return new HashMap (Map::KEY_BOOLEAN_TYPE);

			case 'int':
			case 'float':
				return new HashMap (Map::KEY_NUMERIC_TYPE);

			case 'string':
				return new HashMap (Map::KEY_STRING_TYPE);

			default:
				if (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($returnedTypeOfClosureFunction, Object::class))

					return new HashMap (Map::KEY_OBJECT_TYPE);
		}
		throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__." [groupingBy] "
				                                ,"The returned type of the closure function: ".$returnedTypeOfClosureFunction
				                                    ." is not valid. Please use ".Object::class." (or a subclass of it) or one "
				                                    ."of the following: bool, float, int, string");
	}


	/**
	 * Checks if thegiven object that implements Collection interface is valid or not.
	 *
	 * @see
	 *    Collectors::VALID_COLLECTION_IMPLEMENTATIONS
	 *
	 * @param Collection $collection
	 *    Object belonging to a class that implements of Collection interface
	 *
	 * @return TRUE if the given object is valid, FALSE otherwise.
	 */
	private static function isValidImplementationOfCollection (Collection $collection) : bool {

		return in_array (get_class ($collection), Collectors::VALID_COLLECTION_IMPLEMENTATIONS);
	}

}


/**
 * Class used to store the necessary properties to work with collectors functionality
 */
final class CollectorImpl {

	// Collection used to store the "collected elements"
	private $supplier;

	// A function that folds a value into the $supplier property
	private $accumulator;


	public function __construct (Iterable $supplier) {

		$this->supplier = $supplier;
	}


	public function getSupplier() : Iterable {
		return $this->supplier;
	}

	public function getAccumulator() : \Closure {
		return $this->accumulator;
	}

	public function setAccumulator (\Closure $accumulator) {
		$this->accumulator = $accumulator;
	}

}

?>