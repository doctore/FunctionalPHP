<?php

namespace FunctionalPHP\test\common;

require_once '../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\common\Optional;
use FunctionalPHP\test\DummyObject;
use FunctionalPHP\exception\IllegalArgumentException;
use FunctionalPHP\exception\UnsupportedOperationException;

/**
 * Class used to test FunctionalPHP\common\Optional
 */
final class OptionalTest extends TestCase {


	/**
	 * @covers FunctionalPHP\common\Optional::__construct
	 */
	public function testOptionalWithNullValue() {

		$optional = new Optional (NULL);
		$this->assertFalse ($optional->isPresent());
	}


	/**
	 * @covers FunctionalPHP\common\Optional::__construct
	 */
	public function testOptionalWithNotNullValue() {

		$dummyObject = new DummyObject (1, "a", FALSE);
		$optional = new Optional ($dummyObject);

		$this->assertTrue ($optional->isPresent());
		$this->assertTrue ($dummyObject->equals ($optional->get()));
	}


	/**
	 * @covers FunctionalPHP\common\Optional::equals
	 */
	public function testCheckEquality() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);
		$dummyObject3 = new DummyObject (3, "c", FALSE);
		$dummyObject4 = new DummyObject (3, "c", TRUE);

		$optional1 = new Optional ($dummyObject1);
		$this->assertTrue ($optional1->equals ($optional1));

		$optional2 = new Optional (NULL);
		$this->assertFalse ($optional1->equals ($optional2));
		$this->assertFalse ($optional2->equals ($optional1));

		// Compares two Optional objects with NULL as content
		$optional3 = new Optional (NULL);
		$this->assertTrue ($optional2->equals ($optional3));
		$this->assertTrue ($optional3->equals ($optional2));

		// Compares two Optional objects with "equals DummyObjects" as content
		$optional4 = new Optional ($dummyObject3);
		$optional5 = new Optional ($dummyObject4);
		$this->assertTrue ($optional4->equals ($optional5));
		$this->assertTrue ($optional5->equals ($optional4));

		// Compares Optional with different types as content
		$optional6 = new Optional ("1");
		$optional7 = new Optional (1);
		$this->assertFalse ($optional6->equals ($optional7));
		$this->assertFalse ($optional7->equals ($optional6));

		// Compares Optional with an equal content
		$optional8 = new Optional ("1");
		$optional9 = new Optional ("1");
		$this->assertTrue ($optional8->equals ($optional9));
		$this->assertTrue ($optional9->equals ($optional8));
	}


	/**
	 * @covers FunctionalPHP\common\Optional::get
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testGetWithNullContent() {

		$optional = new Optional (NULL);
		$optional->get();
	}


	/**
	 * @covers FunctionalPHP\common\Optional::get
	 */
	public function testGetWithNonNullContent() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);

		$optional1 = new Optional ($dummyObject1);
		$this->assertNotNull ($optional1->get());
		$this->assertEquals ($dummyObject1, $optional1->get());

		$optional2 = new Optional ($dummyObject2);
		$this->assertNotNull ($optional2->get());
		$this->assertEquals ($dummyObject2, $optional2->get());
	}


	/**
	 * @covers FunctionalPHP\common\Optional::isPresent
	 */
	public function testIsPresent() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$optional1 = new Optional (NULL);
		$this->assertFalse ($optional1->isPresent());

		$optional2 = new Optional ($dummyObject);
		$this->assertTrue ($optional2->isPresent());

		$optional3 = new Optional (123);
		$this->assertTrue ($optional3->isPresent());
	}


	/**
	 * @covers FunctionalPHP\common\Optional::orElse
	 */
	public function testOrElse() {

		$dummyObject1 = new DummyObject (1, "a", FALSE);
		$dummyObject2 = new DummyObject (2, "b", FALSE);

		$optional1 = new Optional (NULL);
		$this->assertEquals ($dummyObject1, $optional1->orElse ($dummyObject1));

		$optional2 = new Optional ($dummyObject2);
		$this->assertEquals ($dummyObject2, $optional2->orElse ($dummyObject1));
	}


	/**
	 * @covers FunctionalPHP\common\Optional::orElseThrow
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testOrElseThrowIllegalArgumentException() {

		$optional = new Optional (NULL);
		$optional->orElseThrow (new IllegalArgumentException ("testOrElseThrowIllegalArgumentException"));
	}


	/**
	 * @covers FunctionalPHP\common\Optional::orElseThrow
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testOrElseThrowUnsupportedOperationException() {

		$optional = new Optional (NULL);
		$optional->orElseThrow (new UnsupportedOperationException ("testOrElseThrowUnsupportedOperationException"));
	}


	/**
	 * @covers FunctionalPHP\common\Optional::orElseThrow
	 */
	public function testOrElseThrowNoException() {

		$dummyObject = new DummyObject (1, "a", FALSE);

		$optional = new Optional ($dummyObject);
		$this->assertEquals ($dummyObject
				            ,$optional->orElseThrow (new UnsupportedOperationException ("This exception won't be threw")));
	}

}

?>