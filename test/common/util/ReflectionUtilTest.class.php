<?php

namespace FunctionalPHP\test\common\util;

require_once '../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\common\Comparator;
use FunctionalPHP\common\Object;

use FunctionalPHP\common\functional\Stream;
use FunctionalPHP\common\functional\BasicStream;

use FunctionalPHP\common\util\ReflectionUtil;
use FunctionalPHP\common\util\ReflectionFunctionInformation;

use FunctionalPHP\iterable\AbstractIterable;
use FunctionalPHP\iterable\collection\Collection;
use FunctionalPHP\iterable\collection\lists\ArrayList;
use FunctionalPHP\iterable\collection\lists\AbstractLists;

use FunctionalPHP\test\Person;


/**
 * Class used to test FunctionalPHP\common\util\ReflectionUtil
 */
final class ReflectionUtilTest extends TestCase {

	/**
	 * @covers FunctionalPHP\common\util\ReflectionUtil::getReflectionInformationOfClosure
	 */
	public function testGetReflectionInformationOfClosure() {

		// Empty closure
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure (function() {});

		$this->assertNotNull ($reflectionFunctionInformation);
		$this->assertEquals (0, $reflectionFunctionInformation->numberOfParameters);
		$this->assertEmpty ($reflectionFunctionInformation->typesOfParameters);
		$this->assertEmpty ($reflectionFunctionInformation->typeOfReturnedValue);

		// Closure without type's definitions
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure (function ($p1, $p2) {});

		$this->assertNotNull ($reflectionFunctionInformation);
		$this->assertEquals (2, $reflectionFunctionInformation->numberOfParameters);

		$this->assertNotEmpty ($reflectionFunctionInformation->typesOfParameters);
		$this->assertEmpty ($reflectionFunctionInformation->typesOfParameters[0]);
		$this->assertEmpty ($reflectionFunctionInformation->typesOfParameters[1]);

		$this->assertEmpty ($reflectionFunctionInformation->typeOfReturnedValue);

		// Closure with native type's definitions
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure (function (int $p1, bool $p2) : float {});

		$this->assertNotNull ($reflectionFunctionInformation);
		$this->assertEquals (2, $reflectionFunctionInformation->numberOfParameters);

		$this->assertNotEmpty ($reflectionFunctionInformation->typesOfParameters);
		$this->assertEquals ("int", $reflectionFunctionInformation->typesOfParameters[0]);
		$this->assertEquals ("bool", $reflectionFunctionInformation->typesOfParameters[1]);

		$this->assertEquals ("float", $reflectionFunctionInformation->typeOfReturnedValue);

		// Closure with non native type's definitions
		$reflectionFunctionInformation = ReflectionUtil::getReflectionInformationOfClosure (function (Person $p1, Object $p2) : ArrayList {});

		$this->assertNotNull ($reflectionFunctionInformation);
		$this->assertEquals (2, $reflectionFunctionInformation->numberOfParameters);

		$this->assertNotEmpty ($reflectionFunctionInformation->typesOfParameters);
		$this->assertEquals (Person::class, $reflectionFunctionInformation->typesOfParameters[0]);
		$this->assertEquals (Object::class, $reflectionFunctionInformation->typesOfParameters[1]);

		$this->assertEquals (ArrayList::class, $reflectionFunctionInformation->typeOfReturnedValue);
	}


	/**
	 * @covers FunctionalPHP\common\util\ReflectionUtil::isGivenTypeNameBelongsToTheGivenList
	 */
	public function testIsGivenTypeNameBelongsToTheGivenListOfEmptyTypeToCheck() {

		$this->assertFalse (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ("", Person::class));
	}


	/**
	 * @covers FunctionalPHP\common\util\ReflectionUtil::isGivenTypeNameBelongsToTheGivenList
	 */
	public function testIsGivenTypeNameBelongsToTheGivenListOfEmptyClassesToCheck() {

		$this->assertFalse (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (Person::class, ""));
	}


	/**
	 * @covers FunctionalPHP\common\util\ReflectionUtil::isGivenTypeNameBelongsToTheGivenList
	 */
	public function testIsGivenTypeNameBelongsToTheGivenListOfInterfaces() {

		$this->assertFalse (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (ArrayList::class, Stream::class));
		$this->assertFalse (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (ArrayList::class, Comparator::class));
		$this->assertFalse (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (ArrayList::class, Stream::class, Comparator::class));

		$this->assertTrue (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (ArrayList::class, Collection::class));
		$this->assertTrue (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (ArrayList::class, Collection::class, Comparator::class));

		$this->assertTrue (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (BasicStream::class, Stream::class));
		$this->assertTrue (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (BasicStream::class, Stream::class, Comparator::class));
	}


	/**
	 * @covers FunctionalPHP\common\util\ReflectionUtil::isGivenTypeNameBelongsToTheGivenList
	 */
	public function testIsGivenTypeNameBelongsToTheGivenListOfClasses() {

		$this->assertFalse (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (ArrayList::class, BasicStream::class));
		$this->assertFalse (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (ArrayList::class, Person::class));
		$this->assertFalse (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (ArrayList::class, BasicStream::class, Person::class));

		$this->assertTrue (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (ArrayList::class, AbstractIterable::class));
		$this->assertTrue (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (ArrayList::class, AbstractLists::class));

		$this->assertTrue (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (Object::class, Object::class));
		$this->assertTrue (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList (Person::class, Object::class));
	}


	/**
	 * @covers FunctionalPHP\common\util\ReflectionUtil::isGivenObjectBelongsToTheGivenList
	 */
	public function testIsGivenObjectBelongsToTheGivenListOFNullObject() {

		$this->assertFalse (ReflectionUtil::isGivenObjectBelongsToTheGivenList (NULL, Person::class));
	}


	/**
	 * @covers FunctionalPHP\common\util\ReflectionUtil::isGivenObjectBelongsToTheGivenList
	 */
	public function testIsGivenObjectBelongsToTheGivenListOfEmptyObject() {

		$this->assertFalse (ReflectionUtil::isGivenTypeNameBelongsToTheGivenList ("", Person::class));
	}


	/**
	 * @covers FunctionalPHP\common\util\ReflectionUtil::isGivenObjectBelongsToTheGivenList
	 */
	public function testIsGivenObjectBelongsToTheGivenListOfEmptyClassesToCheck() {

		$person = new Person ("John", 18, TRUE);

		$this->assertFalse (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($person, ""));
	}


	/**
	 * @covers FunctionalPHP\common\util\ReflectionUtil::isGivenObjectBelongsToTheGivenList
	 */
	public function testIsGivenObjectBelongsToTheGivenListOfInterfaces() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertFalse (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($arrayList, Stream::class));
		$this->assertFalse (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($arrayList, Comparator::class));
		$this->assertFalse (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($arrayList, Stream::class, Comparator::class));

		$this->assertTrue (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($arrayList, Collection::class));
		$this->assertTrue (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($arrayList, Collection::class, Comparator::class));

		$this->assertTrue (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($basicStream, Stream::class));
		$this->assertTrue (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($basicStream, Stream::class, Comparator::class));
	}


	/**
	 * @covers FunctionalPHP\common\util\ReflectionUtil::isGivenObjectBelongsToTheGivenList
	 */
	public function testIsGivenObjectBelongsToTheGivenListOfClasses() {

		$arrayList = new ArrayList();
		$person = new Person ("John", 18, TRUE);

		$this->assertFalse (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($arrayList, BasicStream::class));
		$this->assertFalse (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($arrayList, Person::class));
		$this->assertFalse (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($arrayList, BasicStream::class, Person::class));

		$this->assertTrue (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($arrayList, AbstractIterable::class));
		$this->assertTrue (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($arrayList, AbstractLists::class));

		$this->assertTrue (ReflectionUtil::isGivenObjectBelongsToTheGivenList ($person, Object::class));
	}

}

?>