<?php

header("Content-Type: application/json");

include "connect.php";

$sql = "SELECT * FROM wilaya ORDER BY name";

$result = mysqli_query($conn, $sql);

$wilayas = [];

while ($row = mysqli_fetch_assoc($result)) {
    $wilayas[] = $row;
}

echo json_encode([
    "success" => true,
    "wilayas" => $wilayas
]);

$conn->close();

?>