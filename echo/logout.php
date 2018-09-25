<?php session_start();
  include_once 'scripts/common.php';
  include_once 'scripts/generateLogin.php';

  //if session is not set, go back home!
  if(!isset($_SESSION['auth'])){
    header("Location: http://localhost:8888/echo/index.php", true, 301);
    die();
  }

  //print out new join url
  echo('http://localhost:8888/echo/login.php?user=' . $_SESSION['user'] . '/');

  //destroy session
  session_destroy();
?>

<h2>goodbye.</h2>

<p>
  to continue, copy this link and click it anytime!
</p>
