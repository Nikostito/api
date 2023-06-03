<?php
          require "config.php";
          header("Content-type: application/json");
          if (!isset($_GET['key'])) {
             die("API key missing."); 
          }
          if (!in_array($_GET['key'], $api_keys)) {
             die("API key invalid");   
          }
          header("Content-type: application/json");
          $dir = 'sqlite:rarbg_db.sqlite';
          $dbh  = new PDO($dir) or die("cannot open the database");
          $stmt = $dbh->prepare("SELECT * FROM 'items' WHERE ext_id=:id LIMIT 0,1");
          $stmt->execute([ 'id' => $_GET['id'] ]);
          foreach ($stmt as $row) {
            echo "{\"hash\":\"$row[1]\", \"name\": \"$row[2]\", \"date\": \"$row[3]\", \"cat\": \"$row[4]\", \"size\": $row[5], \"ext_id\": \"$row[6]\", \"imdb_id\": \"$row[7]\"}";
          }
          $dbh = null;
?>
