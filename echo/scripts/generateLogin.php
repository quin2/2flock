<?php
  include_once 'scripts/common.php';

  function uniqueLogin($hash){

    //generate new hash
    $newCode = md5(uniqid($hash, true));

    //connect to database to get list of hashes
    $db = getDB();
    try{
      $stmt = $db->prepare("SELECT * FROM users WHERE user_id=:newCode");
      $stmt->execute(array(':newCode' => $newCode));
      $all = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      return false;
    }

    //check if hash has already been used
    if(count($all) > 0){
      uniqueLogin($hash);
    }

    return $newCode;
  }

?>
