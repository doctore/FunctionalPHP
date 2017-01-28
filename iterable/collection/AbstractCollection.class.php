<?php

namespace FunctionalPHP\iterable\collection;

use FunctionalPHP\common\Object;
use FunctionalPHP\common\functional\Stream;
use FunctionalPHP\common\functional\BasicStream;
use FunctionalPHP\common\util\ReflectionFunctionInformation;
use FunctionalPHP\common\util\ReflectionUtil;

use FunctionalPHP\exception\UnsupportedOperationException;

use FunctionalPHP\iterable\AbstractIterable;
use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\common\functional\Predicate;


/**
 *    This class provides a skeletal implementation of the Collection interface, to minimize the effort
 * required to implement this interface.
 */
abstract class AbstractCollection extends AbstractIterable implements Collection {

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::add()
	 */
	abstract public function add (Object $element) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::contains()
	 */
	abstract public function contains (Object $element) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::equals()
	 */
	abstract public function equals (Collection $collection) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\iterable\collection\Collection::filter()
	 */
	abstract public function filter (Predicate $predicate) : Collection;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\iterable\collection\Collection::filterByLambda()
	 */
	abstract public function filterByLambda (\Closure $funtionToFilter) : Collection;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::remove()
	 */
	abstract public function remove (Object $element) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::retainAll()
	 */
	abstract function retainAll (Collection $collection) : bool;

	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::toArray()
	 */
	abstract public function toArray() : array;


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::addAll()
	 */
	public function addAll (Collection $collection) : bool {

		if ($collection->isEmpty())
			return FALSE;

		foreach ($collection->iterator() as $element)
			$this->add ($element);

		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::containsAll()
	 */
	public function containsAll (Collection $collection) : bool {

		foreach ($collection->iterator() as $element) {

			if (!$this->contains ($element))
				return FALSE;
		}
		return TRUE;
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\iterable\collection\AbstractCollection::forEach()
	 */
	public function forEach (\Closure $functionToApply) {

		$this->checkClosureFunctionOfForeach ($functionToApply);

		foreach ($this->iterator() as $element)
			$functionToApply ($element);
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::removeAll()
	 */
	public function removeAll (Collection $collection) : bool {

		$numOfElementsBefore = $this->size();

		foreach ($collection->iterator() as $element)
			$this->remove ($element);

		return $numOfElementsBefore != $this->size();
	}


	/**
	 * {@inheritDoc}
	 * @see \FunctionalPHP\collection\Collection::stream()
	 */
	public function stream() : Stream {

		return new BasicStream ($this);
	}


	/**
	 * Checks if the given Closure in filterByLambda function verify the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal (or subclass) of Object.
	 *   3. The returned type is not empty and valid (Collection::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA)
	 *
	 * @param \Closure $closureFunction
	 *    Closure function to check.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	protected function checkClosureFunctionOfFilterByLambda (\Closure $closureFunction) {

		// Gets information about the given closure (returned type, types of the parameters, etc)
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure ($closureFunction);

		// Checks 1. and 2.
		$this->checkClosureParameters (__FUNCTION__, $reflectionFunctionInformation, 1);

		// 3. The returned type is not null and valid (Stream::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA)
		$returnType = $reflectionFunctionInformation->typeOfReturnedValue;

		if (empty ($returnType) || !in_array ($returnType, Collection::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA))

			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The returned type of the given closure function: ".$returnType
					                                    ." is not valid. Please use one of the following: "
					                                    .var_export (Collection::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FILTERBYLAMBDA, TRUE));
	}


	/**
	 * Checks if the given closure has valid parameters and verifies the following rules:
	 *
	 *   1. The number of parameters must be equals to $numberOfParameters
	 *   2. The type of the parameters must be equal (or subclass) of Object.
	 *
	 * @param $originalStreamFunction
	 *    Function of this Collection that we are verifying.
	 * @param ReflectionFunctionInformation $reflectionFunctionInformation
	 *    Information about the types of parameters, returned type, etc of the original closure function
	 * @param int $numberOfParameters
	 *    Number of the parameters that the given function must have.
	 *
	 * @throws UnsupportedOperationException if the closure function does not verify all previous rules
	 */
	protected function checkClosureParameters (string $originalStreamFunction, ReflectionFunctionInformation $reflectionFunctionInformation
			                                  ,int $numberOfParameters) {

		// 1. The number of parameters must be equals to $numberOfParameters
		if ($reflectionFunctionInformation->numberOfParameters != $numberOfParameters)
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__." [".$originalStreamFunction."] "
					                                ,"The given closure function has ".$reflectionFunctionInformation->numberOfParameters
					                                    ." parameters, however only ".$numberOfParameters." are permitted");

		// 2. The type of the parameters must be equal (or subclass) of Object
		for ($i = 0; $i < $reflectionFunctionInformation->numberOfParameters; $i++) {

			$parameterType = $reflectionFunctionInformation->typesOfParameters[$i];

			if (empty ($parameterType))
				throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
						                                ,"In the given closure function and the paremeter number: "
						                                    .($i+1).", its type can not be null or empty");

			// The type of the current parameter must be equal to the stored elements in the Stream
			if (!ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ($parameterType, Object::class))
				throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__." [".$originalStreamFunction."] "
						                                ,"In the given closure function and the parameter number: ".($i+1)
						                                    .", its type: ".$parameterType." is not equal (or a subclass) of ".Object::class);
		}
	}


	/**
	 * Checks if the given closure in foreach function verify the following rules:
	 *
	 *   1. Only has one parameter.
	 *   2. The type of this unique parameter must be equal (or subclass) of Object.
	 *   3. The returned type is empty or valid (Collection::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH)
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

		if (!empty ($returnType) && !in_array ($returnType, Collection::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH))
			throw new UnsupportedOperationException (__CLASS__.'-'.__FUNCTION__.':'.__LINE__
					                                ,"The returned type of the given closure function: ".$returnType
					                                    ." is not valid. Please delete the returned type or use one of the following: "
					                                    .var_export (Collection::VALID_RETURNED_TYPES_OF_CLOSURE_IN_FOREACH, TRUE));
	}

}

?>