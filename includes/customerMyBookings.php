  <table
      class="w-full mt-4 text-sm bg-white rounded shadow overflow-hidden">
      <thead class="bg-gray-200">
          <tr>
              <th class="px-3 py-2 text-left">Car</th>
              <th class="px-3 py-2 text-left">Pickup Date</th>
              <th class="px-3 py-2 text-left">Dropoff Date</th>
              <th class="px-3 py-2 text-left">Price</th>
              <th class="px-3 py-2 text-left">Status</th>
              <th class="px-3 py-2 text-left">Date Booked</th>
          </tr>
      </thead>
      <tbody>
          <?php
            $user_id = $_SESSION['user_id'];

            $selectStatementMyBookings = "SELECT 
            c.CAR_NAME AS car,
            DATE_FORMAT(cbd.PICKUP_DATE, '%b %d, %Y') AS pickup_date,
            DATE_FORMAT(cbd.DROP_OFF_DATE, '%b %d, %Y') AS dropoff_date,
            (DATEDIFF(cbd.DROP_OFF_DATE, cbd.PICKUP_DATE) + 1) * c.PRICE AS amount,
            cbd.STATUS AS status,
            DATE_FORMAT(cbd.CREATED_AT, '%b %d, %Y') AS created_at

        FROM CUSTOMER_BOOKING_DETAILS cbd
        JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
        WHERE cbd.STATUS = 'PENDING'
        AND cbd.USER_ID = :user_id";

            $stmt = $conn->prepare($selectStatementMyBookings);
            $stmt->execute([':user_id' => $user_id]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($rows) {
                foreach ($rows as $r) {
                    echo "<tr class='border-b'>
                <td class='px-3 py-2'>{$r['car']}</td>
                <td class='px-3 py-2'>{$r['pickup_date']}</td>
                <td class='px-3 py-2'>{$r['dropoff_date']}</td>
                <td class='px-3 py-2'>₱" . number_format($r['amount'], 2) . "</td>
                <td class='px-3 py-2 text-yellow-600 font-semibold'>{$r['status']}</td>
                <td class='px-3 py-2'>{$r['created_at']}</td>
              </tr>";
                }
            } else {
                echo "<tr><td colspan='5' class='text-center py-4 text-gray-500'>No pending bookings</td></tr>";
            }
            ?>
      </tbody>

  </table>