<?php
include "../config/db.php";



$bookingId = intval($_POST['booking_id'] ?? 0);
if ($bookingId <= 0) {
    die("Invalid booking ID");
}

try {
    // Make sure user can only cancel their own booking
    $updateToCancelQuery = "UPDATE CUSTOMER_BOOKING_DETAILS 
            SET STATUS = 'CANCELED' 
            WHERE BOOKING_ID = :bookingId ";
    $stmt = $conn->prepare($updateToCancelQuery);
    $stmt->bindParam(':bookingId', $bookingId, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "success";
    } else {
        echo "Failed to cancel booking or it does not belong to you.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
