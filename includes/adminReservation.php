<?php
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$totalStmt = $conn->query("SELECT COUNT(*) FROM CUSTOMER_BOOKING_DETAILS");
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

$selectBookings = "
    SELECT 
        u.FIRST_NAME, u.LAST_NAME,
        c.CAR_NAME AS car,
        DATE_FORMAT(cbd.PICKUP_DATE, '%b %d, %Y') AS pickup_date,
        DATE_FORMAT(cbd.DROP_OFF_DATE, '%b %d, %Y') AS dropoff_date,
        (DATEDIFF(cbd.DROP_OFF_DATE, cbd.PICKUP_DATE) + 1) * c.PRICE AS amount,
        cbd.STATUS AS status,
        DATE_FORMAT(cbd.CREATED_AT, '%b %d, %Y') AS created_at
    FROM CUSTOMER_BOOKING_DETAILS cbd
    JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
    JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
    ORDER BY cbd.CREATED_AT DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($selectBookings);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Mobile-first cards -->
<div class="space-y-4 md:hidden">
    <?php
    if ($rows) {
        foreach ($rows as $r) {
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
            echo "<div class='bg-white p-4 rounded-lg shadow'>
                <p class='font-semibold'>{$r['FIRST_NAME']} {$r['LAST_NAME']}</p>
                <p>{$r['car']}</p>
                <p>Pickup: {$r['pickup_date']}</p>
                <p>Dropoff: {$r['dropoff_date']}</p>
                <p>Price: ₱" . number_format($r['amount'], 2) . "</p>
                <p class='font-semibold {$statusColor}'>Status: {$r['status']}</p>
                <p class='text-gray-500 text-sm'>Booked: {$r['created_at']}</p>
            </div>";
        }
    } else {
        echo "<p class='text-center text-gray-500'>No bookings found</p>";
    }
    ?>
</div>

<!-- Table for medium+ screens -->
<div class="hidden md:block">
    <table class="w-full mt-4 text-sm bg-white rounded shadow overflow-hidden">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-3 py-2 text-left">User</th>
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
            if ($rows) {
                foreach ($rows as $r) {
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
                        <td class='px-3 py-2'>{$r['FIRST_NAME']} {$r['LAST_NAME']}</td>
                        <td class='px-3 py-2'>{$r['car']}</td>
                        <td class='px-3 py-2'>{$r['pickup_date']}</td>
                        <td class='px-3 py-2'>{$r['dropoff_date']}</td>
                        <td class='px-3 py-2'>₱" . number_format($r['amount'], 2) . "</td>
                        <td class='px-3 py-2 font-semibold {$statusColor}'>{$r['status']}</td>
                        <td class='px-3 py-2'>{$r['created_at']}</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center py-4 text-gray-500'>No bookings found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="flex justify-center mt-4 space-x-2">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Previous</a>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="?page=<?= $p ?>" class="px-3 py-1 rounded <?= $p == $page ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>">
            <?= $p ?>
        </a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Next</a>
    <?php endif; ?>
</div>