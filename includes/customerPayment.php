<?php

try {
    $sql = "
        SELECT 
            b.BOOKING_ID, 
            b.PICKUP_DATE, 
            b.DROP_OFF_DATE, 
            b.TRIP_DETAILS,
            b.TOTAL_COST,
            b.STATUS,
            c.CAR_NAME, 
            c.COLOR, 
            c.THUMBNAIL_PATH,
            u.FIRST_NAME, 
            u.LAST_NAME
        FROM CUSTOMER_BOOKING_DETAILS b
        INNER JOIN CAR_DETAILS c ON b.CAR_ID = c.CAR_ID
        INNER JOIN USER_DETAILS u ON b.USER_ID = u.USER_ID
        WHERE b.STATUS = 'PENDING'
        ORDER BY b.CREATED_AT DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching bookings: " . $e->getMessage());
}
?>

<h2 class="text-2xl font-bold mb-4">Pending Bookings</h2>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (!empty($bookings)): ?>
        <?php foreach ($bookings as $booking): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <img src="<?= htmlspecialchars($booking['THUMBNAIL_PATH']) ?>"
                    alt="Car Image"
                    class="object-cover">

                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <?= htmlspecialchars($booking['CAR_NAME']) ?> (<?= htmlspecialchars($booking['COLOR']) ?>)
                    </h3>
                    <p class="text-sm text-gray-600">
                        Booked by: <?= htmlspecialchars($booking['FIRST_NAME'] . " " . $booking['LAST_NAME']) ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        Pickup: <?= date("M d, Y H:i", strtotime($booking['PICKUP_DATE'])) ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        Drop-off: <?= date("M d, Y H:i", strtotime($booking['DROP_OFF_DATE'])) ?>
                    </p>
                    <?php if (!empty($booking['TRIP_DETAILS'])): ?>
                        <p class="text-sm text-gray-600 italic">"<?= htmlspecialchars($booking['TRIP_DETAILS']) ?>"</p>
                    <?php endif; ?>
                    <p class="mt-2 font-bold text-orange-600">
                        ₱<?= number_format($booking['TOTAL_COST'], 2) ?>
                    </p>
                </div>

                <div class="flex justify-between items-center bg-gray-50 p-3">
                    <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                        <?= htmlspecialchars($booking['STATUS']) ?>
                    </span>
                    <button
                        onclick="openPaymentModal(<?= $booking['BOOKING_ID'] ?>, <?= $booking['TOTAL_COST'] ?>)"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        Pay Now
                    </button>
                </div>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-600 col-span-full">No pending bookings at the moment.</p>
    <?php endif; ?>
</div>
<!-- Payment Modal -->
<div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
        <!-- Step 1: QR Scan -->
        <div id="step1">
            <h2 class="text-xl font-bold mb-4">Scan QR to Pay</h2>
            <p class="mb-2">Use GCash app to scan this QR code:</p>
            <img src="your-gcash-qr.png" alt="GCash QR" class="mx-auto w-48 mb-4" />
            <button onclick="nextStep()" class="bg-blue-500 text-white px-4 py-2 rounded w-full">
                Next
            </button>
        </div>

        <!-- Step 2: Upload Receipt -->
        <div id="step2" class="hidden">
            <h2 class="text-xl font-bold mb-4">Upload E-Receipt</h2>
            <input type="file" id="receiptUpload" class="border p-2 w-full mb-4" />
            <button onclick="submitReceipt()" class="bg-green-500 text-white px-4 py-2 rounded w-full">
                Submit Payment
            </button>
        </div>

        <!-- Close Btn -->
        <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-500">
            &times;
        </button>
    </div>
</div>
<script>
    let currentBookingId = null;
    let currentAmount = null;

    function openPaymentModal(bookingId, amount) {
        currentBookingId = bookingId;
        currentAmount = amount;
        document.getElementById("paymentModal").classList.remove("hidden");
    }

    function closeModal() {
        document.getElementById("paymentModal").classList.add("hidden");
        document.getElementById("step1").classList.remove("hidden");
        document.getElementById("step2").classList.add("hidden");
        currentBookingId = null;
        currentAmount = null;
    }

    function nextStep() {
        document.getElementById("step1").classList.add("hidden");
        document.getElementById("step2").classList.remove("hidden");
    }

    function submitReceipt() {
        let file = document.getElementById("receiptUpload").files[0];
        if (!file) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: "Please attach your E-Payment Receipt!"
            });
            return;
        }

        let formData = new FormData();
        formData.append("receipt", file);
        formData.append("booking_id", currentBookingId);

        fetch("../../includes/customerPaymentUploadingERecept.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === "success") {
                    Swal.fire({
                        title: "E-Receipt Sent!",
                        text: "We will inform you once your vehicle is ready for pickup.",
                        icon: "success",
                    }).then(() => {
                        closeModal();
                        location.reload(); // refresh booking list
                    });
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Upload Failed",
                        text: data
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: "error",
                    title: "Server Error",
                    text: err
                });
            });
    }
</script>