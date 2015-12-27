<?php
include 'lib.php';
$id = $_GET['id'];

$users = get_all_users();

echo '<pre>',var_dump($users);


echo '<pre>',var_dump($users[$id]);

