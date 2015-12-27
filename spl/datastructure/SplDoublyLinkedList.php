<?php

$list =  new SplDoublyLinkedList();
$list->push('a');
$list->add(1,'e');
$list->push('b');
$list->push('c');
$list->push('d');

$list->setIteratorMode(SplDoublyLinkedList::IT_MODE_FIFO);

$list->rewind();

while($list->valid()) {

    echo $list->current(),"\n";
    $list->next();
}
