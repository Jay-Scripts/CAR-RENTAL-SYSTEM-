<?php
include "../config/db.php";

$bookingId = intval($_POST['booking_id'] ?? 0);
if ($bookingId <= 0) {
    die("Invalid booking ID");
}

try {
    $conn->beginTransaction();

    // Cancel the booking
    $updateBooking = "UPDATE CUSTOMER_BOOKING_DETAILS 
                      SET STATUS = 'CANCELED' 
                      WHERE BOOKING_ID = :bookingId";
    $stmt = $conn->prepare($updateBooking);
    $stmt->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $stmt->execute();

    // Update the car status to AVAILABLE
    $updateCar = "UPDATE CAR_DETAILS c
                  INNER JOIN CUSTOMER_BOOKING_DETAILS b ON c.CAR_ID = b.CAR_ID
                  SET c.STATUS = 'AVAILABLE'
                  WHERE b.BOOKING_ID = :bookingId";
    $stmt = $conn->prepare($updateCar);
    $stmt->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $stmt->execute();

    $conn->commit();
    echo "success";
} catch (PDOException $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
