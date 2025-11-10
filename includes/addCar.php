<?php
include "../config/db.php";

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carName   = $_POST['car_name'] ?? '';
    $color     = $_POST['color'] ?? '';
    $capacity  = intval($_POST['capacity'] ?? 0);
    $price     = floatval($_POST['price'] ?? 0);
    $plateNum  = $_POST['plate_num'] ?? '';

    if (!isset($_FILES['thumbnail'])) {
        $response['message'] = 'Car thumbnail is required.';
        echo json_encode($response);
        exit;
    }

    $fileTmp  = $_FILES['thumbnail']['tmp_name'];
    $fileName = basename($_FILES['thumbnail']['name']);
    $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileExt, $allowedExt)) {
        $response['message'] = 'Invalid file type. Only JPG, JPEG, PNG, GIF allowed.';
        echo json_encode($response);
        exit;
    }

    $newFileName = "car_" . time() . "." . $fileExt;
    $uploadDir   = __DIR__ . "/../src/images/carsImage/";
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

    $uploadPath = $uploadDir . $newFileName;

    if (move_uploaded_file($fileTmp, $uploadPath)) {
        $thumbnailPath = "../../src/images/carsImage/" . $newFileName;

        $stmt = $conn->prepare("INSERT INTO CAR_DETAILS (CAR_NAME, COLOR, CAPACITY, PRICE, PLATE_NUM, THUMBNAIL_PATH, STATUS, CREATED_AT) VALUES (?, ?, ?, ?, ?, ?, 'AVAILABLE', NOW())");
        $stmt->execute([$carName, $color, $capacity, $price, $plateNum, $thumbnailPath]);

        $response['success'] = true;
        $response['message'] = 'Car has been added successfully!';
    } else {
        $response['message'] = 'Failed to upload car image.';
    }

    echo json_encode($response);
}
