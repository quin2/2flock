<?php
include_once 'scripts/common.php';
include 'scripts/generateJoinCode.php';

  //clean group name for any malicious characters
  $spaceName = htmlentities($_POST['spaceName'], ENT_QUOTES);
  $spaceName = $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $spaceName);

  //check to see if length is valid
  if(strlen($spaceName) > 16){
    echo('invalid group name :(');
    return;
  }

  //post group to $database
  $db = getDB();
  try{
    $stmt = $db->prepare("INSERT INTO groups (group_name) VALUES (:spaceName)");
    $stmt->execute(array(':spaceName' => $spaceName));
    $id = $db->lastInsertId();
  }
  catch (Exception $e) {
    return false;
  }

  //generate unique join key and stash into key database w/5 minute expiration
  $newJoinCode = getCode($id, 1);

  echo('<h1>Success!</h1>');
  echo('<p>use the code ' . $newJoinCode . ' to log in <a href="http://localhost:8888/echo/index.php">here</a>!</p>');
?>
