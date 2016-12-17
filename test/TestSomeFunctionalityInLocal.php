<?php

require_once 'LoadRequiredFiles.php';

use FunctionalPHP\collection\lists\ArrayList;

use FunctionalPHP\collection\map\HashMap;
use FunctionalPHP\collection\map\Map;

use FunctionalPHP\collection\set\HashSet;
use FunctionalPHP\collection\set\SortedSet;

use FunctionalPHP\common\Object;
use FunctionalPHP\common\functional\Predicate;
use FunctionalPHP\common\functional\CompositePredicate;
use FunctionalPHP\common\functional\Stream;
use FunctionalPHP\common\functional\BasicStream;

use FunctionalPHP\test\DummyObject;
use FunctionalPHP\test\DummyObjectComparator;
use FunctionalPHP\test\IsIntPredicate;
use FunctionalPHP\test\HasDummyObjectOddIntPropertyPredicate;
use FunctionalPHP\test\HasDummyObjectStringPropertyOfTwoCharactersPredicate;
use FunctionalPHP\test\IsFloatTheIntPropertyOfDummyObjectPredicate;
use FunctionalPHP\test\IsIntTheIntPropertyOfDummyObjectPredicate;


/*
function cmp($a, $b)
{
	if ($a == $b) {
		return 0;
	}
	return ($a < $b) ? -1 : 1;
}

$a = array();

usort($a, function ($a, $b)
{
	if ($a == $b) {
		return 0;
	}
	return ($a < $b) ? -1 : 1;
});

foreach ($a as $clave => $valor) {
	echo "$clave: $valor\n";
}
*/








/*
$dummyObject1 = new DummyObject (1, "a", FALSE);
$dummyObject2 = new DummyObject (2, "b", FALSE);
$dummyObject3 = new DummyObject (3, "c", FALSE);


$hashMap = new HashMap (Map::keyNumericType);

$hashMap->put ($dummyObject1->intProperty, $dummyObject1);
$hashMap->put ($dummyObject2->intProperty, $dummyObject2);
*/



//var_export ((new CompositePredicate (new IsIntP()))->and(new IsNumericP())->test(12));



/*
$arrayList = new ArrayList();
$arrayList->add (new DummyObject (1, "a", FALSE));
$arrayList->add (new DummyObject (2, "b", FALSE));

$basicStream = new BasicStream ($arrayList->iterator());

$basicStream->forEach (function (DummyObject $dummyObject) {
	                      $dummyObject->intProperty *= 2;
                       });

var_export($basicStream->toArray());
*/



/*
function test (\Closure $callable) {

	echo $callable ("A");
}


test (function (string $a) : int {
	return strlen ($a);
});
*/


/*
$arrayList = new ArrayList();
$arrayList->add (new DummyObject (1, "a", TRUE));

$basicStream = new BasicStream ($arrayList->iterator());

$basicStream->forEach (function (int $a, string $b) {
	                      $a += 1;
                       });

var_export ($basicStream->toArray());
*/


/*
function test (\Closure $callable) {

	$callableFunction = new ReflectionFunction ($callable);
	$returnType = (string) $callableFunction->getReturnType();

	echo "RT: ",$returnType;
	if ($returnType == DummyObject::class) {
		echo "returnType == DO";
	}
	if (!$callableFunction->hasReturnType()) {
		echo "NOO RT";
	}

	$p1 = $callableFunction->getParameters()[2];

	$a = (string)$p1->getType();

	echo $a;


	$r = new ReflectionClass($returnType);
	if ($r->  isSubclassOf(Object::class)) {
		echo "is subclass";
	}
	else
		echo "NOOOOO";

	//$callableFunction = new ReflectionMethod($class_method)Function ($callable);

}


test (function (string $a, int $b, Object $c) : Object {
	     return strlen ($a) + $b;
      });


/*
	function to_int(bool $param, Object $p2) : int {
		return (int) $param;
	}

	$reflection1 = new ReflectionFunction(function (string $param) : int {
		return $param;
	});

	echo $reflection1->getReturnType();

	print_r ($reflection1->getParameters());

	/*
	$p = new ReflectionParameter('to_int', 'p2');
	$a = (string)$p->getType();

	echo $a;
	*/






//$hashMap->put ("AA", new DummyObject (1, "a", FALSE));
//$hashMap->put ("AA", new DummyObject (2, "b", FALSE));









//$hashMap->put ("BB", new DummyObject (3, "c", FALSE));


/*
foreach ($hashMap->iterator() as $v) {

	echo "\n".$v;
}

foreach ($hashMap->iterator() as $k => $v) {

	echo "\nK: ".$k." V: ".$v;
}
*/

/*
echo "\n".get_class($hashMap);

$p = new ReflectionMethod ( 'FunctionalPHP\test\DummyObjectComparator', 'compare' );

//Loop through each parameter and get the type
foreach($p as $param)
{
	//Before you call getClass() that class must be defined!
	echo $param->getClass()->name;
}
*/



/*
$arrayList = new ArrayList();
$arrayList->add(new DummyObject (1, "a", FALSE));
$arrayList->add(new DummyObject (2, "b", FALSE));

foreach ($arrayList->iterator() as $k => $v) {

	echo "\nK: ".$k." V: ".$v;
}
*/





/*
$numElements = 10000;


echo "\n--------- ARRAYLIST ---------";

$start = microtime(true);

$arrayList = new ArrayList();
for ($i = 0; $i < $numElements; $i++)
	$arrayList->add (new DummyObject ($i, "A".$i, FALSE));

echo "\nCompleted [ArrayList.add] ", microtime(true) - $start, " Seconds";
echo "\n Size: ".$arrayList->size();

$start = microtime(true);

for ($i = ($numElements-1); $i >= 0; $i--)
	$arrayList->remove (new DummyObject ($i, "A".$i, FALSE));

echo "\nCompleted [ArrayList.remove] ", microtime(true) - $start, " Seconds";
echo "\n Size: ".$arrayList->size()."\n";



echo "\n--------- HASHSET ---------";

$start = microtime(true);

$hashSet = new HashSet();
for ($i = 0; $i < $numElements; $i++)
	$hashSet->add (new DummyObject ($i, "A".$i, FALSE));

echo "\nCompleted [HashSet.add] ", microtime(true) - $start, " Seconds";
echo "\n Size: ".$hashSet->size();

$start = microtime(true);

for ($i = ($numElements-1); $i >= 0; $i--)
	$hashSet->remove (new DummyObject ($i, "A".$i, FALSE));

echo "\nCompleted [HashSet.remove] ", microtime(true) - $start, " Seconds";
echo "\n Size: ".$hashSet->size()."\n";
*/


/*
--------- ARRAYLIST ---------
Completed [ArrayList.add] 0.0024480819702148 Seconds
Size: 5000
Completed [ArrayList.remove] 1.1928398609161 Seconds
Size: 0

--------- HASHSET ---------
Completed [HashSet.add] 0.094861030578613 Seconds
Size: 5000
Completed [HashSet.remove] 0.10201287269592 Seconds
Size: 0
*/

/*
--------- ARRAYLIST ---------

Completed [ArrayList.add] 0.0036849975585938 Seconds
Size: 10000
Completed [ArrayList.remove] 4.4271099567413 Seconds
Size: 0

--------- HASHSET ---------
Completed [HashSet.add] 0.3690299987793 Seconds
Size: 10000
Completed [HashSet.remove] 0.48289918899536 Seconds
Size: 0
*/

?>