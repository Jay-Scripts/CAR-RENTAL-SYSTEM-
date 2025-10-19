<?php
session_start();
include "../config/db.php";

if (!isset($_GET['booking_id'])) {
    die("Booking ID not specified.");
}

$booking_id = $_GET['booking_id'];

$sql = "
SELECT 
    cbd.BOOKING_ID,
    cbd.PICKUP_DATE,
    cbd.DROP_OFF_DATE,
    cbd.STATUS,
    cbd.CREATED_AT,
    c.CAR_NAME,
    c.PRICE,
    CONCAT(u.FIRST_NAME, ' ', u.LAST_NAME) AS FULL_NAME,
    u.PHONE,
    u.EMAIL,
    u.ADDRESS
FROM CUSTOMER_BOOKING_DETAILS cbd
JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
WHERE cbd.BOOKING_ID = :booking_id
";

$stmt = $conn->prepare($sql);
$stmt->execute([':booking_id' => $booking_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    die("Booking not found.");
}

$total_days = (strtotime($booking['DROP_OFF_DATE']) - strtotime($booking['PICKUP_DATE'])) / 86400 + 1;
$subtotal = $total_days * $booking['PRICE'];
$deposit = $subtotal * 0.5;
$total = $subtotal + $deposit;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice Preview</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>


</head>

<body class="bg-gray-100 p-6 ">
    <div class="invoice-container max-w-3xl mx-auto bg-white p-8 border border-gray-200">
        <!-- Heading -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-serif tracking-widest uppercase">Invoice</h1>
            <div class="text-right text-gray-700">
                <p class="font-semibold">Invoice No: CR-<?php echo $booking['BOOKING_ID']; ?></p>
                <p>Date: <?php echo date("F d, Y"); ?></p>
                <p>Due Date: <?php echo date("F d, Y", strtotime("+7 days")); ?></p>
            </div>
        </div>
        <hr class="border-gray-300 mb-6">

        <!-- Renter & Company Details -->
        <div class="flex justify-between mb-6 text-sm">
            <div class="w-1/2 pr-4">
                <p class="uppercase font-semibold mb-2">Renter Details:</p>
                <p><?php echo $booking['FULL_NAME']; ?></p>
                <p><?php echo $booking['PHONE']; ?></p>
                <p><?php echo $booking['EMAIL']; ?></p>
                <p><?php echo $booking['ADDRESS']; ?></p>
            </div>
            <div class="w-1/2 pl-4">
                <p class="uppercase font-semibold mb-2">Rental Company Details:</p>
                <p>CarRental</p>
                <p>Bldg. 4 Urban Deca Homes Velasquez St. Tondo, Manila</p>
                <p>+63-915-5364-4698</p>
                <p>CarRental@gmail.com</p>
            </div>
        </div>

        <!-- Rental Details Table -->
        <div class="mb-6 text-sm">
            <p class="uppercase font-semibold mb-2">Rental Details:</p>
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr>
                        <th class="pb-1">Vehicle</th>
                        <th class="pb-1">Rental Period</th>
                        <th class="pb-1">Daily Rate</th>
                        <th class="pb-1">Total Days</th>
                        <th class="pb-1">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="pt-1"><?php echo $booking['CAR_NAME']; ?></td>
                        <td class="pt-1"><?php echo date("F d, Y", strtotime($booking['PICKUP_DATE'])); ?> - <?php echo date("F d, Y", strtotime($booking['DROP_OFF_DATE'])); ?></td>
                        <td class="pt-1"><?php echo number_format($booking['PRICE'], 2); ?></td>
                        <td class="pt-1"><?php echo $total_days; ?></td>
                        <td class="pt-1"><?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end mb-6 text-sm">
            <div class="w-1/2 text-right">
                <p>Subtotal: <?php echo number_format($subtotal, 2); ?></p>
                <p>Deposit: <?php echo number_format($deposit, 2); ?></p>
                <p class="font-bold mt-1">Total: <?php echo number_format($total, 2); ?></p>
            </div>
        </div>

        <!-- Payment & Terms -->
        <div class="mb-6 text-sm">
            <p class="font-semibold mb-1">Payment Details:</p>
            <p>Payment Method: Cash (Pay Later)</p>
            <p>Payment Status: Unpaid</p>
        </div>

        <div class="text-sm">
            <p class="font-semibold mb-1">Terms & Conditions:</p>
            <ul class="list-disc list-inside">
                <li>Payment must be made in cash on or before the due date.</li>
                <li>Late payments may incur additional charges.</li>
                <li>The vehicle must be returned in the same condition as rented.</li>
            </ul>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center text-gray-500 text-sm">
            Thank you for choosing CarRental!
        </div>
    </div>
</body>

</html>