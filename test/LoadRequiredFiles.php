<?php

/**
 * FunctionalPHP
 */
require_once __DIR__."/../common/functional/Predicate.interface.php";
require_once __DIR__."/../common/functional/Stream.interface.php";
require_once __DIR__."/../common/functional/CompositePredicate.class.php";
require_once __DIR__."/../common/functional/BasicStream.class.php";
require_once __DIR__."/../common/util/ArrayUtil.class.php";
require_once __DIR__."/../common/Comparable.interface.php";
require_once __DIR__."/../common/Comparator.interface.php";
require_once __DIR__."/../common/Object.class.php";
require_once __DIR__."/../common/Optional.class.php";

require_once __DIR__."/../exception/IllegalArgumentException.class.php";
require_once __DIR__."/../exception/UnsupportedOperationException.class.php";

require_once __DIR__."/../iterable/Iterable.interface.php";
require_once __DIR__."/../iterable/AbstractIterable.class.php";

require_once __DIR__."/../iterable/collection/Collection.interface.php";
require_once __DIR__."/../iterable/collection/AbstractCollection.class.php";

require_once __DIR__."/../iterable/map/EntryMap.interface.php";
require_once __DIR__."/../iterable/map/Map.interface.php";
require_once __DIR__."/../iterable/map/AbstractMap.class.php";
require_once __DIR__."/../iterable/map/HashMap.class.php";

require_once __DIR__."/../iterable/collection/lists/Lists.interface.php";
require_once __DIR__."/../iterable/collection/lists/AbstractLists.class.php";
require_once __DIR__."/../iterable/collection/lists/ArrayList.class.php";

require_once __DIR__."/../iterable/collection/queue/Queue.interface.php";
require_once __DIR__."/../iterable/collection/queue/AbstractQueue.class.php";
require_once __DIR__."/../iterable/collection/queue/PriorityQueue.class.php";

require_once __DIR__."/../iterable/collection/set/Set.interface.php";
require_once __DIR__."/../iterable/collection/set/AbstractSet.class.php";
require_once __DIR__."/../iterable/collection/set/HashSet.class.php";
require_once __DIR__."/../iterable/collection/set/SortedSet.class.php";


/**
 * FunctionalPHP/test
 */
require_once __DIR__."/DummyObject.class.php";
require_once __DIR__."/DummyObjectComparator.class.php";
require_once __DIR__."/DummyPredicates.class.php";

?>