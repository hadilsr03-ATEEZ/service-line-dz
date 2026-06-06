<?php

header("Content-Type: application/json");

include "connect.php";

$profileId = $_GET["id"] ?? null;

if (!$profileId) {

    echo json_encode([
        "error" => "Profile ID missing"
    ]);

    exit;
}

/* =========================
   Get Profile
========================= */

$sql = "
SELECT *
FROM profiles
WHERE profileId = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "i",
    $profileId
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "error" => "Profile not found"
    ]);

    exit;
}

$profile =
    $result->fetch_assoc();

/* =========================
   Get Services
========================= */

$servicesSql = "
SELECT *
FROM services
WHERE profileId = ?
";

$servicesStmt =
    $conn->prepare(
        $servicesSql
    );

$servicesStmt->bind_param(
    "i",
    $profileId
);

$servicesStmt->execute();

$servicesResult =
    $servicesStmt->get_result();

$services = [];

while (
    $row =
    $servicesResult->fetch_assoc()
) {

    $services[] = $row;
}

/* =========================
   Response
========================= */

echo json_encode([
    "success" => true,
    "profile" => $profile,
    "services" => $services
]);

$conn->close();

?>