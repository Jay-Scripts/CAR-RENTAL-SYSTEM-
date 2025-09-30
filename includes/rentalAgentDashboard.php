<?php
// For Pickup count
$pickup_count = $conn->query("
    SELECT COUNT(*) FROM CUSTOMER_BOOKING_DETAILS
    WHERE STATUS = 'FOR PICKUP'
")->fetchColumn();

// Ongoing count
$ongoing_count = $conn->query("
    SELECT COUNT(*) FROM CUSTOMER_BOOKING_DETAILS
    WHERE STATUS = 'ONGOING'
")->fetchColumn();

// Checking count
$checking_count = $conn->query("
    SELECT COUNT(*) FROM CUSTOMER_BOOKING_DETAILS
    WHERE STATUS = 'CHECKING'
")->fetchColumn();
?>

<h2 class="text-2xl font-bold mb-2">Dashboard</h2>
<p class="text-gray-600">Quick overview of rentals and system status.</p>

<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-4">

    <!-- For Pickup -->
    <div class="car-content bg-white rounded-xl shadow-md p-5 flex flex-col justify-between hover:shadow-lg transition border-l-4 border-yellow-500">
        <div class="flex items-center space-x-3">
            <!-- Truck Icon -->
            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M3 13h2l1-4h13l1 4h2M5 13v4a2 2 0 002 2h10a2 2 0 002-2v-4" />
            </svg>
            <div>
                <p class="text-gray-500 text-sm">For Pickup</p>
                <p class="text-3xl font-bold text-yellow-500 mt-1"><?php echo $pickup_count; ?></p>
            </div>
        </div>
    </div>

    <!-- Ongoing -->
    <div class="car-content bg-white rounded-xl shadow-md p-5 flex flex-col justify-between hover:shadow-lg transition border-l-4 border-green-600">
        <div class="flex items-center space-x-3">
            <!-- Clock Icon -->
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-gray-500 text-sm">Ongoing</p>
                <p class="text-3xl font-bold text-green-600 mt-1"><?php echo $ongoing_count; ?></p>
            </div>
        </div>
    </div>

    <!-- Checking -->
    <div class="car-content bg-white rounded-xl shadow-md p-5 flex flex-col justify-between hover:shadow-lg transition border-l-4 border-blue-600">
        <div class="flex items-center space-x-3">
            <!-- Search Icon -->
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
            </svg>
            <div>
                <p class="text-gray-500 text-sm">For Inspection</p>
                <p class="text-3xl font-bold text-blue-600 mt-1"><?php echo $checking_count; ?></p>
            </div>
        </div>
    </div>

</div>