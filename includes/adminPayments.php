    <h2 class="text-2xl font-bold mb-4">Pending Bookings for Verification</h2>

    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
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
                 c.PLATE_NUM,  
                u.FIRST_NAME, 
                u.LAST_NAME,
                p.RECEIPT_PATH
            FROM CUSTOMER_BOOKING_DETAILS b
            INNER JOIN CAR_DETAILS c ON b.CAR_ID = c.CAR_ID
            INNER JOIN USER_DETAILS u ON b.USER_ID = u.USER_ID
            LEFT JOIN BOOKING_PAYMENT_DETAILS p ON b.BOOKING_ID = p.BOOKING_ID
            WHERE b.STATUS = 'FOR VERIFICATION'
            ORDER BY b.CREATED_AT DESC
        ";
            $stmt = $conn->prepare($sql);
            $stmt->execute();
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error fetching bookings: " . $e->getMessage());
        }
        ?>

        <?php if (!empty($bookings)): ?>
            <?php foreach ($bookings as $b): ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-200">
                    <img src="<?= htmlspecialchars($b['THUMBNAIL_PATH'] ?: 'placeholder.png') ?>" alt="Car Image" class="object-cover h-48 w-full">
                    <div class="p-4">
                        <h3 class="text-lg font-semibold text-gray-800"><?= htmlspecialchars($b['CAR_NAME']) ?> (<?= htmlspecialchars($b['COLOR']) ?>)</h3>
                        <p class="text-sm text-gray-600">Plate Number: <?= htmlspecialchars($b['PLATE_NUM']) ?></p>
                        <p class="text-sm text-gray-600">Booked by: <?= htmlspecialchars($b['FIRST_NAME'] . ' ' . $b['LAST_NAME']) ?></p>
                        <p class="text-sm text-gray-600">Pickup: <?= date("M d, Y", strtotime($b['PICKUP_DATE'])) ?></p>
                        <p class="text-sm text-gray-600">Drop-off: <?= date("M d, Y", strtotime($b['DROP_OFF_DATE'])) ?></p>

                        <?php if (!empty($b['TRIP_DETAILS'])): ?>
                            <p class="text-sm text-gray-600 italic">"<?= htmlspecialchars($b['TRIP_DETAILS']) ?>"</p>
                        <?php endif; ?>
                        <p class="mt-2 font-bold text-orange-600">₱<?= number_format($b['TOTAL_COST'], 2) ?></p>
                    </div>
                    <div class="flex justify-between items-center bg-gray-50 p-3">
                        <span class="px-3 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full"><?= htmlspecialchars($b['STATUS']) ?></span>
                        <button
                            onclick="openVerificationModal(<?= $b['BOOKING_ID'] ?>, '<?= addslashes($b['RECEIPT_PATH'] ?: 'placeholder.png') ?>')"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded">
                            View E-Receipt
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-gray-600 col-span-full">No pending bookings for verification.</p>
        <?php endif; ?>
    </div>
    <!-- Verification Modal -->
    <div id="verificationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
        <div class="bg-white w-full max-w-lg rounded-lg shadow-lg p-6 relative">
            <h2 class="text-xl font-bold mb-4">E-Receipt</h2>
            <div id="receiptDetails" class="mb-4">
                <img id="receiptImage" src="" alt="E-Receipt" class="mx-auto w-48 mb-4" />
                <p id="receiptInfo" class="text-gray-700"></p>
            </div>
            <!-- New notes input -->
            <div class="mb-4">
                <label for="bookingNotes" class="block text-gray-700 font-medium mb-1">Remarks / Notes:</label>
                <textarea id="bookingNotes" class="w-full border border-gray-300 rounded px-3 py-2" placeholder="Enter notes here..."></textarea>
            </div>
            <div class="flex gap-3">
                <button id="approveBtn" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded w-1/2">Approve</button>
                <button id="declineBtn" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded w-1/2">Decline</button>
            </div>
            <button onclick="closeModal()" class="absolute top-2 right-2 text-gray-500">&times;</button>
        </div>
    </div>


    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let currentBookingId = null;

        function openVerificationModal(bookingId, receiptPath) {
            currentBookingId = bookingId;
            const imgPath = receiptPath && receiptPath !== '' ? '../' + receiptPath : '../placeholder.png';
            document.getElementById('receiptImage').src = imgPath;
            document.getElementById('receiptInfo').textContent = `Booking ID: ${bookingId}`;
            document.getElementById('verificationModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('verificationModal').classList.add('hidden');
            currentBookingId = null;
        }

        function handleBooking(action) {
            if (!currentBookingId) return;

            const notes = encodeURIComponent(document.getElementById('bookingNotes').value.trim());

            fetch('../../includes/adminPaymentsUpdates.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'booking_id=' + currentBookingId + '&action=' + action + '&remarks=' + notes
                })
                .then(res => res.json())
                .then(data => {
                    Swal.fire({
                        icon: data.status === 'success' ? 'success' : 'error',
                        title: data.status === 'success' ? 'Success!' : 'Error!',
                        text: data.message,
                        showConfirmButton: true
                    }).then(() => {
                        if (data.status === 'success') location.reload();
                    });
                })
                .catch(err => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: 'Something went wrong.'
                    });
                });
        }


        // Approve / Decline buttons
        document.getElementById('approveBtn').addEventListener('click', () => handleBooking('approve'));
        document.getElementById('declineBtn').addEventListener('click', () => handleBooking('decline'));
    </script>