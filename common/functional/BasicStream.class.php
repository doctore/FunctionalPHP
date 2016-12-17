<?php

namespace FunctionalPHP\common\functional;

use FunctionalPHP\collection\Collection;
use FunctionalPHP\collection\queue\PriorityQueue;
use FunctionalPHP\collection\set\HashSet;
use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;
use FunctionalPHP\common\Optional;
use FunctionalPHP\common\functional\Predicate;
use FunctionalPHP\common\functional\Stream;
use FunctionalPHP\exception\IllegalArgumentException;
use FunctionalPHP\exception\UnsupportedOperationException;

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
		if ($this->isGivenTypeSubclassOfObject ($this->currentTypeOfInternalData)) {

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
		// The Stream stores a "native type", see Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_MAP
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
	 * @see \FunctionalPHP\common\functional\Stream::forEach()
	 */
	public function forEach (\Closure $functionToApply) {

		$this->checkClosureFunctionOfForeach ($functionToApply);

		array_map ($functionToApply, $this->internalData);
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
		if ($this->isGivenTypeSubclassOfObject ($this->currentTypeOfInternalData)) {

			$priorityQueue = new PriorityQueue();

			foreach ($this->internalData as $element)
				$priorityQueue->add ($element);

			$this->internalData = $priorityQueue->toArray();
		}
		// The Stream stores a "native type", see Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_MAP
		else
			sort ($this->internalData);

		return $this;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\common\functional\Stream::sortedByComparator()
	 */
	public function sortedByComparator (Comparator $comparator) : Stream {

		if (is_null ($this->currentTypeOfInternalData) || !$this->isGivenTypeSubclassOfObject ($this->currentTypeOfInternalData))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"This operation only can be executed if the type of the stored elements "
					                                    ."in the Stream is a subclass of ".Object::class.", the current "
					                                    ."type of this Stream is: ".$this->currentTypeOfInternalData);
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
	 *PriorityQueue
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to the type of the stream's elements.
	 *   3. The returned type is not null and valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA)
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureFunctionOfFilterByLambda (\Closure $closureFunction) {

		// Checks 1. and 2.
		$reflectionFunction = $this->checkClosureParameters ($closureFunction, 1);

		// 3. The returned type is not null and valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA)
		$returnType = (string) $reflectionFunction->getReturnType();
		if ($returnType == NULL || empty ($returnType) ||
				!in_array ($returnType, Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA))

			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The returned type of the given closure function: ".$returnType
					                                    ." is not valid. Please use one of the following: "
					                                    .var_export (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA, TRUE));
	}


	/**
	 * Checks if the given closure in foreach function verify the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal to the type of the stream's elements.
	 *   3. The returned type is null or valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH)
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureFunctionOfForeach (\Closure $closureFunction) {

		// Checks 1. and 2.
		$reflectionFunction = $this->checkClosureParameters ($closureFunction, 1);

		// 3. The returned type is null or valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH)
		$returnType = (string) $reflectionFunction->getReturnType();

		if ($reflectionFunction->hasReturnType() && !in_array ($returnType, Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH))

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
	 *   3. The returned type is not null and valid, that is:
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

		// Checks 1. and 2.
		$reflectionFunction = $this->checkClosureParameters ($closureFunction, 1);

		// 3. The returned type is not null and valid
		$returnType = (string) $reflectionFunction->getReturnType();
		if ($returnType == NULL || empty ($returnType))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
						                            ,"The returned type of the given closure function can not be null");

		if (strcmp ($returnType, $this->currentTypeOfInternalData) != 0 &&
				!in_array ($returnType, Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_MAP)) {

			// Test if it is a subclass of Object
			if (!$this->isGivenTypeSubclassOfObject ($returnType))
				throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
						                                ,"The returned type of the given closure function: ".$returnType
						                                   ." is not valid. Please use a subclass of ".Object::class
						                                   ." or one of the following: "
							                               .var_export (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_MAP, TRUE));
		}
		return $returnType;
	}


	/**
	 * Checks if the given closure in sortedByLambda function verify the following rules:
	 *
	 *   1. Only has two parameters.
	 *   2. The type of the given parameters must be equal to the type of the stream's elements.
	 *   3. The returned type is not null and valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_SORTEDBYLAMBDA)
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureFunctionOfSortedByLambda (\Closure $closureFunction) {

		// Checks 1. and 2.
		$reflectionFunction = $this->checkClosureParameters ($closureFunction, 2);

		// 3. The returned type is null or valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH)
		$returnType = (string) $reflectionFunction->getReturnType();

		if ($returnType == NULL || empty ($returnType) ||
				!in_array ($returnType, Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_SORTEDBYLAMBDA))

			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The returned type of the given closure function: ".$returnType
					                                   ." is not valid. Please use one of the following: "
					                                   .var_export (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH, TRUE));
	}


	/**
	 * Checks if the given closure has valid parameters and verifies the following rules:
	 *
	 *   1. The number of parameters must be equals to $numberOfParameters
	 *   2. The type of the parameters must be equal to the type of the stream's elements.
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 * @param int $numberOfParameters
	 *    Number of the parameters that the given function must have.
	 *
	 * @return \ReflectionFunction with "reflection information" about the given function
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	private function checkClosureParameters (\Closure $closureFunction, int $numberOfParameters) : \ReflectionFunction {

		// 1. Only has one parameter.
		$reflectionFunction = new \ReflectionFunction ($closureFunction);

		if ($reflectionFunction->getNumberOfParameters() != $numberOfParameters)
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The given closure function has ".$reflectionFunction->getNumberOfParameters()
					                                    ." parameters, however only ".$numberOfParameters." are permitted");

		// 2. The type of the parameters must be equal to the type of the stream's elements.
		for ($i = 0; $i < $reflectionFunction->getNumberOfParameters(); $i++) {

			$parameterType = (string) ($reflectionFunction->getParameters()[$i])->getType();

			if ($parameterType == NULL || empty ($parameterType) || strcmp ($parameterType, $this->currentTypeOfInternalData) != 0)
				throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
						                                ,"In the given closure function and the paremeter number: ".($i+1)
						                                    .", its type: ".$parameterType." is not equal to the elements "
						                                    ."stored in the Stream: "
						                                    .(is_null ($this->currentTypeOfInternalData) ? "NULL" : $this->currentTypeOfInternalData));
		}
		return $reflectionFunction;
	}


	/**
	 * Checks if the given type is subclass of Object
	 *
	 * @param string $typeToCheck
	 *    Type to check
	 *
	 * @return TRUE if the given class name is subclass of Object, FALSE otherwise.
	 */
	private function isGivenTypeSubclassOfObject (string $typeToCheck) : bool {

		if (is_null ($typeToCheck) || empty ($typeToCheck))
			return FALSE;

		$isSubclassOfObject = FALSE;
		try {
			if ((new \ReflectionClass ($typeToCheck))->isSubclassOf (Object::class))
				$isSubclassOfObject = TRUE;

		} catch (\Exception $e) {}

		return $isSubclassOfObject;
	}

}

?>