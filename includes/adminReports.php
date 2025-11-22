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
                        bvi.PENALTY,
                        bvi.NOTES,
                        bvi.IMAGE_PATH, -- placeholder, adjust path later
                        bvi.CREATED_AT,
                        COALESCE(bpd.STATUS, 'UNPAID') AS PENALTY_STATUS
                    FROM BOOKING_VEHICLE_INSPECTION bvi
                    JOIN USER_DETAILS u ON bvi.USER_ID = u.USER_ID
                    LEFT JOIN BOOKING_PAYMENT_DETAILS bpd 
                        ON bvi.BOOKING_ID = bpd.BOOKING_ID 
                        AND bpd.PAYMENT_TYPE = 'PENALTY'
                    WHERE bvi.PENALTY > 0
                    ORDER BY bvi.CREATED_AT DESC
                ";

                    $penaltyStmt = $conn->query($penaltySql);

                    if ($penaltyStmt->rowCount() > 0) {
                        foreach ($penaltyStmt as $row) {
                            // Placeholder image button
                            $imgButton = !empty($row['IMAGE_PATH'])
                                ? "<button onclick=\"reportsViewImage('/PROJECTS/RENTAL%20SYSTEM/src/images/e-receiptsFolder/{$row['IMAGE_PATH']}')\" class='bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs'>View Image</button>"
                                : "<span class='text-gray-400 text-xs italic'>No Image</span>";

                            $statusBadge = $row['PENALTY_STATUS'] === 'PAID'
                                ? "<span class='bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-medium'>PAID</span>"
                                : "<span class='bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-medium'>UNPAID</span>";

                            echo "
                        <tr class='hover:bg-gray-50'>
                            <td class='border border-gray-300 px-3 py-2'>{$row['INSPECTION_ID']}</td>
                            <td class='border border-gray-300 px-3 py-2'>{$row['AGENT_NAME']}</td>
                            <td class='border border-gray-300 px-3 py-2 text-red-600 font-semibold'>₱" . number_format($row['PENALTY'], 2) . "</td>
                            <td class='border border-gray-300 px-3 py-2'>{$row['NOTES']}</td>
                            <td class='border border-gray-300 px-3 py-2'>$imgButton</td>
                            <td class='border border-gray-300 px-3 py-2 text-center'>$statusBadge</td>
                            <td class='border border-gray-300 px-3 py-2'>" . date("M d, Y", strtotime($row['CREATED_AT'])) . "</td>
                        </tr>
                        ";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-4 text-gray-500'>No penalty reports found.</td></tr>";
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
                        <th class="border border-gray-300 px-3 py-2 text-left">Reason</th>
                        <th class="border border-gray-300 px-3 py-2 text-left">Added Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <?php
                    $maintenanceSql = "SELECT * FROM CAR_DETAILS WHERE STATUS = 'MAINTENANCE' ORDER BY CREATED_AT DESC";
                    $maintenanceStmt = $conn->query($maintenanceSql);

                    if ($maintenanceStmt->rowCount() > 0) {
                        foreach ($maintenanceStmt as $car) {
                            $reason = "Oil Change"; // default
                            echo "
                                <tr class='hover:bg-gray-50'>
                                    <td class='border border-gray-300 px-3 py-2'>{$car['CAR_ID']}</td>
                                    <td class='border border-gray-300 px-3 py-2'>{$car['CAR_NAME']}</td>
                                    <td class='border border-gray-300 px-3 py-2'>{$car['COLOR']}</td>
                                    <td class='border border-gray-300 px-3 py-2'>{$car['CAPACITY']} seats</td>
                                    <td class='border border-gray-300 px-3 py-2 text-yellow-600 font-medium'>{$car['STATUS']}</td>
                                    <td class='border border-gray-300 px-3 py-2'>{$reason}</td>
                                    <td class='border border-gray-300 px-3 py-2'>" . date("M d, Y", strtotime($car['CREATED_AT'])) . "</td>
                                </tr>
                            ";
                        }
                    } else {
                        echo "<tr><td colspan='7' class='text-center py-4 text-gray-500'>No cars under maintenance.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-8">
        <h3 class="text-xl font-semibold mb-4 text-gray-700">Sales Summary Report</h3>

        <!-- Month Filter & Print -->
        <div class="flex items-center gap-2 mb-4">
            <label for="reportMonth" class="font-medium text-gray-700">Select Month:</label>
            <input type="month" id="reportMonth" class="border p-2 rounded">
            <button onclick="filterSalesMonth()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">Filter</button>
            <button onclick="openSalesSummaryInvoice()" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition ml-auto">
                Print
            </button>

            <script>
                function openSalesSummaryInvoice() {
                    const month = document.getElementById('reportMonth').value;
                    window.open('../../includes/salesSummaryInvoice.php?month=' + month, '_blank');
                }
            </script>

        </div>

        <div class="overflow-x-auto" id="salesSummaryContainer">
            <table class="w-full border border-gray-300 text-sm border-collapse" id="salesSummaryTable">
                <thead class="bg-gray-200 text-gray-700">
                    <tr>
                        <th class="border border-gray-300 px-3 py-2 text-left">Report Item</th>
                        <th class="border border-gray-300 px-3 py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white" id="salesSummaryBody">
                    <?php
                    // Default: current month
                    $currentMonth = date('Y-m');
                    $completedStmt = $conn->prepare("SELECT COUNT(*) AS total_completed, SUM(TOTAL_COST) AS total_income 
                    FROM CUSTOMER_BOOKING_DETAILS 
                    WHERE STATUS='COMPLETED' AND DATE_FORMAT(CREATED_AT, '%Y-%m') = ?");
                    $completedStmt->execute([$currentMonth]);
                    $completed = $completedStmt->fetch(PDO::FETCH_ASSOC);

                    $penaltyStmt = $conn->prepare("SELECT COUNT(*) AS penalty_count, SUM(PENALTY) AS total_penalty 
                    FROM BOOKING_VEHICLE_INSPECTION 
                    WHERE PENALTY > 0 AND DATE_FORMAT(CREATED_AT, '%Y-%m') = ?");
                    $penaltyStmt->execute([$currentMonth]);
                    $penalties = $penaltyStmt->fetch(PDO::FETCH_ASSOC);

                    $reportData = [
                        "Total Completed Bookings" => $completed['total_completed'] ?? 0,
                        "Total Sale Income" => '₱' . number_format($completed['total_income'] ?? 0, 2),
                        "Total Penalty Count" => $penalties['penalty_count'] ?? 0,
                        "Total Penalty Amount" => '₱' . number_format($penalties['total_penalty'] ?? 0, 2)
                    ];

                    foreach ($reportData as $label => $value) {
                        echo "
                    <tr class='hover:bg-gray-50'>
                        <td class='border border-gray-300 px-3 py-2'>{$label}</td>
                        <td class='border border-gray-300 px-3 py-2 text-right font-semibold'>{$value}</td>
                    </tr>
                    ";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>


</section>

<!-- Image Preview Modal for Reports Only -->
<div id="reportsImageModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center hidden z-50">
    <div class="relative bg-white rounded-lg p-4 max-w-3xl w-full">
        <button onclick="closeReportsImage()" class="absolute top-2 right-2 text-gray-700 hover:text-gray-900 text-lg">&times;</button>
        <img id="reportsModalImg" src="" alt="Inspection Image" class="w-full max-h-[80vh] object-contain rounded-lg">
    </div>

</div>


<script>
    function filterSalesMonth() {
        const month = document.getElementById('reportMonth').value;
        if (!month) {
            Swal.fire({
                icon: 'info',
                title: 'Select a month',
                timer: 1500,
                showConfirmButton: false
            });
            return;
        }

        fetch('../../includes/filterSalesSummary.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    month
                })
            })
            .then(res => res.json())
            .then(data => {
                const tbody = document.getElementById('salesSummaryBody');
                tbody.innerHTML = '';

                if (Object.keys(data).length === 0) {
                    tbody.innerHTML = "<tr><td colspan='2' class='text-center py-4 text-gray-500'>No data found for this month.</td></tr>";
                    return;
                }

                for (let key in data) {
                    tbody.innerHTML += `
                <tr class='hover:bg-gray-50'>
                    <td class='border border-gray-300 px-3 py-2'>${key}</td>
                    <td class='border border-gray-300 px-3 py-2 text-right font-semibold'>${data[key]}</td>
                </tr>
            `;
                }
            })
            .catch(err => console.error(err));
    }

    // Print function
    function printSalesSummary() {
        const printContents = document.getElementById('salesSummaryContainer').innerHTML;
        const originalContents = document.body.innerHTML;
        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // reload to restore scripts/styles
    }

    // Scoped to reports section only
    function reportsViewImage(src) {
        document.getElementById('reportsModalImg').src = src;
        document.getElementById('reportsImageModal').classList.remove('hidden');
    }

    function closeReportsImage() {
        document.getElementById('reportsModalImg').src = '';
        document.getElementById('reportsImageModal').classList.add('hidden');
    }
</script>