<?php
$_SERVER['HTTP_HOST']='localhost'; 
require 'c:\xampp\htdocs\pararbazar\config\database.php'; 
print_r(getDB()->query('SHOW COLUMNS FROM orders')->fetchAll(PDO::FETCH_ASSOC));
