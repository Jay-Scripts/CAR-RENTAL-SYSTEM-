<?php

try {
    // Fetch bookings with CHECKING status
    $sql = "
        SELECT 
            b.BOOKING_ID, 
            b.PICKUP_DATE, 
            b.DROP_OFF_DATE, 
            b.TRIP_DETAILS,
            b.TOTAL_COST,
            b.STATUS,
            c.CAR_ID,
            c.CAR_NAME, 
            c.COLOR, 
            c.THUMBNAIL_PATH,
            u.FIRST_NAME, 
            u.LAST_NAME
        FROM CUSTOMER_BOOKING_DETAILS b
        INNER JOIN CAR_DETAILS c ON b.CAR_ID = c.CAR_ID
        INNER JOIN USER_DETAILS u ON b.USER_ID = u.USER_ID
        WHERE b.STATUS = 'CHECKING' AND c.STATUS = 'RENTED'
        ORDER BY b.CREATED_AT DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching bookings: " . $e->getMessage());
}
?>

<h2 class="text-2xl font-bold mb-4">Bookings for Exterior Checking</h2>

<div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (!empty($bookings)): ?>
        <?php foreach ($bookings as $b): ?>
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                <img src="<?= htmlspecialchars($b['THUMBNAIL_PATH']) ?>" alt="Car Image" class="object-cover rounded-2xl p-5">
                <div class="p-4">
                    <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($b['CAR_NAME']) ?> (<?= htmlspecialchars($b['COLOR']) ?>)</h3>
                    <p class="text-sm text-gray-600">Booked by: <?= htmlspecialchars($b['FIRST_NAME'] . ' ' . $b['LAST_NAME']) ?></p>
                    <p class="text-sm text-gray-600">Pickup: <?= date("M d, Y H:i", strtotime($b['PICKUP_DATE'])) ?></p>
                    <p class="text-sm text-gray-600">Drop-off: <?= date("M d, Y H:i", strtotime($b['DROP_OFF_DATE'])) ?></p>
                    <p class="mt-2 font-bold text-orange-600">₱<?= number_format($b['TOTAL_COST'], 2) ?></p>
                </div>
                <div class="flex justify-between items-center bg-gray-50 p-3">
                    <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full"><?= htmlspecialchars($b['STATUS']) ?></span>
                    <button
                        onclick="openForm(<?= $b['BOOKING_ID'] ?>)"
                        class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                        Upload Diagnostic
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-600 col-span-full">No bookings for checking.</p>
    <?php endif; ?>
</div>

<!-- Popup Form -->
<div id="popupForm" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6 relative">
        <form id="inspectionForm" method="POST" enctype="multipart/form-data" class="m-5">
            <input type="hidden" name="booking_id" id="formBookingId">

            <div>
                <label class="block font-medium text-gray-700">Notes for extra info</label>
                <textarea name="notes" class="w-full border rounded-lg p-3 mt-1 focus:ring-2 focus:ring-blue-500"></textarea>
            </div>

            <div>
                <label class="block font-medium text-gray-700">Penalty Amount:</label>
                <input name="penalty" type="number" class="w-full border rounded-lg p-3 mt-1 focus:ring-2 focus:ring-blue-500" min="0">
            </div>

            <div>
                <label class="block font-medium text-gray-700">Upload proof of vehicle condition</label>
                <input type="file" name="images[]" multiple accept="image/*"
                    class="w-full border rounded-lg p-2 mt-1 cursor-pointer file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-600 file:text-white hover:file:bg-blue-700">
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <button type="button" onclick="closeForm()" class="bg-gray-300 hover:bg-gray-400 text-black px-4 py-2 rounded">
                    Cancel
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Confirm
                </button>
            </div>
        </form>
    </div>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function openForm(bookingId) {
        document.getElementById("formBookingId").value = bookingId;
        document.getElementById("popupForm").classList.remove("hidden");
    }

    function closeForm() {
        document.getElementById("popupForm").classList.add("hidden");
    }

    document.getElementById("inspectionForm").addEventListener("submit", function(e) {
        e.preventDefault(); // prevent default form submission

        const formData = new FormData(this);

        fetch('../../includes/rentalAgentForInspectionUpdateStatus.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.text())
            .then(data => {
                data = data.trim();
                if (data === 'success') {
                    Swal.fire({
                        title: 'Inspection submitted!',
                        text: 'Car status updated to MAINTENANCE',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        closeForm();
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Error!',
                        text: data,
                        icon: 'error'
                    });
                }
            })
            .catch(err => Swal.fire('Error!', err, 'error'));
    });
</script>