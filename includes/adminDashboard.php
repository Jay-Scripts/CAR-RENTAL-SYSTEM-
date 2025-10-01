<?php

// 1. Active Reservations
$select_query_active_reservation = "
    SELECT COUNT(*) AS active_count
    FROM CUSTOMER_BOOKING_DETAILS
    WHERE STATUS NOT IN ('COMPLETED', 'CANCELED')
";
$active_count = $conn->query($select_query_active_reservation)->fetchColumn();

// 2. Vehicles Available
$select_query_active_vehicles = "
    SELECT COUNT(*) AS available_count
    FROM CAR_DETAILS
    WHERE STATUS = 'AVAILABLE'
";
$available_count = $conn->query($select_query_active_vehicles)->fetchColumn();

// 3. Payments This Month
$select_query_total_sale_this_month = "
    SELECT COALESCE(SUM(
        (DATEDIFF(cbd.DROP_OFF_DATE, cbd.PICKUP_DATE) + 1) * c.PRICE
    ), 0) AS total_income
    FROM CUSTOMER_BOOKING_DETAILS cbd
    JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
    JOIN BOOKING_PAYMENT_DETAILS bpd ON cbd.BOOKING_ID = bpd.BOOKING_ID
    WHERE cbd.STATUS = 'COMPLETED'
      AND bpd.PAYMENT_TYPE = 'BOOKING FEE'
      AND MONTH(cbd.CREATED_AT) = MONTH(CURRENT_DATE())
      AND YEAR(cbd.CREATED_AT) = YEAR(CURRENT_DATE())
";

$total_income = $conn->query($select_query_total_sale_this_month)->fetchColumn();
?>
<h2 class="text-2xl font-bold mb-2">Car Rental Dashboard</h2>
<p class="text-gray-600 mb-6">Overview of system activity</p>

<!-- KPI Cards -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <!-- Active Reservations -->
    <div class="car-content bg-white rounded-xl shadow-md p-5 flex flex-col justify-between hover:shadow-lg transition border-l-4 border-[#df927f]">
        <div class="flex items-center space-x-3">
            <!-- Calendar Icon -->
            <svg class="w-8 h-8 text-[#df927f]" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 
            00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <div>
                <p class="text-gray-500 text-sm">Active Reservations</p>
                <p class="text-3xl font-bold text-[#df927f] mt-1"><?php echo $active_count; ?></p>
            </div>
        </div>
    </div>


    <!-- Vehicles Available -->
    <div class="car-content bg-white rounded-xl shadow-md p-5 flex flex-col justify-between hover:shadow-lg transition border-l-4 border-green-600">
        <div class="flex items-center space-x-3">
            <!-- Car Icon -->
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 13l2-5a2 2 0 011.9-1.4h10.2a2 2 0 
            011.9 1.4l2 5M5 13h14m-9 4h4m-9 0h.01M19 17h.01" />
            </svg>
            <div>
                <p class="text-gray-500 text-sm">Vehicles Available</p>
                <p class="text-3xl font-bold text-green-600 mt-1"><?php echo $available_count; ?></p>
            </div>
        </div>
    </div>


    <!-- Payments This Month -->
    <div class="car-content bg-white rounded-xl shadow-md p-5 flex flex-col justify-between hover:shadow-lg transition border-l-4 border-blue-600">
        <div class="flex items-center space-x-3">
            <!-- Wallet Icon -->
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 
            00-2 2v10a2 2 0 002 2h10a2 2 0 
            002-2v-2m0-4h4m-4 0a2 2 0 
            110-4 2 2 0 010 4z" />
            </svg>
            <div>
                <p class="text-gray-500 text-sm">Payments This Month</p>
                <p class="text-3xl font-bold text-blue-600 mt-1">₱<?php echo number_format($total_income, 2); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
    <div class="car-content bg-white p-4 rounded-lg shadow col-span-2">
        <h3 class="text-lg font-semibold mb-2">Reservation Trends</h3>
        <canvas id="reservationChart" height="150"></canvas>
    </div>

    <div class="car-content bg-white p-4 rounded-lg shadow col-span-2">
        <h3 class="text-lg font-semibold mb-2">Top Rented Cars</h3>
        <canvas id="topCarsChart" height="150"></canvas>
    </div>

    <div class="car-content bg-white p-4 rounded-lg shadow col-span-2">
        <h3 class="text-lg font-semibold mb-2">Total Income by Car</h3>
        <canvas id="incomePieChart" height="130"></canvas>
    </div>
</div>


<script>
    // Example Data (replace with PHP echo from DB later)
    const reservationData = [5, 8, 12, 9, 15, 20, 18];
    const reservationLabels = ["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"];

    const topCarsData = [25, 18, 12, 10, 8];
    const topCarsLabels = ["Vios 2022", "Innova 2022", "Xpander 2023", "Vios 2019", "Xpander 2022"];

    const incomeData = [120000, 90000, 60000, 45000, 30000]; // per car total income
    const incomeLabels = ["Vios 2022", "Innova 2022", "Xpander 2023", "Vios 2019", "Xpander 2022"];

    // Reservation Chart
    new Chart(document.getElementById('reservationChart'), {
        type: 'line',
        data: {
            labels: reservationLabels,
            datasets: [{
                label: 'Bookings',
                data: reservationData,
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
            labels: topCarsLabels,
            datasets: [{
                label: 'Bookings',
                data: topCarsData,
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
            labels: incomeLabels,
            datasets: [{
                data: incomeData,
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
</script>