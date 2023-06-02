<?php
          header("Content-type: application/json");

          $dir = 'sqlite:rarbg_db.sqlite';
          $dbh  = new PDO($dir) or die("cannot open the database");

          $query =  $dbh->prepare("SELECT * FROM 'items' WHERE cat != 'xxx' AND title LIKE '%".str_replace("'", "\"", $_GET['query'])."%' ORDER BY dt DESC");
          $query->execute();
          $results = $query->fetchAll(PDO::FETCH_ASSOC);
          $total = count($results);

          $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
          $results_per_page = 20;
          $offset = ($page - 1) * $results_per_page;
          $limited_results = array_slice($results, $offset, $results_per_page);

          header('Content-type: application/json');
          echo json_encode(array('results' => $limited_results, 'count' => $total,'pages' => ceil($total/$results_per_page)));

          $dbh = null;

?> 
