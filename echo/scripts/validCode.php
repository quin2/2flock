<?php
  //validates code and 'punches' limited ticket!!!
  function validCode($toCheck){
    //get the time created and expire time of key in question
    $db = getDB();
    try{
      $stmt = $db->prepare("SELECT expire FROM codes WHERE join_code=:toCheck");
      $stmt->execute(array(':toCheck' => $toCheck));
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      return false;
    }

    //does the key even exsist????
    if(count($rows) == 0){
      return false;
    }

    //delete key if it's already used up
    if($rows[0]['expire'] <= 0){
      deleteCode($toCheck);
      return false;
    }

    //reinsert into database
    try{
      $stmt = $db->prepare("UPDATE codes SET expire = expire - 1 WHERE join_code=:toCheck");
      $stmt->execute(array(':toCheck' => $toCheck));
    }
    catch (Exception $e) {
      return false;
    }

    return true;
  }

  //deletes code from system with given join code
  function deleteCode($toDelete){
    $db = getDB();
    try{
      $stmt = $db->prepare("DELETE FROM codes WHERE join_code=:toDelete");
      $resp = $stmt->execute(array(':toDelete' => $toDelete));
    }
    catch (Exception $e) {
      return false;
    }
  }

 ?>
