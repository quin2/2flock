<?php
include 'common.php';
$db = getDB();

$groupSelected = 26;
$numPosts = 5;

try{
  $stmt = $db->prepare("SELECT post_id, post_text, posted, pin FROM posts WHERE group_id = ? ORDER BY pin, posted DESC LIMIT ?");
  $stmt->bindParam(1, $groupSelected);
  $stmt->bindParam(2, $numPosts,PDO::PARAM_INT);
  $stmt->execute();
  $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (Exception $e) {
  return false;
}

print_r($rows)

/*seems to be a prob only when the other variable is added, hmmmmmmmmmmmmmmm*/

 ?>
