<?php
  function getCode($group, $minutes){
    $thisGroup = $group;
    $expire = $minutes;
    //generate random code with length of 6
    $newCode = randomCode(6);

    //check to see if code exsists
    $db = getDB();
    try{
      $stmt = $db->prepare("SELECT * FROM codes WHERE join_code=:newCode");
      $stmt->execute(array(':newCode' => $newCode));
      $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      return false;
    }

    if(count($all) > 0){
      getCode($thisGroup, $expire);
    }

    //post new key to database 
    try{
      $stmt = $db->prepare("INSERT INTO codes (group_id, join_code, expire) VALUES (:thisGroup, :newCode, :expire)");
      $stmt->execute(array(':thisGroup' => $thisGroup, ':newCode' => $newCode, ':expire' => $expire));
    }
    catch (Exception $e) {
      return false;
    }

    //return new key!!!
    return $newCode;
  }

  //returns random $length code with letters and numbers
  function randomCode($length){
    $useChars = array_merge(range('A', 'Z'), range(0, 9));
    $finalString = "";

    for($i = 0; $i < $length; $i++){
      $pick = random_int(0, count($useChars) - 1);
      $finalString .= $useChars[$pick];
    }
    return $finalString;
  }

 ?>
