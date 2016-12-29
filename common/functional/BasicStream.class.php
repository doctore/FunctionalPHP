<?php

namespace FunctionalPHP\common\functional;

use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;

use FunctionalPHP\common\functional\Predicate;
use FunctionalPHP\common\functional\Stream;

use FunctionalPHP\common\util\ReflectionUtil;

use FunctionalPHP\exception\IllegalArgumentException;
use FunctionalPHP\exception\UnsupportedOperationException;

use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\iterable\collection\queue\PriorityQueue;
use FunctionalPHP\iterable\collection\set\HashSet;
use FunctionalPHP\iterable\Iterable;
use FunctionalPHP\common\util\ReflectionFunctionInformation;


/**
 * Basic implementation of the features required by Stream interface.
 */
class BasicStream implements Stream {

	/**
	 * The array buffer into which the elements of the Stream are stored
	 */
	protected $internalData;

	/**
	 * Store the current type of the elements saved in $internalData
	 */
	protected $currentTypeOfInternalData;


	/**
	 * Constructs a skeletal implementation of a Stream.
	 *
	 * @param Collection $collection
	 *    Collection from which we extract its elements and store them into the current Stream
	 */
	public function __construct (Collection $collection) {

		$this->internalData = $collection->toArray();

		if (count ($this->internalData) > 0)
			$this->currentTypeOfInternalData = get_class ($this->internalData[0]);
		else
			$this->currentTypeOfInternalData = Object::class;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::allMatch()
	 */
	public function allMatch (Predicate $predicate) : bool {

		foreach ($this->internalData as $element) {

			if (!$predicate->test ($element))
				return FALSE;
		}
		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::anyMatch()
	 */
	public function anyMatch (Predicate $predicate) : bool {

		foreach ($this->internalData as $element) {

			if ($predicate->test ($element))
				return TRUE;
		}
		return FALSE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::collect()
	 */
	public function collect (CollectorImpl $collector) : Iterable {

		if (!ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($this->currentTypeOfInternalData, Object::class))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"This operation only can be executed if the type of the stored elements "
					                                     ."in the Stream belongs to the class ".Object::class." (or a subclass "
					                                     ."of it), the current type of this Stream is: ".$this->currentTypeOfInternalData);
		foreach ($this->internalData as $element)
			$collector->getAccumulator()($element);

		return $collector->getSupplier();
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::count()
	 */
	public function count() : int {

		return count ($this->internalData);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::distinct()
	 */
	public function distinct() : Stream {

		if ($this->count() == 0)
			return $this;

		// The Stream stores elements belonging to a subclass of Object
		if (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($this->currentTypeOfInternalData, Object::class)) {

			$hashSet = new HashSet();
			$internalDataUniqueValues = array();

			foreach ($this->internalData as $element) {

				// Uses a HashSet to check if the current element is unique or not
				if (!$hashSet->contains ($element)) {

					$hashSet->add ($element);
					$internalDataUniqueValues[] = $element;
				}
			}
			$this->internalData = $internalDataUniqueValues;
		}
		// The Stream stores a "native type", see Stream::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_MAP
		else
			$this->internalData = array_values (array_unique ($this->internalData));

		return $this;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::filter()
	 */
	public function filter (Predicate $predicate) : Stream {

		$internalDataMatchesWithFilter = array();
		foreach ($this->internalData as $element) {

			if ($predicate->test ($element))
				$internalDataMatchesWithFilter[] = $element;
		}
		$this->internalData = $internalDataMatchesWithFilter;
		return $this;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::filterByLambda()
	 */
	public function filterByLambda (\Closure $funtionToFilter) : Stream {

		$this->checkClosureFunctionOfFilterByLambda ($funtionToFilter);

		$internalDataMatchesWithFilter = array();
		foreach ($this->internalData as $element) {

			if ($funtionToFilter ($element))
				$internalDataMatchesWithFilter[] = $element;
		}
		$this->internalData = $internalDataMatchesWithFilter;
		return $this;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::findFirst()
	 */
	public function findFirst() : Optional {

		if ($this->count() == 0)
			return new Optional (NULL);

		return new Optional ($this->internalData[0]);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::flapMap()
	 */
	public function flatMap (\Closure $funtionUsedToFlat) : Stream {

		$this->checkClosureFunctionOfFlatMap ($funtionUsedToFlat);

		$internalDataAfterAppliesGivenFunction = array();
		$finalTypeOfStoredElements             = NULL;
		foreach ($this->internalData as $element) {

			$newStream = $funtionUsedToFlat ($element);
			$finalTypeOfStoredElements = $newStream->getCurrentTypeStoredByStream();

			// Concats the result of the given function on "current element" to the previous results
			$internalDataAfterAppliesGivenFunction = array_merge ($internalDataAfterAppliesGivenFunction
					                                             ,$newStream->toArray());
		}
		if (is_null ($finalTypeOfStoredElements))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"It was not possible to update the type of the stored "
					                                    ."elements in this stream");

		$this->internalData = $internalDataAfterAppliesGivenFunction;
		$this->currentTypeOfInternalData = $finalTypeOfStoredElements;

		return $this;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::forEach()
	 */
	public function forEach (\Closure $functionToApply) {

		$this->checkClosureFunctionOfForeach ($functionToApply);

		array_map ($functionToApply, $this->internalData);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::getCurrentTypeStoredByStream()
	 */
	public function getCurrentTypeStoredByStream() : string {

		return $this->currentTypeOfInternalData;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::limit()
	 */
	public function limit (int $maxSize) : Stream {

		if ($maxSize < 0)
			throw new IllegalArgumentException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                           ,"The given maxSize: {$maxSize} is less than 0");

		$this->internalData = array_slice ($this->internalData, 0, $maxSize);
		return $this;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::min()
	 */
	public function min (Comparator $comparator) : Optional {

		if (!ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($this->currentTypeOfInternalData, Object::class))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"This operation only can be executed if the type of the stored elements "
					                                    ."in the Stream belongs to the class ".Object::class." (or a subclass "
					                                    ."of it), the current type of this Stream is: ".$this->currentTypeOfInternalData);
		if ($this->count() == 0)
			return new Optional (NULL);

		$min = $this->internalData[0];
		foreach ($this->internalData as $element) {

			if ($comparator->compare ($min, $element) > 0)
				$min = $element;
		}
		return new Optional ($min);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::map()
	 */
	public function map (\Closure $functionToApply) : Stream {

		$returnedTypeOfFunctionToApply = $this->checkClosureFunctionOfMap ($functionToApply);

		$internalDataAfterMap = array();
		foreach ($this->internalData as $element)
			$internalDataAfterMap[] = $functionToApply ($element);

		$this->internalData = $internalDataAfterMap;
		$this->currentTypeOfInternalData = $returnedTypeOfFunctionToApply;

		return $this;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::max()
	 */
	public function max (Comparator $comparator) : Optional {

		if (!ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($this->currentTypeOfInternalData, Object::class))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"This operation only can be executed if the type of the stored elements "
					                                    ."in the Stream belongs to the class ".Object::class." (or a subclass "
					                                    ."of it), the current type of this Stream is: ".$this->currentTypeOfInternalData);
		if ($this->count() == 0)
			return new Optional (NULL);

		$max = $this->internalData[0];
		foreach ($this->internalData as $element) {

			if ($comparator->compare ($max, $element) < 0)
				$max = $element;
		}
		return new Optional ($max);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::noneMatch()
	 */
	public function noneMatch (Predicate $predicate) : bool {

		foreach ($this->internalData as $element) {

			if ($predicate->test ($element))
				return FALSE;
		}
		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::sorted()
	 */
	public function sorted() : Stream {

		if ($this->count() == 0)
			return $this;

		// The Stream stores elements belonging to a subclass of Object
		if (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($this->currentTypeOfInternalData, Object::class)) {

			$priorityQueue = new PriorityQueue();

			foreach ($this->internalData as $element)
				$priorityQueue->add ($element);

			$this->internalData = $priorityQueue->toArray();
		}
		// The Stream stores a "native type", see Stream::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_MAP
		else
			sort ($this->internalData);

		return $this;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::sortedByComparator()
	 */
	public function sortedByComparator (Comparator $comparator) : Stream {

		if (!ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($this->currentTypeOfInternalData, Object::class))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"This operation only can be executed if the type of the stored elements "
					                                    ."in the Stream belongs to the class ".Object::class." (or a subclass "
					                                    ."of it), the current type of this Stream is: ".$this->currentTypeOfInternalData);
		if ($this->count() == 0)
			return $this;

		$priorityQueue = new PriorityQueue (NULL, $comparator);

		foreach ($this->internalData as $element)
			$priorityQueue->add ($element);

		$this->internalData = $priorityQueue->toArray();
		return $this;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::sortedByLambda()
	 */
	public function sortedByLambda (\Closure $sortFunction) : Stream {

		$this->checkClosureFunctionOfSortedByLambda ($sortFunction);

		usort ($this->internalData, $sortFunction);
		return $this;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::toArray()
	 */
	public function toArray() : array {

		return $this->internalData;
	}


	/**
	 * Checks if the given closure in filterByLambda function verify the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to the type of the stream's elements.
	 *      (or subclass of Object if the stream stores Objects).
	 *   3. The returned type is not empty and valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA)
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureFunctionOfFilterByLambda (\Closure $closureFunction) {

		// Gets information about the given closure (returned type, types of the parameters, etc)
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure ($closureFunction);

		// Checks 1. and 2.
		$this->checkClosureParameters (__FUNCTION__, $reflectionFunctionInformation, 1);

		// 3. The returned type is not null and valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA)
		$returnType = $reflectionFunctionInformation->typeOfReturnedValue;

		if (empty ($returnType) || !in_array ($returnType, Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA))

			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The returned type of the given closure function: ".$returnType
					                                    ." is not valid. Please use one of the following: "
					                                    .var_export (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA, TRUE));
	}


	/**
	 * Checks if the given closure in flatMap function verify the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to the type of the stream's elements.
	 *      (or subclass of Object if the stream stores Objects).
	 *   3. The returned type must be an instance of Stream.
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureFunctionOfFlatMap (\Closure $closureFunction) {

		// Gets information about the given closure (returned type, types of the parameters, etc)
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure ($closureFunction);

		// Checks 1. and 2.
		$this->checkClosureParameters (__FUNCTION__, $reflectionFunctionInformation, 1);

		// 3. The returned type must be an instance of Stream.
		$returnType = $reflectionFunctionInformation->typeOfReturnedValue;

		if (empty ($returnType) || !ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($returnType, Stream::class))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The returned type of the given closure function: ".$returnType." is not "
					                                    ."valid. Please use ".Stream::class." or a subclass of it");
	}


	/**
	 * Checks if the given closure in foreach function verify the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to the type of the stream's elements.
	 *      (or subclass of Object if the stream stores Objects).
	 *   3. The returned type is empty or valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH)
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureFunctionOfForeach (\Closure $closureFunction) {

		// Gets information about the given closure (returned type, types of the parameters, etc)
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure ($closureFunction);

		// Checks 1. and 2.
		$this->checkClosureParameters (__FUNCTION__, $reflectionFunctionInformation, 1);

		// 3. The returned type is null or valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH)
		$returnType = $reflectionFunctionInformation->typeOfReturnedValue;

		if (!empty ($returnType) && !in_array ($returnType, Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH))

			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The returned type of the given closure function: ".$returnType
					                                    ." is not valid. Please delete the returned type or use one of the following: "
					                                    .var_export (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH, TRUE));
	}


	/**
	 * Checks if the given closure in map function verify the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to the type of the stream's elements.
	 *      (or subclass of Object if the stream stores Objects).
	 *   3. The returned type is not empty and valid, that is:
	 *        3.1 Equal to the type of Stream's elements
	 *        3.2 One of Stream::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_MAP
	 *        3.3 A subclass of Object.
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @return string with the returned type of the given closure function
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureFunctionOfMap (\Closure $closureFunction) : string {

		// Gets information about the given closure (returned type, types of the parameters, etc)
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure ($closureFunction);

		// Checks 1. and 2.
		$this->checkClosureParameters (__FUNCTION__, $reflectionFunctionInformation, 1);

		// 3. The returned type is not null and valid
		$returnType = $reflectionFunctionInformation->typeOfReturnedValue;

		if (empty ($returnType))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
						                            ,"The returned type of the given closure function can not be null or empty");

		if (strcmp ($returnType, $this->currentTypeOfInternalData) != 0 &&
				!in_array ($returnType, Stream::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_MAP)) {

			// Test if it is a subclass of Object
			if (!ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($returnType, Object::class))
				throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
						                                ,"The returned type of the given closure function: ".$returnType." is not "
						                                   ."valid. Please use a subclass of ".Object::class." or one of the following: "
							                               .var_export (Stream::VALID_NATIVE_RETURNED_TYPES_OF_CLOSURE_IN_MAP, TRUE));
		}
		return $returnType;
	}


	/**
	 * Checks if the given closure in sortedByLambda function verify the following rules:
	 *
	 *   1. Only has two parameters.
	 *   2. The type of the given parameters must be equal to the type of the stream's elements.
	 *      (or subclass of Object if the stream stores Objects).
	 *   3. The returned type is not empty and valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_SORTEDBYLAMBDA)
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureFunctionOfSortedByLambda (\Closure $closureFunction) {

		// Gets information about the given closure (returned type, types of the parameters, etc)
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure ($closureFunction);

		// Checks 1. and 2.
		$this->checkClosureParameters (__FUNCTION__, $reflectionFunctionInformation, 2);

		// 3. The returned type is empty or valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH)
		$returnType = $reflectionFunctionInformation->typeOfReturnedValue;

		if (empty ($returnType) || !in_array ($returnType, Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_SORTEDBYLAMBDA))

			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The returned type of the given closure function: ".$returnType
					                                   ." is not valid. Please use one of the following: "
					                                   .var_export (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH, TRUE));
	}


	/**
	 * Checks if the given closure has valid parameters and verifies the following rules:
	 *
	 *   1. The number of parameters must be equals to $numberOfParameters
	 *   2. The type of the parameters must be equal to the type of the stream's elements
	 *      (or subclass of Object if the stream stores Objects).
	 *
	 * @param $originalStreamFunction
	 *    Function of this Stream that we are verifying.
	 * @param ReflectionFunctionInformation $reflectionFunctionInformation
	 *    Information about the types of parameters, returned type, etc of the original closure function
	 * @param int $numberOfParameters
	 *    Number of the parameters that the given function must have.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureParameters (string $originalStreamFunction, ReflectionFunctionInformation $reflectionFunctionInformation
			                                ,int $numberOfParameters) {

		// 1. Only has one parameter.
		if ($reflectionFunctionInformation->numberOfParameters != $numberOfParameters)
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__." [".$originalStreamFunction."] "
					                                ,"The given closure function has ".$reflectionFunctionInformation->numberOfParameters
					                                    ." parameters, however only ".$numberOfParameters." are permitted");

		// 2. The type of the parameters must be equal to the type of the stream's elements.
		for ($i = 0; $i < $reflectionFunctionInformation->numberOfParameters; $i++) {

			$parameterType = $reflectionFunctionInformation->typesOfParameters[$i];

			if (empty ($parameterType))
				throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
						                                ,"In the given closure function and the paremeter number: "
						                                    .($i+1).", its type can not be null or empty");

			if (strcmp ($parameterType, $this->currentTypeOfInternalData) != 0) {

				// Test if it is a subclass of Object
				if (!ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($parameterType, Object::class))
					throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__." [".$originalStreamFunction."] "
							                                ,"In the given closure function and the paremeter number: ".($i+1)
							                                    .", its type: ".$parameterType." is not equal to the elements "
							                                    ."stored in the Stream: ".$this->currentTypeOfInternalData);
			}
		}
	}

}

?>