<?php

namespace FunctionalPHP\common\util;

/**
 * Utility class used to work reflection in PHP.
 */
final class ReflectionUtil {

	/**
	 * Returns information about the given closure: number and types of parameters, returned type, etc.
	 *
	 * @param \Closure $closureFunction
	 *    Function of which this method extracts information.
	 *
	 * @return ReflectionFunctionInformation
	 */
	public static function getReflectionInformationOfClosure (\Closure $closureFunction) : ReflectionFunctionInformation {

		$reflectionFunction = new \ReflectionFunction ($closureFunction);
		$typesOfParameters  = array();

		// Adds information about the types of parameters of $closureFunction
		for ($i = 0; $i < $reflectionFunction->getNumberOfParameters(); $i++)
			$typesOfParameters[] = (string) ($reflectionFunction->getParameters()[$i])->getType();

		return new ReflectionFunctionInformation ($reflectionFunction->getNumberOfParameters()
				                                 ,$typesOfParameters, (string) $reflectionFunction->getReturnType());
	}


	/**
	 * Checks if the given type name is equal or subclass of the given classes (or interfaces).
	 *
	 * @param string $typeToCheck
	 *    Name of the type to check
	 * @param string ...$classesToCheck
	 *    List of class names (or interface) to check
	 *
	 * @return TRUE if the given class name is equal or subclass of one of the list of classes (or interface),
	 *         FALSE otherwise.
	 */
	public static function isGivenTypeNameBelongsToTheGivenList (string $typeToCheck, string ...$classesToCheck) : bool {

		if (empty ($typeToCheck) || count ($classesToCheck) == 0)
			return FALSE;

		try {
			$reflectionClass = new \ReflectionClass ($typeToCheck);

			foreach ($classesToCheck as $classToCheck) {

				if (strcmp ($typeToCheck, $classToCheck) == 0 || $reflectionClass->isSubclassOf ($classToCheck))
					return TRUE;
			}

		} catch (\Exception $e) {
			return FALSE;
		}
		return FALSE;
	}


	/**
	 * Checks if the given object is equal or subclass of the given classes (or interfaces)
	 *
	 * @param $objectToCheck
	 *    Object to check
	 * @param string ...$classesToCheck
	 *    List of class names (or interface) to check
	 *
	 * @return TRUE if the given object is equal or subclass of one of the list of classes (or interface),
	 *         FALSE otherwise.
	 */
	public static function isGivenObjectBelongsToTheGivenList ($objectToCheck, string ...$classesToCheck) : bool {

		if (is_null ($objectToCheck))
			return FALSE;

		return ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (get_class ($objectToCheck), ...$classesToCheck);
	}

}



/**
 * Class used to store all useful information about a PHP function, closure, callable, etc
 */
final class ReflectionFunctionInformation {

	public $numberOfParameters;

	/**
	 * Vector with the types of the parameters of the function. For example:
	 *
	 *    function test (int $a, $b) { ... }
	 *
	 * $typesOfParameters = ([0] => "int", [1] => "")
	 */
	public $typesOfParameters;

	// Returned type of the function, empty string if the function does not declare a "returned type"
	public $typeOfReturnedValue;


	public function __construct (int $numberOfParameters, array $typesOfParameters, string $typeOfReturnedValue) {

		$this->numberOfParameters  = $numberOfParameters;
		$this->typesOfParameters   = $typesOfParameters;
		$this->typeOfReturnedValue = $typeOfReturnedValue;
	}

}

?>