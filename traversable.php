<?php

/**
 * The Traversable interface
 * 
 * Interface to detect if a class is traversable using foreach.
 * 
 * Abstract base interface that cannot be implemented alone. 
 * Instead it must be implemented by either 
 * 
 * @error Class Test must implement interface Traversable as part of 
 * either Iterator or IteratorAggregate. you cann't decare this interface
 * becouse it comes from php core.
 * 
 * @desc This is an internal engine interface which 
 *       cannot be implemented in PHP scripts.
 */
 

interface Traversable{
    
}

class Test implements Traversable{
    
}


// if( !is_array( $items ) && !$items instanceof Traversable ){
//     
// }