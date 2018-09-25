<?php
include_once 'common.php';
include_once 'validCode.php';

//function to join, returns false if shit doesnt work!!!!
function joinGroup($groupCode, $newUserId){
  //convert to uppercase...
  $groupCode = strtoupper($groupCode);

  //check to see if key is expired :/
  if(!validCode($groupCode)){
    return false;
  }

  //get group number
  $db = getDB();
  try{
    $stmt = $db->prepare("SELECT group_id FROM codes WHERE join_code=:groupCode");
    $stmt->execute(array(':groupCode' => $groupCode));
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
  }
  catch (Exception $e) {
    return false;
  }
  $group = $rows[0]['group_id'];

  //store unique id in database along with connected group (as number, not as join code)
  try{
    $stmt = $db->prepare("INSERT INTO users (user_id, group_joined) VALUES (:newUserId, :group)");
    $stmt->execute(array(':newUserId' => $newUserId, 'group' => $group));
  }
  catch (Exception $e) {
    return false;
  }

  return true;
}

 ?>
