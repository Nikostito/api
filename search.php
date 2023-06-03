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
    $keywords = explode(' ', $q);
    $conditions = array();
    $params = array();

    foreach ($keywords as $index => $keyword) {
        $paramName = ":keyword$index";
        $conditions[] = "title LIKE $paramName";
        $params[$paramName] = "%$keyword%";
    }

    $query = "SELECT COUNT(*) FROM items WHERE cat != 'xxx' AND (" . implode(' AND ', $conditions) . ")";
    $stmt = $dbh->prepare($query);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();

    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $results_per_page = 20;
    $offset = ($page - 1) * $results_per_page;

    $query = "SELECT * FROM items WHERE cat != 'xxx' AND (" . implode(' AND ', $conditions) . ") ORDER BY dt DESC LIMIT :offset, :limit";
    $stmt = $dbh->prepare($query);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $results_per_page, PDO::PARAM_INT);
    foreach ($params as $paramName => $paramValue) {
        $stmt->bindValue($paramName, $paramValue);
    }
    $stmt->execute();

    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $num_results = count($results);

    if ($results) {
        $total_pages = ceil($total / $results_per_page);
        header('Content-type: application/json');
        echo json_encode(array(
            'data' => $results,
            'count' => $total,
            'pages' => $total_pages,
            'query' => $q
        ));
    } else {
        echo "Error: " . $q . "<br>" . $stmt->errorInfo()[2];
    }

    $dbh = null;
?>
