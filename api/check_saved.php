<?php

header("Content-Type: application/json");
include "connect.php";

$userId =
    $_GET["userId"] ?? null;

$profileId =
    $_GET["profileId"] ?? null;

if (!$userId || !$profileId) {

    echo json_encode([
        "saved" => false
    ]);

    exit;
}

$stmt = $conn->prepare("
SELECT saveId
FROM saved_profiles
WHERE userId = ?
AND profileId = ?
LIMIT 1
");

$stmt->bind_param(
    "ii",
    $userId,
    $profileId
);

$stmt->execute();

$result =
    $stmt->get_result();

echo json_encode([
    "saved" =>
        $result->num_rows > 0
]);

$stmt->close();
$conn->close();