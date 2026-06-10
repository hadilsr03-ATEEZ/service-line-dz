<?php

header("Content-Type: application/json");

include "connect.php";


if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    echo json_encode([
        "error" => "Invalid request"
    ]);
    exit;
}

/* =========================
   Required Data
========================= */

$profileId = $_POST["profileId"] ?? null;


$getProfile = $conn->prepare(
    "SELECT profilePhoto,
            coverPhoto,
            portfolioPhotos,
            qualificationFiles
     FROM profiles
     WHERE profileId = ?"
);

$getProfile->bind_param(
    "i",
    $profileId
);

$getProfile->execute();

$oldProfile =
    $getProfile
    ->get_result()
    ->fetch_assoc();

if (!$profileId) {
    echo json_encode([
        "error" => "Profile ID missing"
    ]);
    exit;
}

$fullName = trim($_POST["fullName"] ?? "");
$aboutMe = trim($_POST["aboutMe"] ?? "");
$phone = trim($_POST["phone"] ?? "");

if (
    empty($fullName) ||
    empty($aboutMe) ||
    empty($phone)
) {
    echo json_encode([
        "error" => "Missing required fields"
    ]);
    exit;
}

/* =========================
   Optional Data
========================= */

$whatsapp = trim($_POST["whatsapp"] ?? "");
$instagram = trim($_POST["instagram"] ?? "");
$facebook = trim($_POST["facebook"] ?? "");
$tiktok = trim($_POST["tiktok"] ?? "");

$wilaya = trim($_POST["wilaya"] ?? "");
$commune = trim($_POST["commune"] ?? "");
$experience = trim($_POST["experience"] ?? "");
$qualifications = trim($_POST["qualifications"] ?? "");

$mainCategory = $_POST["mainCategory"] ?? "";
$additionalCategories = $_POST["additionalCategories"] ?? "[]";

$days = $_POST["days"] ?? [];

$availabilityData = [];

foreach ($days as $day) {

    $availabilityData[$day] = [

        "start" =>
            $_POST[$day . "_start"]
            ?? "",

        "end" =>
            $_POST[$day . "_end"]
            ?? ""

    ];
}

$availability =
    json_encode(
        $availabilityData
    );

$serviceAreas = $_POST["serviceAreas"] ?? "[]";

$emergencyServices =
    isset($_POST["emergencyServices"])
    ? 1
    : 0;

/* =========================
   Upload Folder
========================= */

$uploadDir = "../uploads/";

if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

/* =========================
   Profile Photo
========================= */

$profilePhoto =
    $oldProfile["profilePhoto"];

if (
    isset($_FILES["profilePhoto"]) &&
    $_FILES["profilePhoto"]["error"] === 0
) {

    $fileName =
        uniqid() .
        "_profile_" .
        basename($_FILES["profilePhoto"]["name"]);

    $target = $uploadDir . $fileName;

    if (
        move_uploaded_file(
            $_FILES["profilePhoto"]["tmp_name"],
            $target
        )
    ) {
        $profilePhoto = "uploads/" . $fileName;
    }
}

/* =========================
   Cover Photo
========================= */

$coverPhoto =
    $oldProfile["coverPhoto"];

if (
    isset($_FILES["coverPhoto"]) &&
    $_FILES["coverPhoto"]["error"] === 0
) {

    $fileName =
        uniqid() .
        "_cover_" .
        basename($_FILES["coverPhoto"]["name"]);

    $target = $uploadDir . $fileName;

    if (
        move_uploaded_file(
            $_FILES["coverPhoto"]["tmp_name"],
            $target
        )
    ) {
        $coverPhoto = "uploads/" . $fileName;
    }
}

/* =========================
   Portfolio Photos
========================= */

$portfolioPaths =
    json_decode(
        $oldProfile["portfolioPhotos"]
        ?? "[]",
        true
    );
$qualificationPaths =
    json_decode(
        $oldProfile["qualificationFiles"]
        ?? "[]",
        true
    );

if (isset($_FILES["portfolioPhotos"])) {

    foreach (
        $_FILES["portfolioPhotos"]["tmp_name"]
        as $key => $tmpName
    ) {

        if (
            $_FILES["portfolioPhotos"]["error"][$key] === 0
        ) {

            $fileName =
                uniqid() .
                "_portfolio_" .
                basename(
                    $_FILES["portfolioPhotos"]["name"][$key]
                );

            $target =
                $uploadDir . $fileName;

            if (
                move_uploaded_file(
                    $tmpName,
                    $target
                )
            ) {

                $portfolioPaths[] =
                    "uploads/" . $fileName;
            }
        }
    }
}




/* =========================
   Qualification Files
========================= */

if (isset($_FILES["qualificationFiles"])) {

    foreach (
        $_FILES["qualificationFiles"]["tmp_name"]
        as $key => $tmpName
    ) {

        if (
            $_FILES["qualificationFiles"]["error"][$key] === 0
        ) {

            $fileName =
                uniqid() .
                "_qualification_" .
                basename(
                    $_FILES["qualificationFiles"]["name"][$key]
                );

            $target =
                $uploadDir . $fileName;

            if (
                move_uploaded_file(
                    $tmpName,
                    $target
                )
            ) {

                $qualificationPaths[] =
                    "uploads/" . $fileName;
            }
        }
    }
}

$portfolioPhotos =
    json_encode($portfolioPaths);

$qualificationFiles =
    json_encode($qualificationPaths);

/* =========================
   Update Profile
========================= */

$sql = "
UPDATE profiles
SET
    profilePhoto = ?,
    coverPhoto = ?,
    fullName = ?,
    aboutMe = ?,
    phone = ?,
    whatsapp = ?,
    instagram = ?,
    facebook = ?,
    tiktok = ?,
    wilayaId = ?,
    commune = ?,
    experience = ?,
    qualifications = ?,
    mainCategoryId = ?,
    additionalCategories = ?,
    portfolioPhotos = ?,
    qualificationFiles = ?,
    availability = ?,
    emergencyServices = ?,
    serviceAreas = ?
WHERE profileId = ?
";

$stmt = $conn->prepare($sql);

$stmt->bind_param(
    "ssssssssssssssssssisi",
    $profilePhoto,
    $coverPhoto,
    $fullName,
    $aboutMe,
    $phone,
    $whatsapp,
    $instagram,
    $facebook,
    $tiktok,
    $wilaya,
    $commune,
    $experience,
    $qualifications,
    $mainCategory,
    $additionalCategories,
    $portfolioPhotos,
    $qualificationFiles,
    $availability,
    $emergencyServices,
    $serviceAreas,
    $profileId
);

if (!$stmt->execute()) {

    echo json_encode([
        "error" => $stmt->error
    ]);

    exit;
}

echo json_encode([
    "success" => true,
    "profileId" => $profileId
]);

$conn->close();
exit;


foreach ($days as $day) {

    $startTime =
        $_POST[$day . "_start"]
        ?? null;

    $endTime =
        $_POST[$day . "_end"]
        ?? null;

    $availabilitySql = "
    INSERT INTO availability
    (
        providerId,
        dayOfWeek,
        startTime,
        endTime,
        isActive
    )
    VALUES
    (
        ?, ?, ?, ?, 1
    )
    ";

    $availabilityStmt =
        $conn->prepare(
            $availabilitySql
        );

    $availabilityStmt->bind_param(
        "isss",
        $userId,
        $day,
        $startTime,
        $endTime
    );

    $availabilityStmt->execute();
}

/* =========================
   Insert Services
========================= */

if (
    isset($_POST["serviceName"]) &&
    isset($_POST["serviceDescription"]) &&
    isset($_POST["servicePrice"])
) {

    $serviceNames =
        $_POST["serviceName"];

    $serviceDescriptions =
        $_POST["serviceDescription"];

    $servicePrices =
        $_POST["servicePrice"];

    for (
        $i = 0;
        $i < count($serviceNames);
        $i++
    ) {

        if (
            empty($serviceNames[$i]) ||
            empty($serviceDescriptions[$i])
        ) {
            continue;
        }

        $serviceSql = "
        INSERT INTO services
        (
            profileId,
            serviceName,
            serviceDescription,
            startingPrice
        )
        VALUES
        (
            ?, ?, ?, ?
        )
        ";

        $serviceStmt =
            $conn->prepare($serviceSql);

        $price =
            floatval($servicePrices[$i]);

        $serviceStmt->bind_param(
            "issd",
            $profileId,
            $serviceNames[$i],
            $serviceDescriptions[$i],
            $price
        );

        $serviceStmt->execute();
    }
}

/* =========================
   Success
========================= */

echo json_encode([
    "success" => true,
    "profileId" => $profileId
]);

$conn->close();

?>