<?php
header("Content-Type: application/json");
include "connect.php";

$data = json_decode(file_get_contents("php://input"));

if (
    !$data ||
    !isset($data->emailOrPhone, $data->password)
) {
    echo json_encode([
        "error" => "Missing data"
    ]);
    exit;
}

$emailOrPhone = trim($data->emailOrPhone);
$password = $data->password;

$sql = "
SELECT
    userId,
    fullName,
    email,
    phone,
    passwordHash,
    userType
FROM users
WHERE email = ?
OR phone = ?
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "ss",
    $emailOrPhone,
    $emailOrPhone
);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {

    echo json_encode([
        "error" => "Invalid credentials"
    ]);

    exit;
}

$user = $result->fetch_assoc();

if (
    !password_verify(
        $password,
        $user["passwordHash"]
    )
) {

    echo json_encode([
        "error" => "Invalid credentials"
    ]);

    exit;
}

echo json_encode([
    "success" => true,
    "userId" => $user["userId"],
    "fullName" => $user["fullName"],
    "userType" => $user["userType"]
]);

$stmt->close();
$conn->close();
?>