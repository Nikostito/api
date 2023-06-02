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
        $q = $_GET['query'];
        //$query =  $dbh->prepare("SELECT * FROM 'items' WHERE cat != 'xxx' AND title LIKE :like ORDER BY dt DESC");
        $query = "SELECT * FROM 'items' WHERE cat != 'xxx' AND title LIKE ";
        if (strpos($q, ' ') !== false) {
            $parts = explode(' ', $q);
            $like = '';
            foreach ($parts as $word) {
                $query.=" '%$word%' OR ";
                }
            $query = rtrim($query, " OR");
            $query.= 'ORDER BY dt DESC';
            } 
        else {
        $query.= "'%". $q . "%'".' ORDER BY dt DESC';
        } 
        $data= $dbh->query($query);
        $results = $data->fetchAll(PDO::FETCH_ASSOC);
        $total = count($results);
        if ($results) {
            $total = count($results);
        
            $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
            $results_per_page = 20;
            $offset = ($page - 1) * $results_per_page;
            $limited_results = array_slice($results, $offset, $results_per_page);

            header('Content-type: application/json');
            echo json_encode(array('data' => $limited_results, 'count' => $total,'pages' => ceil($total/$results_per_page),'query'=>$q));
        }
        else {
            echo "Error: " . $q . "<br>" . $dbh->errorInfo()[2];
        }
        $dbh = null;

?> 
