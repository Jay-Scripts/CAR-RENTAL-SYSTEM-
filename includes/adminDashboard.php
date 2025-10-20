<?php
// ====== DASHBOARD QUERIES ======

// 1. Active Reservations
$select_query_active_reservation = "
    SELECT COUNT(*) AS active_count
    FROM CUSTOMER_BOOKING_DETAILS
    WHERE STATUS NOT IN ('COMPLETED', 'CANCELED')
";
$stmt = $conn->query($select_query_active_reservation);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$active_count = $row['active_count'];


// 2. Vehicles Available
$select_query_active_vehicles = "
    SELECT COUNT(*) AS available_count
    FROM CAR_DETAILS
    WHERE STATUS = 'AVAILABLE'
";
$stmt = $conn->query($select_query_active_vehicles);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$available_count = $row['available_count'];


// 3. Payments (Income) This Month
$select_query_total_sale_this_month = "
    SELECT COALESCE(SUM(
        (DATEDIFF(cbd.DROP_OFF_DATE, cbd.PICKUP_DATE)) * c.PRICE
    ), 0) AS total_income
    FROM CUSTOMER_BOOKING_DETAILS cbd
    JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
    JOIN BOOKING_PAYMENT_DETAILS bpd ON cbd.BOOKING_ID = bpd.BOOKING_ID
    WHERE cbd.STATUS = 'COMPLETED'
      AND bpd.PAYMENT_TYPE = 'BOOKING FEE'
      AND MONTH(cbd.CREATED_AT) = MONTH(CURRENT_DATE())
      AND YEAR(cbd.CREATED_AT) = YEAR(CURRENT_DATE())
";
$stmt = $conn->query($select_query_total_sale_this_month);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$total_income = $row['total_income'];


// 4. Reservation Trends (daily bookings this month)
$reservationQuery = "
    SELECT DATE(CREATED_AT) AS day, COUNT(*) AS bookings
    FROM CUSTOMER_BOOKING_DETAILS
    WHERE STATUS = 'COMPLETED'
      AND MONTH(CREATED_AT) = MONTH(CURRENT_DATE())
      AND YEAR(CREATED_AT) = YEAR(CURRENT_DATE())
    GROUP BY DATE(CREATED_AT)
    ORDER BY day ASC
";
$reservationResult = $conn->query($reservationQuery)->fetchAll(PDO::FETCH_ASSOC);

$reservationLabels = array_column($reservationResult, 'day');
$reservationData   = array_column($reservationResult, 'bookings');


// 5. Top Cars
$topCarsQuery = "
    SELECT c.CAR_NAME, COUNT(cbd.BOOKING_ID) AS rented_count
    FROM CUSTOMER_BOOKING_DETAILS cbd
    JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
    JOIN BOOKING_PAYMENT_DETAILS bpd ON cbd.BOOKING_ID = bpd.BOOKING_ID
    WHERE cbd.STATUS = 'COMPLETED'
      AND bpd.PAYMENT_TYPE = 'BOOKING FEE'
    GROUP BY c.CAR_ID
    ORDER BY rented_count DESC
    LIMIT 5
";
$topCarsResult = $conn->query($topCarsQuery)->fetchAll(PDO::FETCH_ASSOC);

$topCarsLabels = array_column($topCarsResult, 'CAR_NAME');
$topCarsData   = array_column($topCarsResult, 'rented_count');

// 6. Income by Car
$incomeQuery = "
    SELECT c.CAR_NAME,
           SUM((DATEDIFF(cbd.DROP_OFF_DATE, cbd.PICKUP_DATE) + 1) * c.PRICE) AS total_income
    FROM CUSTOMER_BOOKING_DETAILS cbd
    JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
    JOIN BOOKING_PAYMENT_DETAILS bpd ON cbd.BOOKING_ID = bpd.BOOKING_ID
    WHERE cbd.STATUS = 'COMPLETED'
      AND bpd.PAYMENT_TYPE = 'BOOKING FEE'
    GROUP BY c.CAR_ID
    ORDER BY total_income DESC
";

// 7. Penalty Summary
$penaltyQuery = "
    SELECT 
        CONCAT(u.FIRST_NAME, ' ', u.LAST_NAME) AS agent_name,
        SUM(bvi.PENALTY) AS total_penalty
    FROM BOOKING_VEHICLE_INSPECTION bvi
    JOIN USER_DETAILS u ON bvi.USER_ID = u.USER_ID
    WHERE bvi.PENALTY > 0
    GROUP BY u.USER_ID
    ORDER BY total_penalty DESC
    LIMIT 5
";
$penaltyResult = $conn->query($penaltyQuery)->fetchAll(PDO::FETCH_ASSOC);

$penaltyLabels = array_column($penaltyResult, 'agent_name');
$penaltyData   = array_column($penaltyResult, 'total_penalty');

$incomeResult = $conn->query($incomeQuery)->fetchAll(PDO::FETCH_ASSOC);

$incomeLabels = array_column($incomeResult, 'CAR_NAME');
$incomeData   = array_column($incomeResult, 'total_income');
?>
<h2 class="text-xl sm:text-2xl font-bold mb-2 text-gray-800">Car Rental Dashboard</h2>
<p class="text-gray-500 mb-6 text-sm sm:text-base">Overview of system activity</p>

<!-- KPI Cards -->
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 mb-8">
    <!-- Active Reservations -->
    <div class="bg-white rounded-xl shadow-md p-4 sm:p-5 flex items-center justify-between hover:shadow-lg transition border-l-4 border-[#df927f]">
        <div>
            <p class="text-gray-500 text-xs sm:text-sm">Active Reservations</p>
            <p class="text-2xl sm:text-3xl font-bold text-[#df927f] mt-1"><?php echo $active_count; ?></p>
        </div>
        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-[#df927f]" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 
                   002-2V7a2 2 0 00-2-2H5a2 2 0 
                   00-2 2v12a2 2 0 002 2z" />
        </svg>
    </div>

    <!-- Vehicles Available -->
    <div class="bg-white rounded-xl shadow-md p-4 sm:p-5 flex items-center justify-between hover:shadow-lg transition border-l-4 border-green-600">
        <div>
            <p class="text-gray-500 text-xs sm:text-sm">Vehicles Available</p>
            <p class="text-2xl sm:text-3xl font-bold text-green-600 mt-1"><?php echo $available_count; ?></p>
        </div>
        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 13l2-5a2 2 0 011.9-1.4h10.2a2 2 0 
                   011.9 1.4l2 5M5 13h14m-9 4h4m-9 0h.01M19 17h.01" />
        </svg>
    </div>

    <!-- Payments This Month -->
    <div class="bg-white rounded-xl shadow-md p-4 sm:p-5 flex items-center justify-between hover:shadow-lg transition border-l-4 border-blue-600">
        <div>
            <p class="text-gray-500 text-xs sm:text-sm">Income This Month</p>
            <p class="text-2xl sm:text-3xl font-bold text-blue-600 mt-1">₱<?php echo number_format($total_income, 2); ?></p>
        </div>
        <svg class="w-7 h-7 sm:w-8 sm:h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
            viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 
                   00-2 2v10a2 2 0 002 2h10a2 2 0 
                   002-2v-2m0-4h4m-4 0a2 2 0 
                   110-4 2 2 0 010 4z" />
        </svg>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <!-- Reservation Trends -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-base sm:text-lg font-semibold mb-2">Reservation Trends (This Month)</h3>
        <canvas id="reservationChart" class="w-full" height="150"></canvas>
    </div>

    <!-- Top Rented Cars -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-base sm:text-lg font-semibold mb-2">Top Rented Cars</h3>
        <canvas id="topCarsChart" class="w-full" height="150"></canvas>
    </div>

    <!-- Total Income by Car -->
    <div class="bg-white p-4 rounded-lg shadow flex flex-col items-center">
        <h3 class="text-base sm:text-lg font-semibold mb-2">Total Income by Car</h3>
        <div class="w-60 h-60">
            <canvas id="incomePieChart"></canvas>
        </div>
    </div>


    <!-- Penalty Summary -->
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-base sm:text-lg font-semibold mb-2">Penalty Summary (Top 5 Agents)</h3>
        <canvas id="penaltyChart" class="w-full" height="150"></canvas>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Reservation Chart (Dynamic)
    new Chart(document.getElementById('reservationChart'), {
        type: 'line',
        data: {
            labels: <?php echo json_encode($reservationLabels); ?>,
            datasets: [{
                label: 'Bookings',
                data: <?php echo json_encode($reservationData); ?>,
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59,130,246,0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Top Cars Chart
    new Chart(document.getElementById('topCarsChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($topCarsLabels); ?>,
            datasets: [{
                label: 'Bookings',
                data: <?php echo json_encode($topCarsData); ?>,
                backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Income Pie Chart
    new Chart(document.getElementById('incomePieChart'), {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($incomeLabels); ?>,
            datasets: [{
                data: <?php echo json_encode($incomeData); ?>,
                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Penalty Summary Chart
    new Chart(document.getElementById('penaltyChart'), {
        type: 'bar',
        data: {
            labels: <?php echo json_encode($penaltyLabels); ?>,
            datasets: [{
                label: 'Total Penalty (₱)',
                data: <?php echo json_encode($penaltyData); ?>,
                backgroundColor: ['#EF4444', '#F59E0B', '#3B82F6', '#10B981', '#8B5CF6']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '₱' + context.formattedValue;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Total Penalty (₱)'
                    }
                }
            }
        }
    });
</script>