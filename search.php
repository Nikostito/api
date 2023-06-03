<?php
    require "config.php";
    header("Content-type: application/json");

    if (!isset($_GET['key'])) {
        die("API key missing.");
    }

    $apiKey = $_GET['key'];

    if (!in_array($apiKey, $api_keys)) {
        die("API key invalid");
    }

    $dir = 'sqlite:rarbg_db.sqlite';
    $dbh = new PDO($dir) or die("cannot open the database");

    $q = $_GET['query'];
    $query = "SELECT * FROM items WHERE cat != 'xxx' AND title LIKE :like ORDER BY dt DESC";
    $stmt = $dbh->prepare($query);
    $stmt->bindValue(':like', "%$q%", PDO::PARAM_STR);
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $total = count($results);

    if ($results) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $results_per_page = 20;
        $offset = ($page - 1) * $results_per_page;
        $limited_results = array_slice($results, $offset, $results_per_page);

        header('Content-type: application/json');
        echo json_encode(array(
            'data' => $limited_results,
            'count' => $total,
            'pages' => ceil($total / $results_per_page),
            'query' => $q
        ));
    } else {
        echo "Error: " . $q . "<br>" . $stmt->errorInfo()[2];
    }

    $dbh = null;
?>
