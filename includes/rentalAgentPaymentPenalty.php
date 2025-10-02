<?php

// Fetch all bookings in CHECKING status with inspection and penalty details
try {
    $sql = "
    SELECT 
        b.BOOKING_ID,
        u.FIRST_NAME AS CUSTOMER_FIRST,
        u.LAST_NAME  AS CUSTOMER_LAST,
        c.CAR_NAME,
        c.COLOR,
        b.PICKUP_DATE,
        b.DROP_OFF_DATE,
        b.TOTAL_COST,
        i.INSPECTION_ID,
        i.IMAGE_PATH,
        i.NOTES,
        i.PENALTY,
        iu.FIRST_NAME AS INSPECTOR_FIRST,
        iu.LAST_NAME  AS INSPECTOR_LAST,
        p.PAYMENT_DETAILS_ID,
        p.STATUS AS PAYMENT_STATUS
    FROM CUSTOMER_BOOKING_DETAILS b
    INNER JOIN USER_DETAILS u 
        ON b.USER_ID = u.USER_ID
    INNER JOIN CAR_DETAILS c 
        ON b.CAR_ID = c.CAR_ID
    LEFT JOIN BOOKING_VEHICLE_INSPECTION i 
        ON b.BOOKING_ID = i.BOOKING_ID
    LEFT JOIN USER_DETAILS iu 
        ON i.USER_ID = iu.USER_ID
    LEFT JOIN BOOKING_PAYMENT_DETAILS p 
        ON b.BOOKING_ID = p.BOOKING_ID AND p.PAYMENT_TYPE = 'PENALTY'
    WHERE b.STATUS = 'CHECKING'
    ORDER BY b.CREATED_AT DESC
";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $penalties = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching penalties: " . $e->getMessage());
}
?>

<h2 class="text-2xl font-bold mb-4">Penalty Payments for Checking Bookings</h2>

<?php if (!empty($penalties)): ?>
    <div class="overflow-x-auto">
        <table class="min-w-full table-auto border border-gray-300 bg-white rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="border px-4 py-2 text-left">Booking ID</th>
                    <th class="border px-4 py-2 text-left">Customer</th>
                    <th class="border px-4 py-2 text-left">Car</th>
                    <th class="border px-4 py-2 text-left">Pickup</th>
                    <th class="border px-4 py-2 text-left">Drop-off</th>
                    <th class="border px-4 py-2 text-left">Total Cost</th>
                    <th class="border px-4 py-2 text-left">Penalty</th>
                    <th class="border px-4 py-2 text-left">Notes</th>
                    <th class="border px-4 py-2 text-left">Actions</th>
                    <th class="border px-4 py-2 text-left">Inspector</th>

                </tr>
            </thead>
            <tbody>
                <?php foreach ($penalties as $p): ?>
                    <tr class="border-b">
                        <td class="border px-4 py-2"><?= htmlspecialchars($p['BOOKING_ID']) ?></td>

                        <!-- Customer column now shows Inspector -->
                        <td class="border px-4 py-2">
                            <?= htmlspecialchars(($p['INSPECTOR_FIRST'] ?? '') . ' ' . ($p['INSPECTOR_LAST'] ?? '')) ?>
                        </td>

                        <td class="border px-4 py-2"><?= htmlspecialchars($p['CAR_NAME'] . ' (' . $p['COLOR'] . ')') ?></td>
                        <td class="border px-4 py-2"><?= date("M d, Y", strtotime($p['PICKUP_DATE'])) ?></td>
                        <td class="border px-4 py-2"><?= date("M d, Y", strtotime($p['DROP_OFF_DATE'])) ?></td>
                        <td class="border px-4 py-2">₱<?= number_format($p['TOTAL_COST'], 2) ?></td>
                        <td class="border px-4 py-2 text-red-600 font-semibold">₱<?= number_format($p['PENALTY'], 2) ?></td>
                        <td class="border px-4 py-2"><?= htmlspecialchars($p['NOTES'] ?? '') ?></td>
                        <td class="border px-4 py-2 flex gap-2">
                            <?php if (!empty($p['IMAGE_PATH'])): ?>
                                <button onclick="viewImage('../<?= $p['IMAGE_PATH'] ?>')" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs">
                                    View Image
                                </button>
                            <?php endif; ?>

                            <?php if (!empty($p['PAYMENT_DETAILS_ID']) && $p['PAYMENT_STATUS'] === 'UNPAID'): ?>
                                <button onclick="markAsPaid(<?= $p['PAYMENT_DETAILS_ID'] ?>, <?= $p['BOOKING_ID'] ?>)" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">
                                    Mark as Paid
                                </button>
                            <?php elseif (!empty($p['PAYMENT_DETAILS_ID']) && $p['PAYMENT_STATUS'] === 'PAID'): ?>
                                <span class="text-green-600 font-semibold text-xs">Paid</span>
                            <?php endif; ?>
                        </td>

                        <!-- Inspector column now shows Customer -->
                        <td class="border px-4 py-2">
                            <?= htmlspecialchars($p['CUSTOMER_FIRST'] . ' ' . $p['CUSTOMER_LAST']) ?>
                        </td>
                    </tr>


                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <p class="text-gray-600">No penalties to display.</p>
<?php endif; ?>

<!-- Modal for Image View -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
    <div class="relative bg-white rounded-lg p-4 max-w-lg w-full">
        <button onclick="closeImage()" class="absolute top-2 right-2 text-gray-700 hover:text-gray-900">&times;</button>
        <img id="modalImg" src="" alt="Inspection Image" class="w-full rounded-lg">
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function viewImage(src) {
        document.getElementById('modalImg').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImage() {
        document.getElementById('imageModal').classList.add('hidden');
    }

    function markAsPaid(paymentId, bookingId) {
        Swal.fire({
            title: 'Confirm Payment?',
            text: "This will mark the penalty as PAID and complete the booking.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, mark as paid'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('../../includes/rentalAgentPaymentPenaltyUpdate.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            payment_id: paymentId,
                            booking_id: bookingId
                        })
                    })
                    .then(res => res.text())
                    .then(data => {
                        if (data.trim() === 'success') {
                            Swal.fire('Updated!', 'Penalty marked as paid.', 'success').then(() => location.reload());
                        } else {
                            Swal.fire('Error!', data, 'error');
                        }
                    });
            }
        });
    }
</script>