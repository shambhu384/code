<?php

/**
PHP provide to use count function in php objects
by implementing Countable interface.

*/


class Library implements Countable {
    
    var $books = array();

    public function count(){
        $num = count($this->books);    
        return $num;
    }

    public function addBook(Book $instance){
        $this->books[$instance->get_id()] = $instance;
    }
}
class Book {

    private $id;
    private $name;

    public function __construct($id, $name){
        $this->id = $id;
        $this->name =$name;
    }

    public function get_id() {
        return $this->id;
    }
}

$lib = new Library();

$lib->addBook(new Book(1,'Java'));
$lib->addBook(new Book(2,'PHP'));
$lib->addBook(new Book(3,'Moodle'));

echo count($lib);
