<?php

namespace FunctionalPHP\test\common\functional;

require_once '../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\common\functional\CompositePredicate;
use FunctionalPHP\test\IsFloatPredicate;
use FunctionalPHP\test\IsIntPredicate;
use FunctionalPHP\test\IsNumericPredicate;
use FunctionalPHP\test\IsPrimePredicate;

/**
 * Class used to test FunctionalPHP\common\functional\CompositePredicate
 */
final class CompositePredicateTest extends TestCase {

	/**
	 * @covers FunctionalPHP\common\functional\CompositePredicate::test
	 */
	public function testSinglePredicates() {

		// IsIntPredicate
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->test());
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->test (12.1));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->test (12, 13.2));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->test ("AA"));

		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->test (12));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->test (12, 13));

		// IsFloatPredicate
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->test());
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->test (12));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->test (12.1, 13));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->test ("AA"));

		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->test (12.1));
		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->test (12.1, 13.2));

		// IsNumericPredicate
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->test());
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->test (12, "AA"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->test ("AA"));

		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->test (12.1));
		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->test (12.1, 13));

		// IsPrimePredicate
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->test());
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->test (2, "AA"));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->test ("AA"));

		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->test (2));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->test (2, 7));
	}


	/**
	 * @covers FunctionalPHP\common\functional\CompositePredicate::and
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testAndWithNullPredicateToJoin() {

		(new CompositePredicate (new IsNumericPredicate()))->and();
	}


	/**
	 * @covers FunctionalPHP\common\functional\CompositePredicate::and
	 */
	public function testAndWithNonNullPredicatesToJoin() {

		// IsFloatPredicate and IsIntPredicate
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->and (new IsIntPredicate())->test());
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->and (new IsIntPredicate())->test (12));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->and (new IsIntPredicate())->test (12, 13));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->and (new IsFloatPredicate())->test (12));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->and (new IsFloatPredicate())->test (12, 13));

		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->and (new IsIntPredicate())->test (12.2));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->and (new IsIntPredicate())->test (12.2, 13.1));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->and (new IsFloatPredicate())->test (12.2));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->and (new IsFloatPredicate())->test (12.2, 13.1));

		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->and (new IsIntPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->and (new IsIntPredicate())->test ("A", "B"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->and (new IsFloatPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->and (new IsFloatPredicate())->test ("A", "B"));

		// IsPrimePredicate and IsNumericPredicate
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->and (new IsNumericPredicate())->test());
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->and (new IsNumericPredicate())->test (12.2));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->and (new IsNumericPredicate())->test (12, 13.1));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->test (12.2));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->test (12, 13.1));

		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->and (new IsNumericPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->and (new IsNumericPredicate())->test ("A", "B"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->test ("A", "B"));

		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->test (2));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->and (new IsNumericPredicate())->test (2));

		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->test (2, 7));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->and (new IsNumericPredicate())->test (2, 7));

		// IsPrimePredicate and IsNumericPredicate and IsIntPredicate
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->and (new IsIntPredicate())->test());
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->and (new IsIntPredicate())->test (4));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->and (new IsIntPredicate())->and (new IsNumericPredicate())->test (4));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->and (new IsNumericPredicate())->and (new IsPrimePredicate())->test (4));

		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->and (new IsIntPredicate())->test (4, 7));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->and (new IsIntPredicate())->and (new IsNumericPredicate())->test (4, 7));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->and (new IsNumericPredicate())->and (new IsPrimePredicate())->test (4, 7));

		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->and (new IsIntPredicate())->test (7));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->and (new IsIntPredicate())->and (new IsNumericPredicate())->test (7));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->and (new IsNumericPredicate())->and (new IsPrimePredicate())->test (7));

		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->and (new IsPrimePredicate())->and (new IsIntPredicate())->test (2, 7));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->and (new IsIntPredicate())->and (new IsNumericPredicate())->test (2, 7));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->and (new IsNumericPredicate())->and (new IsPrimePredicate())->test (2, 7));
	}


	/**
	 * @covers FunctionalPHP\common\functional\CompositePredicate::not
	 */
	public function testNotPredicate() {

		// IsIntPredicate
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->not()->test());
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->not()->test (12.1));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->not()->test (12, 13.2));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->not()->test ("AA"));

		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->not()->test (12));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->not()->test (12, 13));

		// IsFloatPredicate
		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->not()->test());
		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->not()->test (12));
		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->not()->test (12.1, 13));
		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->not()->test ("AA"));

		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->not()->test (12.1));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->not()->test (12.1, 13.2));

		// IsNumericPredicate
		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->not()->test());
		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->not()->test (12, "AA"));
		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->not()->test ("AA"));

		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->not()->test (12.1));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->not()->test (12.1, 13));

		// IsPrimePredicate
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->not()->test());
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->not()->test (2, "AA"));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->not()->test ("AA"));

		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->not()->test (2));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->not()->test (2, 7));
	}


	/**
	 * @covers FunctionalPHP\common\functional\CompositePredicate::or
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testOrWithNullPredicateToJoin() {

		(new CompositePredicate (new IsNumericPredicate()))->or();
	}


	/**
	 * @covers FunctionalPHP\common\functional\CompositePredicate::or
	 */
	public function testOrWithNonNullPredicatesToJoin() {

		// IsFloatPredicate and IsIntPredicate
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->or (new IsIntPredicate())->test());
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->or (new IsIntPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->or (new IsIntPredicate())->test ("A", "B"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->or (new IsIntPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->or (new IsIntPredicate())->test ("A", "B"));

		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->or (new IsIntPredicate())->test (12, "A"));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->or (new IsIntPredicate())->test (12, 13, "A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->or (new IsFloatPredicate())->test (12, "A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->or (new IsFloatPredicate())->test (12, 13, "A"));

		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->or (new IsIntPredicate())->test (12.2));
		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->or (new IsIntPredicate())->test (12.2, 13.1));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->or (new IsFloatPredicate())->test (12.2));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->or (new IsFloatPredicate())->test (12.2, 13.1));

		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->or (new IsIntPredicate())->test (12));
		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->or (new IsIntPredicate())->test (12, 13));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->or (new IsFloatPredicate())->test (12));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->or (new IsFloatPredicate())->test (12, 13));

		// IsPrimePredicate and IsNumericPredicate
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->or (new IsNumericPredicate())->test());
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->or (new IsNumericPredicate())->test (12.2, "A"));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->or (new IsNumericPredicate())->test (12, 13.1, "A"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->test (12.2, "A"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->test (12, 13.1, "A"));

		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->or (new IsNumericPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->or (new IsNumericPredicate())->test ("A", "B"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->test ("A", "B"));

		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->or (new IsNumericPredicate())->test (4, 12.2));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->or (new IsNumericPredicate())->test (4, 12));
		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->test (4, 12.2));
		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->test (4, 12));

		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->test (2, 7));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->or (new IsNumericPredicate())->test (2, 7));

		// IsPrimePredicate and IsNumericPredicate and IsIntPredicate
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->or (new IsIntPredicate())->test());
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->or (new IsIntPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->or (new IsIntPredicate())->or (new IsNumericPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->or (new IsNumericPredicate())->or (new IsPrimePredicate())->test ("A"));

		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->or (new IsIntPredicate())->test (4, "A"));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->or (new IsIntPredicate())->or (new IsNumericPredicate())->test (4, "A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->or (new IsNumericPredicate())->or (new IsPrimePredicate())->test (4, "A"));

		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->or (new IsIntPredicate())->test (8));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->or (new IsIntPredicate())->or (new IsNumericPredicate())->test (8));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->or (new IsNumericPredicate())->or (new IsPrimePredicate())->test (8));

		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->or (new IsPrimePredicate())->or (new IsIntPredicate())->test (2, 7));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->or (new IsIntPredicate())->or (new IsNumericPredicate())->test (2, 7));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->or (new IsNumericPredicate())->or (new IsPrimePredicate())->test (2, 7));
	}


	/**
	 * @covers FunctionalPHP\common\functional\CompositePredicate::xor
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testXorWithNullPredicateToJoin() {

		(new CompositePredicate (new IsNumericPredicate()))->xor();
	}


	/**
	 * @covers FunctionalPHP\common\functional\CompositePredicate::or
	 */
	public function testXorWithNonNullPredicatesToJoin() {

		// IsFloatPredicate and IsIntPredicate
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test());
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test ("A", "B"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->xor (new IsFloatPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->xor (new IsFloatPredicate())->test ("A", "B"));

		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test (12, "A"));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test (12, 13, "A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->xor (new IsFloatPredicate())->test (12, "A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->xor (new IsFloatPredicate())->test (12, 13, "A"));

		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test (12, 13.2));
		$this->assertFalse ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test (12, 13.2));

		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test (12.2));
		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test (12.2, 13.1));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->xor (new IsFloatPredicate())->test (12.2));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->xor (new IsFloatPredicate())->test (12.2, 13.1));

		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test (12));
		$this->assertTrue ((new CompositePredicate (new IsFloatPredicate()))->xor (new IsIntPredicate())->test (12, 13));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->xor (new IsFloatPredicate())->test (12));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->xor (new IsFloatPredicate())->test (12, 13));

		// IsPrimePredicate and IsNumericPredicate
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsNumericPredicate())->test());
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsNumericPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsNumericPredicate())->test ("A", "B"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->test ("A", "B"));

		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsNumericPredicate())->test (12, "A"));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsNumericPredicate())->test (12, 13, "A"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->test (12, "A"));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->test (12, 13, "A"));

		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->test (2));
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->test (2, 7));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsNumericPredicate())->test (2));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsNumericPredicate())->test (2, 7));

		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->test (4));
		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->test (4, 7));
		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->test (4, 8));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsNumericPredicate())->test (4));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsNumericPredicate())->test (4, 7));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsNumericPredicate())->test (4, 8));

		// IsPrimePredicate and IsNumericPredicate and IsIntPredicate
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->xor (new IsIntPredicate())->test());
		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->xor (new IsIntPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsIntPredicate())->xor (new IsNumericPredicate())->test ("A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->xor (new IsNumericPredicate())->xor (new IsPrimePredicate())->test ("A"));

		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->xor (new IsIntPredicate())->test (4, "A"));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsIntPredicate())->xor (new IsNumericPredicate())->test (4, "A"));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->xor (new IsNumericPredicate())->xor (new IsPrimePredicate())->test (4, "A"));

		$this->assertFalse ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->xor (new IsIntPredicate())->test (8));
		$this->assertFalse ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsIntPredicate())->xor (new IsNumericPredicate())->test (8));
		$this->assertFalse ((new CompositePredicate (new IsIntPredicate()))->xor (new IsNumericPredicate())->xor (new IsPrimePredicate())->test (8));

		$this->assertTrue ((new CompositePredicate (new IsNumericPredicate()))->xor (new IsPrimePredicate())->xor (new IsIntPredicate())->test (12.2));
		$this->assertTrue ((new CompositePredicate (new IsPrimePredicate()))->xor (new IsIntPredicate())->xor (new IsNumericPredicate())->test (12.2));
		$this->assertTrue ((new CompositePredicate (new IsIntPredicate()))->xor (new IsNumericPredicate())->xor (new IsPrimePredicate())->test (12.2));
	}


	/**
	 * @covers FunctionalPHP\common\functional\CompositePredicate::and
	 * @covers FunctionalPHP\common\functional\CompositePredicate::not
	 * @covers FunctionalPHP\common\functional\CompositePredicate::or
	 * @covers FunctionalPHP\common\functional\CompositePredicate::xor
	 */
	public function testCombineLogicalOperations() {

		// (IsNumericPredicate AND IsIntPredicate AND IsPrimePredicate) OR IsFloatPredicate
		$compositePredicate = (new CompositePredicate (new IsNumericPredicate()))->and (new IsIntPredicate(), new IsPrimePredicate())
				                                                                 ->or (new IsFloatPredicate());
		$this->assertFalse ($compositePredicate->test());
		$this->assertFalse ($compositePredicate->test (12));
		$this->assertFalse ($compositePredicate->test (12, 13));
		$this->assertFalse ($compositePredicate->test (4, 7));
		$this->assertFalse ($compositePredicate->test ("A"));
		$this->assertFalse ($compositePredicate->test ("A", "B"));
		$this->assertFalse ($compositePredicate->test (12, "A"));

		$this->assertTrue ($compositePredicate->test (2));
		$this->assertTrue ($compositePredicate->test (2, 7));
		$this->assertTrue ($compositePredicate->test (12.1));
		$this->assertTrue ($compositePredicate->test (12.1, 13.2));

		// (IsIntPredicate XOR IsFloatPredicate) AND !IsPrimePredicate
		$compositePredicate = (new CompositePredicate (new IsIntPredicate()))->xor (new IsFloatPredicate())
		                                                                     ->and ((new CompositePredicate (new IsPrimePredicate()))->not());
        $this->assertFalse ($compositePredicate->test());
        $this->assertFalse ($compositePredicate->test (2));
        $this->assertFalse ($compositePredicate->test (2, 7));
        $this->assertFalse ($compositePredicate->test ("A"));
        $this->assertFalse ($compositePredicate->test ("A", "B"));
        $this->assertFalse ($compositePredicate->test (12, "A"));

        $this->assertTrue ($compositePredicate->test (4));
        $this->assertTrue ($compositePredicate->test (4, 8));
        $this->assertTrue ($compositePredicate->test (12.1));
        $this->assertTrue ($compositePredicate->test (12.1, 13.2));
	}

}

?>