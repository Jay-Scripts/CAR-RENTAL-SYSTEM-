<section id="reports" class="animate-fadeSlide hidden bg-gray-500/20 rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Reports</h2>

    <!-- Rental Agent Penalty Reports -->
    <div class="mb-8">
        <h3 class="text-xl font-semibold mb-2 text-gray-700">Rental Agent Penalty Reports</h3>
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 text-sm border-collapse">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="border border-gray-300 px-3 py-2 text-left">Inspection ID</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Agent Name</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Car</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Penalty</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Notes</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Image</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Penalty Status</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php

                    $penaltySql = "
            SELECT 
              bvi.INSPECTION_ID,
              CONCAT(u.FIRST_NAME, ' ', u.LAST_NAME) AS AGENT_NAME,
              c.CAR_NAME,
              bvi.PENALTY,
              bvi.NOTES,
              bvi.IMAGE_PATH,
              bvi.CREATED_AT,
              COALESCE(bpd.STATUS, 'UNPAID') AS PENALTY_STATUS
            FROM BOOKING_VEHICLE_INSPECTION bvi
            JOIN USER_DETAILS u ON bvi.USER_ID = u.USER_ID
            JOIN CUSTOMER_BOOKING_DETAILS cbd ON bvi.BOOKING_ID = cbd.BOOKING_ID
            JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
            LEFT JOIN BOOKING_PAYMENT_DETAILS bpd 
              ON bvi.BOOKING_ID = bpd.BOOKING_ID 
              AND bpd.PAYMENT_TYPE = 'PENALTY'
            WHERE bvi.PENALTY > 0
            ORDER BY bvi.CREATED_AT DESC
          ";

                    $penaltyStmt = $conn->query($penaltySql);

                    if ($penaltyStmt->rowCount() > 0) {
                        foreach ($penaltyStmt as $row) {
                            $imgButton = !empty($row['IMAGE_PATH'])
                                ? "<button onclick=\"viewImage('../{$row['IMAGE_PATH']}')\" class='bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs'>View Image</button>"
                                : "<span class='text-gray-400 text-xs italic'>No Image</span>";

                            $statusBadge = $row['PENALTY_STATUS'] === 'PAID'
                                ? "<span class='bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium'>PAID</span>"
                                : "<span class='bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-medium'>UNPAID</span>";

                            echo "
              <tr class='hover:bg-gray-50'>
                <td class='border border-gray-300 px-3 py-2'>{$row['INSPECTION_ID']}</td>
                <td class='border border-gray-300 px-3 py-2'>{$row['AGENT_NAME']}</td>
                <td class='border border-gray-300 px-3 py-2'>{$row['CAR_NAME']}</td>
                <td class='border border-gray-300 px-3 py-2 text-red-600 font-semibold'>₱" . number_format($row['PENALTY'], 2) . "</td>
                <td class='border border-gray-300 px-3 py-2'>{$row['NOTES']}</td>
                <td class='border border-gray-300 px-3 py-2'>$imgButton</td>
                <td class='border border-gray-300 px-3 py-2 text-center'>$statusBadge</td>
                <td class='border border-gray-300 px-3 py-2'>" . date("M d, Y", strtotime($row['CREATED_AT'])) . "</td>
              </tr>
              ";
                        }
                    } else {
                        echo "<tr><td colspan='8' class='text-center py-4 text-gray-500'>No penalty reports found.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Car Maintenance Reports -->
    <div>
        <h3 class="text-xl font-semibold mb-2 text-gray-700">Car Maintenance Reports</h3>
        <div class="overflow-x-auto">
            <table class="w-full border border-gray-300 text-sm border-collapse">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="border border-gray-300 px-3 py-2 text-left">Car ID</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Car Name</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Color</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Capacity</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Status</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Added Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    $maintenanceSql = "SELECT * FROM CAR_DETAILS WHERE STATUS = 'MAINTENANCE' ORDER BY CREATED_AT DESC";
                    $maintenanceStmt = $conn->query($maintenanceSql);

                    if ($maintenanceStmt->rowCount() > 0) {
                        foreach ($maintenanceStmt as $car) {
                            echo "
              <tr class='hover:bg-gray-50'>
                <td class='border border-gray-300 px-3 py-2'>{$car['CAR_ID']}</td>
                <td class='border border-gray-300 px-3 py-2'>{$car['CAR_NAME']}</td>
                <td class='border border-gray-300 px-3 py-2'>{$car['COLOR']}</td>
                <td class='border border-gray-300 px-3 py-2'>{$car['CAPACITY']} seats</td>
                <td class='border border-gray-300 px-3 py-2 text-yellow-600 font-medium'>{$car['STATUS']}</td>
                <td class='border border-gray-300 px-3 py-2'>" . date("M d, Y", strtotime($car['CREATED_AT'])) . "</td>
              </tr>
              ";
                        }
                    } else {
                        echo "<tr><td colspan='6' class='text-center py-4 text-gray-500'>No cars under maintenance.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Image Preview Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
    <div class="relative bg-white rounded-lg p-4 max-w-3xl w-full">
        <button onclick="closeImage()" class="absolute top-2 right-2 text-gray-700 hover:text-gray-900 text-lg">&times;</button>
        <img id="modalImg" src="" alt="Inspection Image" class="w-full max-h-[80vh] object-contain rounded-lg">
    </div>
</div>

<script>
    function viewImage(src) {
        document.getElementById('modalImg').src = src;
        document.getElementById('imageModal').classList.remove('hidden');
    }

    function closeImage() {
        document.getElementById('modalImg').src = '';
        document.getElementById('imageModal').classList.add('hidden');
    }
</script>