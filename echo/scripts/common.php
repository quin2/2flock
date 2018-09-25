<?php
function getDB(){
  $servername = "localhost";
  $username = "accManager";
  $password = "W4i6xJZpVUf183dY";
  $database = "espace";
  $dbport = 8889;

  try {
  $db = new PDO("mysql:host=$servername;dbname=$database;charset=utf8;port=$dbport", $username, $password);
  }
  catch(PDOException $e) {
  echo $e->getMessage();
  }
  return $db;
}

/*
made with <3 in:
loveland, co
denver, co
fort collens, co
seattle, wa
bainbridge island, wa
anchorage, ak
fairbanks, ak
*/
?>
