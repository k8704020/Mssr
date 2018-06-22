<?php
ob_start(); 
session_start();  

  unset($_SESSION["book_level_user_id"]);
  unset($_SESSION["book_level_permission"]);
  unset($_SESSION["book_level_name"]);


  // header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');

?>