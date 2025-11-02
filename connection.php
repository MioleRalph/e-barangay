<?php 
    //to display errors
    error_reporting(E_ALL);
    ini_set('display_errors', 1);


   // $db_name = 'mysql:host=localhost;dbname=barangay_assistance';
   // $db_user_name = 'root';
   // $db_user_pass = '';

   $db_name = 'mysql:host=localhost;dbname=u590380520_e_barangay';
   $db_user_name = 'u590380520_barangay';
   $db_user_pass = 'barangayQ2001';


   $connection = new PDO($db_name, $db_user_name, $db_user_pass);

   if(isset($_COOKIE['user_id'])){
      $user_id = $_COOKIE['user_id'];
   }else{
      $user_id = '';
   }

define('ENCRYPTION_KEY', 'your-32-character-secret-key-1234');
define('ENCRYPTION_IV', substr(hash('sha256', 'iv-for-aes-256'), 0, 16));

?>