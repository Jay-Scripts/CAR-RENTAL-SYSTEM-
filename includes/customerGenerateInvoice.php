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
$deposit = 1000;
$total = $subtotal + $deposit;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice Preview</title>
    <script src="https://cdn.tailwindcss.com/3.4.10"></script>



</head>

<body class="bg-gray-100 p-6">
    <div class="invoice-container max-w-3xl mx-auto bg-white p-8 border border-gray-300 shadow-md">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-serif tracking-widest uppercase">Invoice</h1>
            <div class="text-right text-gray-700">
                <p class="font-semibold">Invoice No: CR-<?php echo $booking['BOOKING_ID']; ?></p>
                <p>Date: <?php echo date("F d, Y"); ?></p>
                <p>Due Date: <?php echo date("F d, Y", strtotime("+7 days")); ?></p>
            </div>
        </div>

        <hr class="border-gray-400 mb-6">

        <!-- Buttons -->
        <div class="flex justify-end mb-4">
            <button id="downloadPDF" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 text-sm rounded shadow">
                Download PDF
            </button>
        </div>

        <!-- Renter & Company -->
        <div class="flex justify-between mb-6 text-sm">
            <div class="w-1/2 pr-4">
                <p class="uppercase font-semibold mb-2">Renter Details:</p>
                <p><?php echo $booking['FULL_NAME']; ?></p>
                <p><?php echo $booking['PHONE']; ?></p>
                <p><?php echo $booking['EMAIL']; ?></p>
                <p><?php echo $booking['ADDRESS']; ?></p>
            </div>
            <div class="w-1/2 pl-4">
                <p class="uppercase font-semibold mb-2">Rental Company:</p>
                <p>CarRental</p>
                <p>Bldg. 4 Urban Deca Homes Velasquez St. Tondo, Manila</p>
                <p>+63-915-5364-4698</p>
                <p>CarRental@gmail.com</p>
            </div>
        </div>

        <!-- Table -->
        <div class="mb-6 text-sm">
            <p class="uppercase font-semibold mb-2">Rental Details:</p>
            <table class="w-full border border-gray-400 border-collapse">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border border-gray-400 px-3 py-2">Vehicle</th>
                        <th class="border border-gray-400 px-3 py-2">Rental Period</th>
                        <th class="border border-gray-400 px-3 py-2">Daily Rate</th>
                        <th class="border border-gray-400 px-3 py-2">Total Days</th>
                        <th class="border border-gray-400 px-3 py-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="border border-gray-400 px-3 py-2"><?php echo $booking['CAR_NAME']; ?></td>
                        <td class="border border-gray-400 px-3 py-2">
                            <?php echo date("F d, Y", strtotime($booking['PICKUP_DATE'])); ?> -
                            <?php echo date("F d, Y", strtotime($booking['DROP_OFF_DATE'])); ?>
                        </td>
                        <td class="border border-gray-400 px-3 py-2"><?php echo number_format($booking['PRICE'], 2); ?></td>
                        <td class="border border-gray-400 px-3 py-2"><?php echo $total_days; ?></td>
                        <td class="border border-gray-400 px-3 py-2"><?php echo number_format($subtotal, 2); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="flex justify-end mb-6 text-sm">
            <div class="w-1/2 text-right">
                <p>Subtotal: ₱<?php echo number_format($subtotal, 2); ?></p>
                <p>Deposit: ₱<?php echo number_format($deposit, 2); ?></p>
                <p class="font-bold mt-1">Total: ₱<?php echo number_format($total, 2); ?></p>
            </div>
        </div>


        <!-- Terms -->
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

    <!-- jsPDF and html2canvas (load first, outside Tailwind’s scope) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        window.addEventListener("DOMContentLoaded", () => {
            const downloadBtn = document.getElementById("downloadPDF");

            downloadBtn.addEventListener("click", async () => {
                // Ensure jsPDF is properly loaded
                if (!window.jspdf) {
                    alert("jsPDF library failed to load.");
                    return;
                }

                const {
                    jsPDF
                } = window.jspdf;
                const invoice = document.querySelector(".invoice-container");

                // Hide the button during capture
                downloadBtn.style.display = "none";

                // Capture invoice as image
                const canvas = await html2canvas(invoice, {
                    scale: 3, // high quality
                    useCORS: true
                });

                const imgData = canvas.toDataURL("image/png");
                const pdf = new jsPDF("p", "mm", "a4");

                const pdfWidth = pdf.internal.pageSize.getWidth();
                const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

                pdf.addImage(imgData, "PNG", 0, 0, pdfWidth, pdfHeight);
                pdf.save("Invoice-<?php echo $booking['BOOKING_ID']; ?>.pdf");

                // Show button again
                downloadBtn.style.display = "block";
            });
        });
    </script>

</body>

</html>