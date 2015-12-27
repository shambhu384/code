<?php
interface Collection{
	public function add(E e);
	public function addAll(Collection<? extends E> c);
    public function 	clear();
    public function 	contains(Object o);
	public function 	containsAll(Collection c);
	public function 	equals(Object o);
	public function 	hashCode();
	public function  	isEmpty();
	Iterator<E> 	iterator();
	public function Stream<E> parallelStream()
	public function 	remove(Object o);
	public function	removeAll(Collection c);
	public function 	removeIf(Predicate<? super E> filter)
	public function 	retainAll(Collection c)
	public function  	size()
	public function Spliterator<E> 	spliterator()
	public function Stream<E> 	stream()
	public function	toArray()
	public function 	toArray( a)
}