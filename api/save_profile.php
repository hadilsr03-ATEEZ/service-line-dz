<?php

header("Content-Type: application/json");
include "connect.php";

$data = json_decode(file_get_contents("php://input"));

if (
    !$data ||
    !isset($data->userId, $data->profileId)
) {
    echo json_encode([
        "error" => "Missing data"
    ]);
    exit;
}

$userId = (int)$data->userId;
$profileId = (int)$data->profileId;

/* Check if already saved */

$check = $conn->prepare("
SELECT saveId
FROM saved_profiles
WHERE userId = ?
AND profileId = ?
");

$check->bind_param(
    "ii",
    $userId,
    $profileId
);

$check->execute();

$result = $check->get_result();

/* Already saved -> Unsave */

if ($result->num_rows > 0) {

    $delete = $conn->prepare("
    DELETE FROM saved_profiles
    WHERE userId = ?
    AND profileId = ?
    ");

    $delete->bind_param(
        "ii",
        $userId,
        $profileId
    );

    $delete->execute();

    echo json_encode([
        "saved" => false
    ]);

} else {

    $insert = $conn->prepare("
    INSERT INTO saved_profiles
    (
        userId,
        profileId
    )
    VALUES (?, ?)
    ");

    $insert->bind_param(
        "ii",
        $userId,
        $profileId
    );

    $insert->execute();

    echo json_encode([
        "saved" => true
    ]);
}

$conn->close();