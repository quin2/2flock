<?php session_start();

  //redirect cleared users to homepage, BUT WHAT IF JOIN IS SENT???
  if(isset($_SESSION['auth']) && !isset($_GET['groupCode'])){
      header("Location: http://localhost:8888/echo/home.php", true, 301);
      die();
  }

  //redirect current user to homepage if join is entered
  if(isset($_SESSION['auth']) && isset($_GET['groupCode'])){
      $groupCode = htmlentities($_GET['groupCode'], ENT_QUOTES);
      $groupCode = strtoupper($groupCode);
      header("Location: http://localhost:8888/echo/home.php?join=" . $groupCode, true, 301);
      die();
  }

  //includes!!!!!!
  include_once 'scripts/common.php';
  include_once 'scripts/validCode.php';
  include_once 'scripts/generateLogin.php';
  include_once 'scripts/joinGroup.php';

  //runs on form submission, or enter of join string from URL
  if(isset($_POST['join']) || isset($_GET['join'])){
    //clear group code of malicious content, and generate new user ID
    $groupCode = htmlentities($_POST['groupCode'], ENT_QUOTES);
    $newUserId = uniqueLogin($groupCode);

    joinGroup($groupCode, $newUserId);

    //store in session cookies
    $_SESSION['auth'] = true;
    $_SESSION['user'] = $newUserId;

    //redirect to home.php
    header("Location: http://localhost:8888/echo/home.php", true, 301);
    die();
  }
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>echospace login</title>
</head>

<body>
  <h1>echo_</h1>
  <h3>join space</h3>
  <form method="post" action="index.php">
    <label for="groupCode">space code:</label>
    <input name="groupCode" type="password" size="8" maxlength="6"/>
    <input name="join" type="submit" value="join" />
  </form>

  <h3>create space</h3>
  <form method="post" action="newspace.php">
    <label for="spaceName">name:</label>
    <input name="spaceName" type="text" size="18" maxlength="16"/>
    <input type="submit" value="next" />
  </form>

  <!--note: add capicha to both forms to make life harder for brute-forcing-->

  <h6>powered by x</h6>
</body>

</html>
