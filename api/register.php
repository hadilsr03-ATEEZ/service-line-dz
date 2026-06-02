<?php
header("Content-Type: application/json");
include "connect.php";

$data = json_decode(file_get_contents("php://input"));

if (
    !$data ||
    !isset(
        $data->fullName,
        $data->email,
        $data->phone,
        $data->password,
        $data->userType
    )
) {
    echo json_encode(["error" => "Missing data"]);
    exit;
}

$fullName = trim($data->fullName);
$email = trim($data->email);
$phone = trim($data->phone);
$password = $data->password;
$userType = $data->userType;

// Check email
$checkEmail = $conn->prepare(
    "SELECT userId FROM users WHERE email = ?"
);
$checkEmail->bind_param("s", $email);
$checkEmail->execute();

if ($checkEmail->get_result()->num_rows > 0) {
    echo json_encode([
        "error" => "Email already exists"
    ]);
    exit;
}

// Check phone
$checkPhone = $conn->prepare(
    "SELECT userId FROM users WHERE phone = ?"
);
$checkPhone->bind_param("s", $phone);
$checkPhone->execute();

if ($checkPhone->get_result()->num_rows > 0) {
    echo json_encode([
        "error" => "Phone already exists"
    ]);
    exit;
}

$passwordHash = password_hash(
    $password,
    PASSWORD_DEFAULT
);

$sql = "
INSERT INTO users
(
    fullName,
    email,
    phone,
    passwordHash,
    status,
    userType
)
VALUES
(
    ?,
    ?,
    ?,
    ?,
    'active',
    ?
)
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "sssss",
    $fullName,
    $email,
    $phone,
    $passwordHash,
    $userType
);

if ($stmt->execute()) {

    echo json_encode([
        "message" => "Registration successful",
        "userId" => $stmt->insert_id
    ]);

} else {

    echo json_encode([
        "error" => $stmt->error
    ]);

}

$stmt->close();
$conn->close();
?>