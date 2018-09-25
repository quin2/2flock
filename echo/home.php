<?php session_start();
  //if session is not set, go back home!!!
  if(!isset($_SESSION['auth'])){
    header("Location: http://localhost:8888/echo/index.php", true, 301);
    die();
 }
?>

<!DOCTYPE HTML>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>echospace</title>
</head>

<body>

  <h1>echo_</h1>

    <!--LOGIC TO SHOW USER's GROUP LIST + FORM FOR SELECTING GROUP-->
    <form action="home.php" method="post">
    <fieldset>
    <legend>select group</legend>
    <select name="groupList">

    <?php
    include_once 'scripts/common.php';
    //fetch all group names the user is a part of, along with id #s
    $userID = $_SESSION['user'];

    $db = getDB();
    try{
      $stmt = $db->prepare("SELECT id, group_name FROM groups g JOIN users u ON g.id = u.group_joined WHERE u.user_id =:userID ORDER BY g.group_name DESC");
      $stmt->execute(array(':userID' => $userID));
      $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    catch (Exception $e) {
      return false;
    }

    //mark first group as default!
    $groupSelected = $rows[0]['id'];
    //if group name is already set, go with that
    if(isset($_SESSION['currentGroup'])){
      $groupSelected = $groupSelected = $_SESSION['currentGroup'];
    }


    //make dropdown where user can select the group they want
    for($i = 0; $i < count($rows); $i++){
      echo('<option value="' . $rows[$i]['id'] . '">' . $rows[$i]['group_name'] .'</option>');
    }
    ?>

    </select>
    <input type="submit" name="groupSelect" value="go" />
    </fieldset>
    </form>

    <!--LOGIC TO update groupSelected variable-->
    <?php
      if(isset($_POST['groupSelect'])){
        $_SESSION['currentGroup'] = $_POST['groupList'];
        $groupSelected = $_SESSION['currentGroup'];
      }
    ?>

    <!--PHP TO show group name-->
    <?php
      echo('<h2>' . $groupName . '</h2>');
    ?>

    <!--FORM FOR POSTING, only shows when group select is triggered-->
    <form action="home.php" method="post">
      <fieldset>
        <legend>new post</legend>
        <textarea name="postText" rows="5" cols="30" wrap="hard" maxlength="750" placeholder="be nice..."></textarea>
        <input type="submit" name="postButton" value="post" />
      </fieldset>
    </form>

    <!--PHP BLOCK FOR SUBMITTING POSTS-->
    <?php
      if(isset($_POST['postButton'])){
        //get variable and clear stuff
        $postTextToUpload = htmlentities($_POST['postText'], ENT_QUOTES);

        //see if it's over limits
        if(strlen($postTextToUpload) > 750){
          echo('post is too long :(');
        }

        //otherwise stash it into the db
        else{
          try{
            $stmt = $db->prepare("INSERT INTO posts (group_id, post_text) VALUES (:groupSelected, :postTextToUpload)");
            $stmt->execute(array(':groupSelected' => $groupSelected, ':postTextToUpload' => $postTextToUpload));
          }
          catch (Exception $e) {
            return false;
          }
        }
      }
     ?>

  <!--PHP BLOCK FOR LOADING POSTS-->
  <?php
    //check to see if post advance button was pressed
    $numPosts = 20;
    if(isset($_GET['loadPosts'])){
      $numPosts = htmlentities($_GET['loadPosts'], ENT_QUOTES);
    }

    //db call to get all posts, call isn't fucking working (somethign new!!)
    $db = getDB();
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

    //for each value in db
    for($i = 0; $i < count($rows); $i++){
      $postText = $rows[$i]['post_text'];
      $currentDate = $rows[$i]['posted'];

      echo('
      <table>
      <tr>
      <th>' . $currentDate . '</th>
      </tr>
      <tr>
      <td>' . $postText . '</td>
      </tr>
      <tr>
      <td>pin report</td>
      </tr>
      </table>
      ');
    }

    //link to load more posts!
    $morePosts = $numPosts + 10;
    echo('<a href="http://localhost:8888/echo/home.php?loadPosts=' . $morePosts . '">load more posts</a>');
  ?>


  <!--FORM TO JOIN GROUP-->
  <form action="home.php" method="post">
    <fieldset>
      <legend>join new group...</legend>
      <input type="text" name="toJoin" placeholder="group code"/>
      <input type="submit" name="joinButton" value="join!" />
    </fieldset>
  </form>

  <!--PHP BLOCK FOR GROUP JOINING-->
  <?php
  include_once 'scripts/joinGroup.php';

  if(isset($_POST['joinButton'])){
    $user = $_SESSION['user'];
    $group = htmlentities($_POST['toJoin'], ENT_QUOTES);
    joinGroup($group, $user);
  }
  ?>

  <!--FORM FOR CODE GENERATION, uses show logic on group select-->
  <form action="home.php" method="post">
    <fieldset>
      <legend>share group code</legend>
      <input type="text" name="numToAdd" id="numToAdd" placeholder="number of uses"/>
      <input type="submit" name="genButton" value="generate code" />
    </fieldset>
  </form>

  <!--PHP BLOCK FOR CODE GENERATION-->
  <?php
  include_once 'scripts/generateJoinCode.php';

  if(isset($_POST['genButton'])){
    $numToGen = htmlentities($_POST['numToAdd'], ENT_QUOTES);

    if($numToGen <= 0){
      echo('code invalid!');
      exit();
    }

    else{
      //send to class, get stuff
      $joinCode = getCode($groupSelected, $numToGen);
      echo('new code is: ' . $joinCode . ' share it with your friends!');
    }
  }
  ?>

  <!--FORM FOR LOGGING OUT-->
  <form action="logout.php">
      <input type="submit" value="logout" />
  </form>

</body>

</html>
