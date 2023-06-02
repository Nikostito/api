<?php
          require "config.php";
          header("Content-type: application/json");
          if (isset($_GET['key'])) {
             die("API key missing."); 
          }
          if (!in_array($_GET['key'], $api_keys)) {
             die("API key invalid");   
          }
          echo "{\"data\":[";
          $dir = 'sqlite:rarbg_db.sqlite';
          $dbh  = new PDO($dir) or die("cannot open the database");
          $query =  "SELECT * FROM 'items' WHERE cat!='xxx' ORDER BY date(dt) DESC LIMIT 0,20";
          $i = 0;
          $results = $dbh->query($query);
          foreach ($results as $row) {
            echo "{\"id\":\"$row[0]\", \"hash\":\"$row[1]\", \"name\": \"$row[2]\", \"date\": \"$row[3]\", \"cat\": \"$row[4]\", \"size\": $row[5], \"ext_id\": \"$row[6]\", \"imdb_id\": \"$row[7]\"}";
            if ($i != 19) {
              echo ',';
            }
            $i++;
          }
          $dbh = null;
          echo "]}";
?>
