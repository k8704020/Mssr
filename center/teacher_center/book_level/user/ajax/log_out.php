<?php
ob_start(); 
session_start();  

  unset($_SESSION["user_id"]);
  unset($_SESSION["permission"]);
  unset($_SESSION["name"]);

  // header('Location:http://www.cot.org.tw/mssr/center/teacher_center/book_level/user/index.php');

?>