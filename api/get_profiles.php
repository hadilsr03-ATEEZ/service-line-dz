<?php

header("Content-Type: application/json");

include "connect.php";

$sql = "
SELECT
    p.profileId,
    p.profilePhoto,
    p.fullName,
    p.wilayaId,
    p.commune,
    p.mainCategoryId,
    p.additionalCategories,
    p.serviceAreas,
    p.emergencyServices,
    p.createdAt,

    c.name AS mainCategoryName,
    w.name AS wilayaName

FROM profiles p

LEFT JOIN category c
    ON p.mainCategoryId = c.categoryId

LEFT JOIN wilaya w
    ON p.wilayaId = w.wilayaId

ORDER BY p.createdAt DESC
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