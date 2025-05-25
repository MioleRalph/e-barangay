<?php 
    //to display errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);


   //DATABASE CONNECTION FOR ACCOUNTS
   $db_name = 'mysql:host=localhost;dbname=barangay_assistance';
   $db_user_name = 'root';
   $db_user_pass = '';

   $connection = new PDO($db_name, $db_user_name, $db_user_pass);

   if(isset($_COOKIE['user_id'])){
      $user_id = $_COOKIE['user_id'];
   }else{
      $user_id = '';
   }
?>