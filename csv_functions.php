<?php
/**
Reading/Writing csv php provides us two functions

fgetcsv - for reading csv data by passing resource object .this function
          return one row each time when you call it. plase use while loop
          to iterator all rows from csv file.

fputcsv - for writing csv data on file by passing resource object with
          array data. below example

Note: resource must be writable/ readable
*/
if(is_writeable('data.csv')){
    echo 'write able';
}
$file_handle = fopen('data.csv','w+');

$vec = array(
                array(1,'shambhu kumar',25),
                array(2,'Vikash kumar',23),
                array(3,'rajesh kumar',33)   
            );

foreach($vec as $row) {
    
    fputcsv($file_handle,$row);
}
fclose($file_handle);

echo('the data.csv file created');

$file = fopen('data.csv','r+');
$csvdata = fgetcsv($file,10000);
print_r($csvdata);
