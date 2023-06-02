<?php
          header("Content-type: application/json");
          $dir = 'sqlite:rarbg_db.sqlite';
          $dbh  = new PDO($dir) or die("cannot open the database");
          $query =  "SELECT * FROM 'items' WHERE ext_id='".str_replace("'", "\"", $_GET[id])."' LIMIT 0,1";
          $results = $dbh->query($query);
          foreach ($results as $row) {
            echo "{\"hash\":\"$row[1]\", \"name\": \"$row[2]\", \"date\": \"$row[3]\", \"cat\": \"$row[4]\", \"size\": $row[5], \"ext_id\": \"$row[6]\", \"imdb_id\": \"$row[7]\"}";
          }
          $dbh = null;
?>
