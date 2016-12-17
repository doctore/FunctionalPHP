<?php

namespace FunctionalPHP\test\common\functional;

require_once '../../LoadRequiredFiles.php';

use PHPUnit\Framework\TestCase;

use FunctionalPHP\collection\lists\ArrayList;
use FunctionalPHP\collection\queue\PriorityQueue;
use FunctionalPHP\common\Optional;
use FunctionalPHP\common\functional\BasicStream;
use FunctionalPHP\test\DummyObject;
use FunctionalPHP\test\DummyObjectComparator;
use FunctionalPHP\test\HasDummyObjectOddIntPropertyPredicate;
use FunctionalPHP\test\HasDummyObjectStringPropertyOfTwoCharactersPredicate;
use FunctionalPHP\test\IsFloatTheIntPropertyOfDummyObjectPredicate;
use FunctionalPHP\test\IsIntTheIntPropertyOfDummyObjectPredicate;


/**
 * Class used to test FunctionalPHP\common\functional\BasicStream
 */
final class BasicStreamTest extends TestCase {


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::__construct
	 */
	public function testCreateEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::__construct
	 */
	public function testCreateNotEmptyBasicStream() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::allMatch
	 */
	public function testAllMatchOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertTrue ($basicStream->allMatch (new HasDummyObjectOddIntPropertyPredicate()));
		$this->assertTrue ($basicStream->allMatch (new HasDummyObjectStringPropertyOfTwoCharactersPredicate()));
		$this->assertTrue ($basicStream->allMatch (new IsFloatTheIntPropertyOfDummyObjectPredicate()));
		$this->assertTrue ($basicStream->allMatch (new IsIntTheIntPropertyOfDummyObjectPredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::allMatch
	 */
	public function testAllMatchOfNotEmptyBasicStream() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertFalse ($basicStream->allMatch (new HasDummyObjectOddIntPropertyPredicate()));
		$this->assertFalse ($basicStream->allMatch (new HasDummyObjectStringPropertyOfTwoCharactersPredicate()));
		$this->assertFalse ($basicStream->allMatch (new IsFloatTheIntPropertyOfDummyObjectPredicate()));
		$this->assertTrue ($basicStream->allMatch (new IsIntTheIntPropertyOfDummyObjectPredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::anyMatch
	 */
	public function testAnyMatchOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertFalse ($basicStream->anyMatch (new HasDummyObjectOddIntPropertyPredicate()));
		$this->assertFalse ($basicStream->anyMatch (new HasDummyObjectStringPropertyOfTwoCharactersPredicate()));
		$this->assertFalse ($basicStream->anyMatch (new IsFloatTheIntPropertyOfDummyObjectPredicate()));
		$this->assertFalse ($basicStream->anyMatch (new IsIntTheIntPropertyOfDummyObjectPredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::anyMatch
	 */
	public function testAnyMatchOfNotEmptyBasicStream() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertTrue ($basicStream->anyMatch (new HasDummyObjectOddIntPropertyPredicate()));
		$this->assertTrue ($basicStream->anyMatch (new HasDummyObjectStringPropertyOfTwoCharactersPredicate()));
		$this->assertFalse ($basicStream->anyMatch (new IsFloatTheIntPropertyOfDummyObjectPredicate()));
		$this->assertTrue ($basicStream->anyMatch (new IsIntTheIntPropertyOfDummyObjectPredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::count
	 */
	public function testCount() {

		// Empty Stream
		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertEquals (0, $basicStream->count());

		// Not empty Stream
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertEquals ($arrayList->size(), $basicStream->count());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::distinct
	 */
	public function testDistinctOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->distinct();

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::distinct
	 */
	public function testDistinctStoringObjectsInsideBasicStream() {

		$arrayListWithDuplicates = $this->generateDummyArrayList();
		$arrayListWithoutDuplicates = $this->generateDummyArrayList (FALSE);

		$basicStream = new BasicStream ($arrayListWithDuplicates);
		$basicStream->distinct();

		$this->assertGreaterThan (0, $basicStream->count());
		$this->assertNotEquals ($arrayListWithDuplicates->size(), $basicStream->count());
		$this->assertEquals ($arrayListWithoutDuplicates->size(), $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		$this->assertEquals ($arrayListWithoutDuplicates->size(), count ($contentOfStream));

		for ($i = 0; $i < $arrayListWithoutDuplicates->size(); $i++)
			$this->assertEquals ($arrayListWithoutDuplicates->get($i), $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::distinct
	 */
	public function testDistinctStoringStringsInsideBasicStream() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		// Converts into a Stream of strings
		$basicStream->map (function (DummyObject $dummyObject) : string {
			                  return "sameString";
		                   });

		$this->assertGreaterThan (1, $basicStream->count());
		$this->assertEquals ($arrayList->size(), $basicStream->count());

		$basicStream->distinct();

		$this->assertEquals (1, $basicStream->count());

		$contentOfStream = $basicStream->toArray();

		$this->assertEquals (1, count ($contentOfStream));
		$this->assertEquals ("sameString", $contentOfStream[0]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::distinct
	 */
	public function testDistinctStoringIntInsideBasicStream() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		// Converts into a Stream of strings
		$basicStream->map (function (DummyObject $dummyObject) : int {
			                  return 24;
		                   });

		$this->assertGreaterThan (1, $basicStream->count());
		$this->assertEquals ($arrayList->size(), $basicStream->count());

		$basicStream->distinct();

		$this->assertEquals (1, $basicStream->count());

		$contentOfStream = $basicStream->toArray();

		$this->assertEquals (1, count ($contentOfStream));
		$this->assertEquals (24, $contentOfStream[0]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::distinct
	 */
	public function testDistinctStoringBoolInsideBasicStream() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		// Converts into a Stream of strings
		$basicStream->map (function (DummyObject $dummyObject) : bool {
			                  return TRUE;
		                   });

		$this->assertGreaterThan (1, $basicStream->count());
		$this->assertEquals ($arrayList->size(), $basicStream->count());

		$basicStream->distinct();

		$this->assertEquals (1, $basicStream->count());

		$contentOfStream = $basicStream->toArray();

		$this->assertEquals (1, count ($contentOfStream));
		$this->assertEquals (TRUE, $contentOfStream[0]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filter
	 */
	public function testFilterOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filter (new HasDummyObjectOddIntPropertyPredicate());
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		$basicStream = new BasicStream ($arrayList);
		$basicStream->filter (new HasDummyObjectStringPropertyOfTwoCharactersPredicate());
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		$basicStream = new BasicStream ($arrayList);
		$basicStream->filter (new IsFloatTheIntPropertyOfDummyObjectPredicate());
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		$basicStream = new BasicStream ($arrayList);
		$basicStream->filter (new IsIntTheIntPropertyOfDummyObjectPredicate());
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filter
	 */
	public function testFilterOnePredicate() {

		// IsIntTheIntPropertyOfDummyObjectPredicate
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filter (new IsIntTheIntPropertyOfDummyObjectPredicate());
		$this->assertEquals ($arrayList->size(), $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		$this->assertEquals ($arrayList->size(), count ($contentOfStream));

		for ($i = 0; $i < $arrayList->size(); $i++)
			$this->assertEquals ($arrayList->get($i), $contentOfStream[$i]);

		// IsFloatTheIntPropertyOfDummyObjectPredicate
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filter (new IsFloatTheIntPropertyOfDummyObjectPredicate());
		$this->assertEquals (0, $basicStream->count());

		// HasDummyObjectOddIntPropertyPredicate
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filter (new HasDummyObjectOddIntPropertyPredicate());
		$this->assertGreaterThan (0, $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertTrue ($contentOfStream[$i]->intProperty % 2 != 0);

		// HasDummyObjectStringPropertyOfTwoCharactersPredicate
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filter (new HasDummyObjectStringPropertyOfTwoCharactersPredicate());
		$this->assertGreaterThan (0, $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertTrue (strlen ($contentOfStream[$i]->stringProperty) == 2);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filter
	 */
	public function testFilterMoreThanOnePredicate() {

		// HasDummyObjectOddIntPropertyPredicate && HasDummyObjectStringPropertyOfTwoCharactersPredicate
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filter (new HasDummyObjectOddIntPropertyPredicate())
		            ->filter (new HasDummyObjectStringPropertyOfTwoCharactersPredicate());

        $this->assertGreaterThan (0, $basicStream->count());

        $contentOfStream = $basicStream->toArray();
        for ($i = 0; $i < count ($contentOfStream); $i++) {

        	$this->assertTrue ($contentOfStream[$i]->intProperty % 2 != 0);
        	$this->assertTrue (strlen ($contentOfStream[$i]->stringProperty) == 2);
        }
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaWithClosureWithMoreThanOneParameter() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filterByLambda (function (int $p1, string $p2) {
			                             $p1 += 1;
			                             $p2 .= "_test";
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaWithClosureWithParameterTypeDifferentOfStreamElements() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filterByLambda (function (string $p1) {
			                             $p1 .= "_test";
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaWithClosureWithInvalidReturnedType() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filterByLambda (function (DummyObject $dummyObject) : DummyObject {
			                             $dummyObject->intProperty *= 2;
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testFilterByLambdaOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filterByLambda (function (DummyObject $dummyObject) {
			                             $dummyObject->intProperty *= 2;
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 */
	public function testFilterByLambdaByOneIteration() {

		// Filter pair intProperty values
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filterByLambda (function (DummyObject $dummyObject) : bool {
			                             return $dummyObject->intProperty % 2 == 0;
		                              });

		$this->assertGreaterThan (0, $basicStream->count());
		$this->assertFalse ($arrayList->size() == $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals (0, $contentOfStream[$i]->intProperty % 2);

		// Filter stringProperty values with lenght = 3
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filterByLambda (function (DummyObject $dummyObject) : bool {
			                             return strlen ($dummyObject->stringProperty) == 3;
		                              });

		$this->assertGreaterThan (0, $basicStream->count());
		$this->assertFalse ($arrayList->size() == $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals (3, strlen ($contentOfStream[$i]->stringProperty));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::filterByLambda
	 */
	public function testFilterByLambdaByMoreThanOneIteration() {

		// Filter pair intProperty values
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->filterByLambda (function (DummyObject $dummyObject) : bool {
			                             return $dummyObject->intProperty % 2 == 0;
		                              })
		            ->filterByLambda (function (DummyObject $dummyObject) : bool {
		                              	 return strlen ($dummyObject->stringProperty) == 3;
		                              });

		$this->assertGreaterThan (0, $basicStream->count());
		$this->assertFalse ($arrayList->size() == $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++) {

			$this->assertEquals (0, $contentOfStream[$i]->intProperty % 2);
			$this->assertEquals (3, strlen ($contentOfStream[$i]->stringProperty));
		}
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::findFirst
	 */
	public function testFindFirstOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$optional = $basicStream->findFirst();
		$this->assertFalse ($optional->isPresent());

		// IsFloatTheIntPropertyOfDummyObjectPredicate
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$optional = $basicStream->filter (new IsFloatTheIntPropertyOfDummyObjectPredicate())
		                        ->findFirst();

		$this->assertFalse ($optional->isPresent());
		$this->assertEquals (0, $basicStream->count());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::findFirst
	 */
	public function testFindFirstOfNotEmptyBasicStream() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$optional = $basicStream->findFirst();
		$this->assertTrue ($optional->isPresent());

		// HasDummyObjectOddIntPropertyPredicate
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$optional = $basicStream->filter (new HasDummyObjectOddIntPropertyPredicate())
		                        ->findFirst();

		$this->assertTrue ($optional->isPresent());
		$this->assertTrue ($optional->get()->intProperty % 2 != 0);

		// HasDummyObjectStringPropertyOfTwoCharactersPredicate
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$optional = $basicStream->filter (new HasDummyObjectStringPropertyOfTwoCharactersPredicate())
		                        ->findFirst();

		$this->assertTrue ($optional->isPresent());
		$this->assertTrue (strlen ($optional->get()->stringProperty) == 2);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithMoreThanOneParameter() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->forEach (function (int $p1, string $p2) {
			                      $p1 += 1;
			                      $p2 .= "_test";
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithParameterTypeDifferentOfStreamElements() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->forEach (function (int $p1) {
		 	                      $p1 += 1;
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachWithClosureWithInvalidReturnedType() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->forEach (function (DummyObject $dummyObject) : int {
			                      $dummyObject->intProperty *= 2;
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::forEach
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testForEachOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->forEach (function (DummyObject $dummyObject) {
			                      $dummyObject->intProperty *= 2;
		                       });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::forEach
	 */
	public function testForEachOfNotEmptyBasicStream() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->forEach (function (DummyObject $dummyObject) {
			                      $dummyObject->intProperty *= 2;
		                       });

		// It is necessary "reinitialize" it due to the stream works with the same objects that "initial arrayList"
		$arrayList = $this->generateDummyArrayList();
		$contentOfStream = $basicStream->toArray();

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($contentOfStream));

		for ($i = 0; $i < $arrayList->size(); $i++)
			$this->assertEquals ($arrayList->get($i)->intProperty * 2, $contentOfStream[$i]->intProperty);

		// Using the filter HasDummyObjectStringPropertyOfTwoCharactersPredicate
		$arrayList = $this->generateDummyArrayList();
		$originalStream = new BasicStream ($arrayList);

		$originalStream->filter (new HasDummyObjectStringPropertyOfTwoCharactersPredicate());

		$arrayList = $this->generateDummyArrayList();
		$finalStream = new BasicStream ($arrayList);

		// Only applies forEach to the finalStream
		$finalStream->filter (new HasDummyObjectStringPropertyOfTwoCharactersPredicate())
		            ->forEach (function (DummyObject $dummyObject) {
			                      $dummyObject->stringProperty .= "_2";
		                       });

        $contentOfOriginalStream = $originalStream->toArray();
        $contentOfFinalStream    = $finalStream->toArray();

        $this->assertGreaterThan (0, count ($contentOfOriginalStream));
        $this->assertEquals (count ($contentOfOriginalStream), count ($contentOfFinalStream));

        for ($i = 0; $i < count ($contentOfOriginalStream); $i++)
        	$this->assertEquals ($contentOfOriginalStream[$i]->stringProperty . "_2"
        			            ,$contentOfFinalStream[$i]->stringProperty);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::limit
	 *
	 * @expectedException FunctionalPHP\exception\IllegalArgumentException
	 */
	public function testLimitWithMaxSizeLessThanZero() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->limit (-1);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::limit
	 */
	public function testLimitOfEmptyBasicStream() {

		// Limit = 0
		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->limit (0);
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		// Limit = 10
		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->limit (10);
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::limit
	 */
	public function testLimitOfNotEmptyBasicStream() {

		// Limit = 5
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->limit (5);

		$this->assertGreaterThan ($basicStream->count(), $arrayList->size());
		$this->assertEquals (5, $basicStream->count());
		$this->assertEquals (5, count ($basicStream->toArray()));

		// Limit = 10
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->limit (10);

		$this->assertGreaterThan ($basicStream->count(), $arrayList->size());
		$this->assertEquals (10, $basicStream->count());
		$this->assertEquals (10, count ($basicStream->toArray()));

		// Limit = $arrayList->size() + 1
		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->limit ($arrayList->size() + 1);

		$this->assertEquals ($arrayList->size(), $basicStream->count());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMapWithClosureWithMoreThanOneParameter() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->map (function (int $p1, string $p2) {
			                  $p1 += 1;
			                  $p2 .= "_test";
		                   });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMapWithClosureWithParameterTypeDifferentOfStreamElements() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->map (function (bool $p1) {
			                  $p1 = TRUE;
		                   });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMapWithClosureWithInvalidNativeReturnedType() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->map (function (DummyObject $dummyObject) : array {
			                  return array ($dummyObject->intProperty);
		                   });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMapWithClosureWithInvalidObjectReturnedType() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->map (function (DummyObject $dummyObject) : Object {
			                  return $dummyObject;
		                   });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testMapOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->map (function (DummyObject $dummyObject) : DummyObject {
			                  return $dummyObject;
		                   });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 */
	public function testMapConvertOriginalObjectIntoNativeType() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		// Converts into a Stream of strings
		$basicStream->map (function (DummyObject $dummyObject) : string {
			                  return "sameString";
		                   });

		$this->assertEquals ($arrayList->size(), $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ("sameString", $contentOfStream[$i]);

		// Initialize Stream and converts it into a Stream of float
		$basicStream = new BasicStream ($arrayList);

		$basicStream->map (function (DummyObject $dummyObject) : float {
				              return $dummyObject->intProperty * 0.1;
			               });

		$this->assertEquals ($arrayList->size(), $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($arrayList->get($i)->intProperty * 0.1, $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::map
	 */
	public function testMapAppliesMoreThanOnce() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		// Converts into a Stream of strings
		$basicStream->map (function (DummyObject $dummyObject) : string {
			                  return $dummyObject->stringProperty;
		                   });

		$this->assertEquals ($arrayList->size(), $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($arrayList->get($i)->stringProperty, $contentOfStream[$i]);

		// Convert current Stream of string into a Stream of int
		$basicStream->map (function (string $stringProperty) : int {
			return strlen ($stringProperty);
		});

		$this->assertEquals ($arrayList->size(), $basicStream->count());

		$contentOfStream = $basicStream->toArray();
		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals (strlen ($arrayList->get($i)->stringProperty), $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::noneMatch
	 */
	public function testNoneMatchOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertTrue ($basicStream->noneMatch (new HasDummyObjectOddIntPropertyPredicate()));
		$this->assertTrue ($basicStream->noneMatch (new HasDummyObjectStringPropertyOfTwoCharactersPredicate()));
		$this->assertTrue ($basicStream->noneMatch (new IsFloatTheIntPropertyOfDummyObjectPredicate()));
		$this->assertTrue ($basicStream->noneMatch (new IsIntTheIntPropertyOfDummyObjectPredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::noneMatch
	 */
	public function testNoneMatchOfNotEmptyBasicStream() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertFalse ($basicStream->noneMatch (new HasDummyObjectOddIntPropertyPredicate()));
		$this->assertFalse ($basicStream->noneMatch (new HasDummyObjectStringPropertyOfTwoCharactersPredicate()));
		$this->assertTrue ($basicStream->noneMatch (new IsFloatTheIntPropertyOfDummyObjectPredicate()));
		$this->assertFalse ($basicStream->noneMatch (new IsIntTheIntPropertyOfDummyObjectPredicate()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sorted
	 */
	public function testSortedOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		$basicStream->sorted();
		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sorted
	 */
	public function testSortedOfBasicStreamWithObjects() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$priorityQueue = new PriorityQueue ($arrayList);

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			if ($element->equals ($contentOfStream[$i]))
				$numberOfElementsInTheSamePosition++;

			$i++;
		}
		$this->assertGreaterThan (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, $priorityQueue->size());

		// After ordering the elements in the stream
		$basicStream->sorted();

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			$this->assertEquals ($element, $contentOfStream[$i]);
			$i++;
		}
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sorted
	 */
	public function testSortedOfBasicStreamWithIntegers() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$ordinationIntPropertyArray = array();
		foreach ($arrayList->iterator() as $element)
			$ordinationIntPropertyArray[] = $element->intProperty;

		sort ($ordinationIntPropertyArray);

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationIntPropertyArray), $basicStream->count());
		$this->assertEquals (count ($ordinationIntPropertyArray), count ($basicStream->toArray()));

		$basicStream->map (function (DummyObject $dummyObject) : int {
			                  return $dummyObject->intProperty;
		                   });

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		for ($i = 0; $i < count ($contentOfStream); $i++) {

			if ($contentOfStream[$i] === $ordinationIntPropertyArray[$i])
				$numberOfElementsInTheSamePosition++;
		}
		$this->assertGreaterThan (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, count ($ordinationIntPropertyArray));

		// After ordering the elements in the stream
		$basicStream->sorted();

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationIntPropertyArray), $basicStream->count());
		$this->assertEquals (count ($ordinationIntPropertyArray), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($ordinationIntPropertyArray[$i], $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sorted
	 */
	public function testSortedOfBasicStreamWithStrings() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$ordinationIntPropertyArray = array();
		foreach ($arrayList->iterator() as $element)
			$ordinationIntPropertyArray[] = $element->stringProperty;

		sort ($ordinationIntPropertyArray);

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationIntPropertyArray), $basicStream->count());
		$this->assertEquals (count ($ordinationIntPropertyArray), count ($basicStream->toArray()));

		$basicStream->map (function (DummyObject $dummyObject) : string {
			                  return $dummyObject->stringProperty;
		                   });

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		for ($i = 0; $i < count ($contentOfStream); $i++) {

			if ($contentOfStream[$i] === $ordinationIntPropertyArray[$i])
				$numberOfElementsInTheSamePosition++;
		}
		$this->assertGreaterThan (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, count ($ordinationIntPropertyArray));

		// After ordering the elements in the stream
		$basicStream->sorted();

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationIntPropertyArray), $basicStream->count());
		$this->assertEquals (count ($ordinationIntPropertyArray), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($ordinationIntPropertyArray[$i], $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByComparatorOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->sortedByComparator (new DummyObjectComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByComparatorOfBasicStreamWithStringElements() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);
		$basicStream->map (function (DummyObject $dummyObject) : string {
			                  return $dummyObject->stringProperty;
		                   });

		$basicStream->sortedByComparator (new DummyObjectComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByComparatorOfBasicStreamWithIntElements() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);
		$basicStream->map (function (DummyObject $dummyObject) : int {
			                  return $dummyObject->intProperty;
		                   });

		$basicStream->sortedByComparator (new DummyObjectComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByComparatorOfBasicStreamWithFloatElements() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);
		$basicStream->map (function (DummyObject $dummyObject) : float {
			                  return $dummyObject->intProperty * 0.1;
		                   });

		$basicStream->sortedByComparator (new DummyObjectComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByComparator
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByComparatorOfBasicStreamWithBoolElements() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);
		$basicStream->map (function (DummyObject $dummyObject) : bool {
			                  return TRUE;
		                   });

		$basicStream->sortedByComparator (new DummyObjectComparator());
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sorted
	 */
	public function testSortedByComparatorOfBasicStreamWithObjects() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$priorityQueue = new PriorityQueue ($arrayList, new DummyObjectComparator());

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			if ($element->equals ($contentOfStream[$i]))
				$numberOfElementsInTheSamePosition++;

				$i++;
		}
		$this->assertEquals (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, $priorityQueue->size());

		// After ordering the elements in the stream
		$basicStream->sortedByComparator (new DummyObjectComparator());

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			$this->assertEquals ($element, $contentOfStream[$i]);
			$i++;
		}
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByLambdaWithClosureWithDifferentOfTwoParameters() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->sortedByLambda (function (int $p) {
			                             $p += 1;
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByLambdaWithClosureWithParameterTypeDifferentOfStreamElements() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->sortedByLambda (function (string $p1, string $p2) : int {
			                             $p1 .= "_test";
			                             $p2 .= "_test";
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByLambdaWithClosureWithInvalidReturnedType() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$basicStream->sortedByLambda (function (DummyObject $dummyObject1, DummyObject $dummyObject2) : DummyObject {
			                             return $dummyObject1;
		                              });
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 *
	 * @expectedException FunctionalPHP\exception\UnsupportedOperationException
	 */
	public function testSortedByLambdaOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));

		$basicStream->sortedByLambda (function (DummyObject $dummyObject1, DummyObject $dummyObject2) : int {
			                             return $dummyObject1->intProperty;
		                              });

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 */
	public function testSortedByLambdaOfBasicStreamWithObjects() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$priorityQueue = new PriorityQueue ($arrayList, new DummyObjectComparator());

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			if ($element->equals ($contentOfStream[$i]))
				$numberOfElementsInTheSamePosition++;

			$i++;
		}
		$this->assertEquals (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, $priorityQueue->size());

		// After ordering the elements in the stream
		$basicStream->sortedByLambda (function (DummyObject $dummyObject1, DummyObject $dummyObject2): int {
		                                 return $dummyObject2->intProperty - $dummyObject1->intProperty;
	                                  });

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals ($priorityQueue->size(), $basicStream->count());
		$this->assertEquals ($priorityQueue->size(), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		$i = 0;
		foreach ($priorityQueue->iterator() as $element) {

			$this->assertEquals ($element, $contentOfStream[$i]);
			$i++;
		}
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 */
	public function testSortedByLambdaOfBasicStreamWithStrings() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$ordinationIntPropertyArray = array();
		foreach ($arrayList->iterator() as $element)
			$ordinationIntPropertyArray[] = $element->stringProperty;

		usort ($ordinationIntPropertyArray, function (string $string1, string $string2) : int {

			                                   if ($string1 == $string2)
				                                  return 0;

			                                   return ($string1 < $string2) ? -1 : 1;
		                                    });

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationIntPropertyArray), $basicStream->count());
		$this->assertEquals (count ($ordinationIntPropertyArray), count ($basicStream->toArray()));

		$basicStream->map (function (DummyObject $dummyObject) : string {
			return $dummyObject->stringProperty;
		});

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		for ($i = 0; $i < count ($contentOfStream); $i++) {

			if ($contentOfStream[$i] === $ordinationIntPropertyArray[$i])
				$numberOfElementsInTheSamePosition++;
		}
		$this->assertGreaterThan (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, count ($ordinationIntPropertyArray));

		// After ordering the elements in the stream
		$basicStream->sortedByLambda (function (string $string1, string $string2) : int {

			                             if ($string1 == $string2)
				                            return 0;

			                             return ($string1 < $string2) ? -1 : 1;
		                              });

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationIntPropertyArray), $basicStream->count());
		$this->assertEquals (count ($ordinationIntPropertyArray), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($ordinationIntPropertyArray[$i], $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::sortedByLambda
	 */
	public function testSortedByLambdaOfBasicStreamWithFloats() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$ordinationIntPropertyArray = array();
		foreach ($arrayList->iterator() as $element)
			$ordinationIntPropertyArray[] = $element->intProperty * 0.1;

		usort ($ordinationIntPropertyArray, function (float $float1, float $float2) : int {

			                                   if ($float1 == $float2)
				                                  return 0;

				                               return ($float1 < $float2) ? -1 : 1;
		                                    });

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationIntPropertyArray), $basicStream->count());
		$this->assertEquals (count ($ordinationIntPropertyArray), count ($basicStream->toArray()));

		$basicStream->map (function (DummyObject $dummyObject) : float {
			                  return $dummyObject->intProperty * 0.1;
		                   });

		// Initially not all elements in this stream are sorted
		$contentOfStream = $basicStream->toArray();
		$numberOfElementsInTheSamePosition = 0;

		for ($i = 0; $i < count ($contentOfStream); $i++) {

			if ($contentOfStream[$i] === $ordinationIntPropertyArray[$i])
				$numberOfElementsInTheSamePosition++;
		}
		$this->assertGreaterThan (0, $numberOfElementsInTheSamePosition);
		$this->assertGreaterThan ($numberOfElementsInTheSamePosition, count ($ordinationIntPropertyArray));

		// After ordering the elements in the stream
		$basicStream->sortedByLambda (function (float $float1, float $float2) : int {

		                                 if ($float1 == $float2)
			                                return 0;

			                             return ($float1 < $float2) ? -1 : 1;
		                              });

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));
		$this->assertEquals (count ($ordinationIntPropertyArray), $basicStream->count());
		$this->assertEquals (count ($ordinationIntPropertyArray), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($ordinationIntPropertyArray[$i], $contentOfStream[$i]);
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::toArray
	 */
	public function testToArrayOfEmptyBasicStream() {

		$arrayList = new ArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertEquals (0, $basicStream->count());
		$this->assertEquals (0, count ($basicStream->toArray()));
	}


	/**
	 * @covers FunctionalPHP\common\functional\BasicStream::toArray
	 */
	public function testToArrayOfNotEmptyBasicStream() {

		$arrayList = $this->generateDummyArrayList();
		$basicStream = new BasicStream ($arrayList);

		$this->assertEquals ($arrayList->size(), $basicStream->count());
		$this->assertEquals ($arrayList->size(), count ($basicStream->toArray()));

		$contentOfStream = $basicStream->toArray();

		for ($i = 0; $i < count ($contentOfStream); $i++)
			$this->assertEquals ($arrayList->get($i), $contentOfStream[$i]);
	}


	/**
	 * Uses in the different tests as "initial collection"
	 */
	private function generateDummyArrayList (bool $addDuplicates = TRUE) : ArrayList {

		$arrayList = new ArrayList();
		$arrayList->add (new DummyObject (1, "a", FALSE));
		$arrayList->add (new DummyObject (2, "b", FALSE));
		$arrayList->add (new DummyObject (3, "c", FALSE));
		$arrayList->add (new DummyObject (4, "d", FALSE));

		$arrayList->add (new DummyObject (5, "aa", TRUE));
		$arrayList->add (new DummyObject (6, "bb", TRUE));
		$arrayList->add (new DummyObject (7, "cc", TRUE));
		$arrayList->add (new DummyObject (8, "dd", TRUE));

		$arrayList->add (new DummyObject (9, "aaa", TRUE));
		$arrayList->add (new DummyObject (10, "bbb", FALSE));
		$arrayList->add (new DummyObject (11, "ccc", TRUE));
		$arrayList->add (new DummyObject (12, "ddd", FALSE));

		// Adds some duplicate elements
		if ($addDuplicates) {
			$arrayList->add (new DummyObject (1, "a", FALSE));
			$arrayList->add (new DummyObject (7, "cc", TRUE));
			$arrayList->add (new DummyObject (7, "cc", TRUE));
			$arrayList->add (new DummyObject (11, "ccc", TRUE));
			$arrayList->add (new DummyObject (12, "ddd", FALSE));
		}
		return $arrayList;
	}

}

?>