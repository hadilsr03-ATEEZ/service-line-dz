<?php

header("Content-Type: application/json");

require_once "connect.php";

$sql = "SELECT * FROM category ORDER BY name";

$result = mysqli_query($conn, $sql);

$categories = [];

while ($row = mysqli_fetch_assoc($result)) {
    $categories[] = $row;
}

echo json_encode([
    "success" => true,
    "categories" => $categories
]);