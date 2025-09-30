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
        WHERE b.STATUS = 'FOR CHECKING'
        ORDER BY b.CREATED_AT DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching bookings: " . $e->getMessage());
}
?>

<h2 class="text-2xl font-bold mb-4">Bookings for Checking</h2>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (!empty($bookings)): ?>
        <?php foreach ($bookings as $b): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <img src="<?= htmlspecialchars($b['THUMBNAIL_PATH']) ?>"
                    alt="Car Image"
                    class="object-cover rounded-2xl p-5">

                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800">
                        <?= htmlspecialchars($b['CAR_NAME']) ?> (<?= htmlspecialchars($b['COLOR']) ?>)
                    </h3>
                    <p class="text-sm text-gray-600">
                        Booked by: <?= htmlspecialchars($b['FIRST_NAME'] . ' ' . $b['LAST_NAME']) ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        Pickup: <?= date("M d, Y H:i", strtotime($b['PICKUP_DATE'])) ?>
                    </p>
                    <p class="text-sm text-gray-600">
                        Drop-off: <?= date("M d, Y H:i", strtotime($b['DROP_OFF_DATE'])) ?>
                    </p>
                    <p class="mt-2 font-bold text-orange-600">
                        ₱<?= number_format($b['TOTAL_COST'], 2) ?>
                    </p>
                </div>

                <div class="flex justify-between items-center bg-gray-50 p-3">
                    <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                        <?= htmlspecialchars($b['STATUS']) ?>
                    </span>

                    <button
                        onclick="openForm(<?= $b['BOOKING_ID'] ?>)"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        Verify
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-600 col-span-full">No bookings for checking.</p>
    <?php endif; ?>
</div>

<!-- Popup Modal -->
<div id="popupForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6 relative">
        <h2 class="text-xl font-bold mb-4">Verification Form</h2>

        <form id="verificationForm">
            <input type="hidden" name="booking_id" id="formBookingId">

            <label class="block mb-2 font-medium">Remarks</label>
            <textarea name="remarks" class="w-full border rounded p-2 mb-4" required></textarea>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeForm()"
                    class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                    Cancel
                </button>
                <button type="submit"
                    class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openForm(bookingId) {
        document.getElementById("formBookingId").value = bookingId;
        document.getElementById("popupForm").classList.remove("hidden");
    }

    function closeForm() {
        document.getElementById("popupForm").classList.add("hidden");
    }

    document.getElementById("verificationForm").addEventListener("submit", function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('../../includes/updateBookingStatus.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === 'success') {
                    closeForm();
                    location.reload();
                } else {
                    alert("Error: " + data);
                }
            });
    });
</script>