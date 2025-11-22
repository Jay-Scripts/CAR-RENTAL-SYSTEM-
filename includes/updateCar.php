<?php
header('Content-Type: application/json');
include "../config/db.php";

try {
    if (!isset($_POST['car_id']) || empty($_POST['car_id'])) {
        throw new Exception("Car ID is missing");
    }

    $id = $_POST['car_id'];
    $name = $_POST['car_name'];
    $color = $_POST['color'];
    $capacity = $_POST['capacity'];
    $price = $_POST['price'];
    $plate = $_POST['plate_num'];

    // Start SQL
    if (!empty($_FILES['thumbnail']['name'])) {

        $fileName = time() . "_" . basename($_FILES['thumbnail']['name']);
        $target = "../uploads/" . $fileName;

        if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $target)) {
            throw new Exception("Failed to upload image.");
        }

        $sql = "UPDATE CAR_DETAILS 
                SET CAR_NAME=?, COLOR=?, CAPACITY=?, PRICE=?, PLATE_NUM=?, THUMBNAIL_PATH=?
                WHERE CAR_ID=?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $color, $capacity, $price, $plate, $target, $id]);
    } else {

        $sql = "UPDATE CAR_DETAILS 
                SET CAR_NAME=?, COLOR=?, CAPACITY=?, PRICE=?, PLATE_NUM=?
                WHERE CAR_ID=?";

        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $color, $capacity, $price, $plate, $id]);
    }

    echo json_encode(["success" => true, "message" => "Car updated successfully"]);
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
