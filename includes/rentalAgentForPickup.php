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
        WHERE b.STATUS = 'FOR PICKUP'
        ORDER BY b.CREATED_AT DESC
    ";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching bookings: " . $e->getMessage());
}
?>

<h2 class="text-2xl font-bold mb-4">Bookings for Pickup</h2>

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
                    <?php if (!empty($b['TRIP_DETAILS'])): ?>
                        <p class="text-sm text-gray-600 italic">
                            "<?= htmlspecialchars($b['TRIP_DETAILS']) ?>"
                        </p>
                    <?php endif; ?>
                    <p class="mt-2 font-bold text-orange-600">
                        ₱<?= number_format($b['TOTAL_COST'], 2) ?>
                    </p>
                </div>

                <div class="flex justify-between items-center bg-gray-50 p-3">
                    <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">
                        <?= htmlspecialchars($b['STATUS']) ?>
                    </span>

                    <button
                        onclick="updateBookingStatus(<?= $b['BOOKING_ID'] ?>)"
                        class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded">
                        Mark as Ongoing
                    </button>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-gray-600 col-span-full">No bookings waiting for pickup.</p>
    <?php endif; ?>
</div>

<script>
    function updateBookingStatus(bookingId) {
        if (!confirm("Mark this booking as ONGOING?")) return;

        fetch('../../includes/rentalAgentForPickupUpdateStatus.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'booking_id=' + bookingId + '&new_status=ONGOING'
            })
            .then(res => res.text())
            .then(data => {
                if (data.trim() === 'success') {
                    location.reload();
                } else {
                    alert('Error: ' + data);
                }
            });
    }
</script>