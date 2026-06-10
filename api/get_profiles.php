<?php

header("Content-Type: application/json");

include "connect.php";

$sql = "
SELECT
    profileId,
    profilePhoto,
    fullName,
    wilaya,
    mainCategory,
    additionalCategories,
    serviceAreas,
    emergencyServices,
    createdAt
FROM profiles
ORDER BY createdAt DESC
";

$result = $conn->query($sql);

$profiles = [];

while ($row = $result->fetch_assoc()) {
    $profiles[] = $row;
}

echo json_encode([
    "success" => true,
    "profiles" => $profiles
]);

$conn->close();

?>