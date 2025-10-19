<table class="w-full mt-4 text-sm bg-white rounded shadow overflow-hidden">
    <thead class="bg-gray-200">
        <tr>
            <th class="px-3 py-2 text-left">Car</th>
            <th class="px-3 py-2 text-left">Pickup Date</th>
            <th class="px-3 py-2 text-left">Dropoff Date</th>
            <th class="px-3 py-2 text-left">Price</th>
            <th class="px-3 py-2 text-left">Status</th>
            <th class="px-3 py-2 text-left">Date Booked</th>
            <th class="px-3 py-2 text-left">Invoice</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $user_id = $_SESSION['user_id'];

        // Fetch bookings including the BOOKING_ID for invoice link
        $selectBookings = "
            SELECT 
                cbd.BOOKING_ID,
                c.CAR_NAME AS car,
                DATE_FORMAT(cbd.PICKUP_DATE, '%b %d, %Y') AS pickup_date,
                DATE_FORMAT(cbd.DROP_OFF_DATE, '%b %d, %Y') AS dropoff_date,
                (DATEDIFF(cbd.DROP_OFF_DATE, cbd.PICKUP_DATE) + 1) * c.PRICE AS amount,
                cbd.STATUS AS status,
                DATE_FORMAT(cbd.CREATED_AT, '%b %d, %Y') AS created_at
            FROM CUSTOMER_BOOKING_DETAILS cbd
            JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
            WHERE cbd.USER_ID = :user_id
            ORDER BY cbd.CREATED_AT DESC
        ";

        $stmt = $conn->prepare($selectBookings);
        $stmt->execute([':user_id' => $user_id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($rows) {
            foreach ($rows as $r) {
                // Status color mapping
                $statusColor = match ($r['status']) {
                    'PENDING' => 'text-yellow-600',
                    'FOR VERIFICATION' => 'text-blue-600',
                    'FOR PICKUP' => 'text-purple-600',
                    'ONGOING' => 'text-orange-600',
                    'EXTENDED' => 'text-indigo-600',
                    'COMPLETED' => 'text-green-600',
                    'CANCELED' => 'text-red-600',
                    default => 'text-gray-600',
                };

                echo "<tr class='border-b'>
                    <td class='px-3 py-2'>{$r['car']}</td>
                    <td class='px-3 py-2'>{$r['pickup_date']}</td>
                    <td class='px-3 py-2'>{$r['dropoff_date']}</td>
                    <td class='px-3 py-2'>₱" . number_format($r['amount'], 2) . "</td>
                    <td class='px-3 py-2 font-semibold {$statusColor}'>{$r['status']}</td>
                    <td class='px-3 py-2'>{$r['created_at']}</td>
                    <td class='px-3 py-2'>
                       <a href='../../includes/customerGenerateInvoice.php?booking_id={$r['BOOKING_ID']}' 
   target='_blank' 
   class='bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs'>
   Generate
</a>

                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7' class='text-center py-4 text-gray-500'>No bookings found</td></tr>";
        }
        ?>
    </tbody>
</table>