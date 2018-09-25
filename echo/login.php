<?php session_start();

include_once 'scripts/common.php';
include_once 'scripts/generateLogin.php';

//check to see if ya already logged in!!
if(isset($SESSION_['auth'])){
    echo('already logged in!');
    header("Location: http://localhost:8888/echo/home.php", true, 301);
    die();
}

//check to see if login was even entered in the first place!
if(!isset($_GET['user']) && !isset($_POST['submit'])){
  echo('invalid login...sorry :(');
}

if(isset($_POST['submit'])){
  //get the user
  $userID = htmlentities($_POST['userHash'], ENT_QUOTES);

  //get the list of names
  $groupIds = [];
  foreach($_POST['authGroupList'] as $item){
    $groupIds[] = $item;
  }

  //get list of all groups the user is in
  $db = getDB();
  try{
    $stmt = $db->prepare("SELECT id FROM groups g JOIN users u ON g.id = u.group_joined WHERE u.user_id =:userID ORDER BY g.group_name DESC");
    $stmt->execute(array(':userID' => $userID));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  catch (Exception $e) {
    return false;
  }

  //if user is in no groups, kick them out!
  if(count($rows) == 0){
    echo("you don't seem to be a member here ;)");
    exit();
  }

  //move PDO array into regular array
  $allIds = [];
  for($i = 0; $i < count($rows); $i++){
    $allIds[] = $rows[$i]['id'];
  }

  //see if they match tf up (if array has value group list DOESN'T HAVE )
  foreach($groupIds as $idToCheck){
    if(!in_array($idToCheck, $allIds)){
      echo('you answered incorrectly :[ sorry, try logging on again!');
      exit();
    }
  }

  //on success, get new key and change it!
  $newUserId = uniqueLogin($userID);

  $db = getDB();
  try{
    $stmt = $db->prepare("UPDATE users SET user_id = REPLACE(user_id, :oldUserId, :newUserId) WHERE INSTR(user_id, :oldUserId)");
    $stmt->execute(array(':oldUserId' => $userID, ':newUserId' => $newUserId));
  }
  catch (Exception $e) {
    return false;
  }

  //set session variables and exit!
  $_SESSION['auth'] = true;
  $_SESSION['user'] = $newUserId;

  header("Location: http://localhost:8888/echo/home.php", true, 301);
  die();
}
?>

<h2>select all groups you were a part of</h2>
<h3>(we need to make sure it's really you!)</h3>

<form method="post" action="login.php">

<?php
if(isset($_GET['user'])){
  $userHash = htmlentities($_GET['user'], ENT_QUOTES);
  $userHash = str_replace("/", "", $userHash);

  //use db lookup to get list of groups, make checklist for them: which ones you in? (will require joins)
  //note: 5.0 in this case is the number of random groups to return
  $db = getDB();
  try{
    $stmt = $db->prepare("SELECT id, group_name FROM groups ORDER BY RAND() LIMIT 5");
    $stmt->execute();
    $rows = $stmt->fetchAll();
  }
  catch (Exception $e) {
    return false;
  }

  //make checklist form of all groups
  for($i = 0; $i < count($rows); $i++){
    echo('<input type="checkbox" name="authGroupList[]" value="' . $rows[$i]['id'] . '">' . $rows[$i]['group_name'] . '</br>');
  }

  //stash and resubmit the user's userID
  echo('<input type="hidden" name="userHash" value="' . $userHash .'" />');
}
?>

<input name='submit' type="submit" value="login..." />
</form>

<h4>*refresh to attempt again</h4>
