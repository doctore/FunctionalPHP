# FunctionalPHP

- [Why this project was created?](#why-this-project-was-created)
- [Elements included in this project](#elements-included-in-this-project)
    - [Iterable](#iterable)
    - [Common classes](#common-classes)
    - [Functional programming features](#functional-programming-features)
- [Iterable in more detail](#iterable-in-more-detail)    

## Why this project was created?

The main purpose of this module is to help to PHP 7/Java 8 developers use functional programming in a similar way in both languages. So, despite the differences between Java and PHP, a Java 8 developer will be feel comfortable when he/she wants use functional programming in PHP 7, and a PHP 7 developer that uses this module won't need to learn a lot of things and new concepts when he/she wants to use functional programming in Java 8.

## Elements included in this project

Below is shown a brief introduction to the components included in this project:

### Iterable

* **ArrayList**: implementation of a list of elements (permits duplicates).
* **HashMap**: hash table used to store key-value elements (duplicate keys are not allowed).
* **HashSet**: implementation of a set of elements (does not permit duplicates).
* **PriorityQueue**: implementation of a queue that further provides a total ordering on its elements using its compareTo method (see Comparable interface) or an instance of Comparator (permits duplicates).
* **SortedSet**: implementation of a set of ordered elements using its compareTo method (see Comparable interface) or an instance of Comparator (does not permit duplicates).

### Common classes

* **Comparable**: this interface imposes a total ordering on the Objects of each class that implements it.
* **Comparator**: a comparison function, which imposes a total ordering on some collection of Objects.
* **Object**: root class of the hierarchy, that is, we only will be able to use ArrayList, HashSet, etc to store elements with Object as superclass.
* **Optional**: a container object which may or may not contain a non-null value. Useful to know if we can to access to the properties/method of the contained object due to it is not NULL

### Functional programming features

* **Predicate**: represents a predicate (boolean-valued function) of one argument.
* **CompositePredicate**: complex Predicate on which we can add several logical conditions like: AND, OR, etc
* **Stream**: interface used to provide of functional programming features to the collections: ArrayList, HashSet, PriorityQueue and SortedSet.
* **BasicStream**: implementation of the Stream interface.

## Iterable in more detail

Now it is necessary to learn more about the Iterable hierarchy for this reason, the first thing is to see an image with all interfaces and classes included in the Iterable module: 

![Alt text](/documentation/IterableUML.png?raw=true "Hierarchy of Iterable")

As you can see it is a little bit complex than the "list of iterables" in the previous section however, we only need to know (and use) that previous list: ArrayList, HashMap, HashSet, PriorityQueue and SortedSet, that is, the final classes.


