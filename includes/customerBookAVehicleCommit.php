<?php
include "../../config/db.php";
session_start();

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Not logged in.");
    }

    $car_id       = $_POST['car_id'] ?? '';
    $pickup_date  = $_POST['pickup_date'] ?? '';
    $dropoff_date = $_POST['dropoff_date'] ?? '';
    $trip_details = $_POST['trip_details'] ?? '';

    $conn->beginTransaction();

    // Calculate total cost again (to prevent tampering)
    $stmt = $conn->prepare("SELECT PRICE FROM CAR_DETAILS WHERE CAR_ID = :car_id LIMIT 1");
    $stmt->execute([':car_id' => $car_id]);
    $carRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$carRow) throw new Exception("Car not found.");

    $pickup  = new DateTime($pickup_date);
    $dropoff = new DateTime($dropoff_date);
    $interval = $pickup->diff($dropoff);
    $total_days = $interval->days ?: 1;
    $total_cost = $total_days * (float)$carRow['PRICE'];

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO CUSTOMER_BOOKING_DETAILS 
        (USER_ID, CAR_ID, PICKUP_DATE, DROP_OFF_DATE, TRIP_DETAILS, STATUS, TOTAL_COST)
        VALUES (:user_id, :car_id, :pickup_date, :dropoff_date, :trip_details, 'PENDING', :total_cost)");
    $stmt->execute([
        ':user_id'      => $_SESSION['user_id'],
        ':car_id'       => $car_id,
        ':pickup_date'  => $pickup_date,
        ':dropoff_date' => $dropoff_date,
        ':trip_details' => $trip_details,
        ':total_cost'   => $total_cost
    ]);

    // Update car status
    $stmt = $conn->prepare("UPDATE CAR_DETAILS SET STATUS = 'RESERVED' WHERE CAR_ID = :car_id");
    $stmt->execute([':car_id' => $car_id]);

    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
<?php
include "../../config/db.php";
session_start();

try {
    if (!isset($_SESSION['user_id'])) {
        throw new Exception("Not logged in.");
    }

    $car_id       = $_POST['car_id'] ?? '';
    $pickup_date  = $_POST['pickup_date'] ?? '';
    $dropoff_date = $_POST['dropoff_date'] ?? '';
    $trip_details = $_POST['trip_details'] ?? '';

    $conn->beginTransaction();

    // Calculate total cost again (to prevent tampering)
    $stmt = $conn->prepare("SELECT PRICE FROM CAR_DETAILS WHERE CAR_ID = :car_id LIMIT 1");
    $stmt->execute([':car_id' => $car_id]);
    $carRow = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$carRow) throw new Exception("Car not found.");

    $pickup  = new DateTime($pickup_date);
    $dropoff = new DateTime($dropoff_date);
    $interval = $pickup->diff($dropoff);
    $total_days = $interval->days ?: 1;
    $total_cost = $total_days * (float)$carRow['PRICE'];

    // Insert booking
    $stmt = $conn->prepare("INSERT INTO CUSTOMER_BOOKING_DETAILS 
        (USER_ID, CAR_ID, PICKUP_DATE, DROP_OFF_DATE, TRIP_DETAILS, STATUS, TOTAL_COST)
        VALUES (:user_id, :car_id, :pickup_date, :dropoff_date, :trip_details, 'PENDING', :total_cost)");
    $stmt->execute([
        ':user_id'      => $_SESSION['user_id'],
        ':car_id'       => $car_id,
        ':pickup_date'  => $pickup_date,
        ':dropoff_date' => $dropoff_date,
        ':trip_details' => $trip_details,
        ':total_cost'   => $total_cost
    ]);

    // Update car status
    $stmt = $conn->prepare("UPDATE CAR_DETAILS SET STATUS = 'RESERVED' WHERE CAR_ID = :car_id");
    $stmt->execute([':car_id' => $car_id]);

    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollBack();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
