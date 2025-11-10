<?php
include "../../config/db.php";

// 1️ Get search term
$search = $_GET['search'] ?? '';

// 2️ Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// 3️ Count total rows (with optional search)
$totalQuery = "
    SELECT COUNT(*) 
    FROM CUSTOMER_BOOKING_DETAILS cbd
    JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
    JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
    WHERE CONCAT(u.FIRST_NAME, ' ', u.LAST_NAME) LIKE :search
       OR c.CAR_NAME LIKE :search
";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->execute([':search' => "%$search%"]);
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// 4️ Fetch bookings
$selectBookings = "
    SELECT 
        cbd.BOOKING_ID,
        u.USER_ID, u.FIRST_NAME, u.LAST_NAME, u.ID_PATH,
        c.CAR_NAME AS car,
        cbd.TRIP_DETAILS,
        DATE_FORMAT(cbd.PICKUP_DATE, '%b %d, %Y') AS pickup_date,
        DATE_FORMAT(cbd.DROP_OFF_DATE, '%b %d, %Y') AS dropoff_date,
        cbd.TOTAL_COST AS amount,
        cbd.STATUS AS status,
        DATE_FORMAT(cbd.CREATED_AT, '%b %d, %Y') AS created_at,
        bpd.RECEIPT_PATH AS receipt_path
    FROM CUSTOMER_BOOKING_DETAILS cbd
    JOIN CAR_DETAILS c ON cbd.CAR_ID = c.CAR_ID
    JOIN USER_DETAILS u ON cbd.USER_ID = u.USER_ID
    LEFT JOIN BOOKING_PAYMENT_DETAILS bpd ON cbd.BOOKING_ID = bpd.BOOKING_ID
    WHERE CONCAT(u.FIRST_NAME, ' ', u.LAST_NAME) LIKE :search
       OR c.CAR_NAME LIKE :search
    ORDER BY cbd.CREATED_AT DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $conn->prepare($selectBookings);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Search bar -->
<form method="get" class="mb-4 flex space-x-2">
    <input type="text" name="search" placeholder="Search by user or car"
        value="<?= htmlspecialchars($search) ?>"
        class="border rounded px-3 py-2 flex-1">
    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">Search</button>
</form>

<!-- Mobile Cards -->
<div class="space-y-4 md:hidden">
    <?php if ($rows): ?>
        <?php foreach ($rows as $r):
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
            $idPath = $r['ID_PATH'] ?? '';
            $receiptPath = $r['receipt_path'] ?? '';

        ?>
            <div class="bg-white p-4 rounded-lg shadow">
                <p class="font-semibold"><?= htmlspecialchars($r['FIRST_NAME'] . ' ' . $r['LAST_NAME']) ?></p>
                <p>Car: <?= htmlspecialchars($r['car']) ?></p>
                <p>Trip Details: <?= htmlspecialchars($r['TRIP_DETAILS']) ?></p>
                <p>Pickup: <?= $r['pickup_date'] ?></p>
                <p>Dropoff: <?= $r['dropoff_date'] ?></p>
                <p>Price: ₱<?= number_format($r['amount'], 2) ?></p>
                <p class="font-semibold <?= $statusColor ?>">Status: <?= $r['status'] ?></p>
                <p class="text-gray-500 text-sm">Booked: <?= $r['created_at'] ?></p>

                <?php if ($idPath): ?>
                    <button class="res-view-id-btn px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600" data-id="<?= htmlspecialchars($idPath) ?>">View ID</button>
                <?php endif; ?>

                <?php if ($receiptPath): ?>
                    <button class="res-view-payment-btn px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600" data-id="<?= htmlspecialchars($receiptPath) ?>">View ePayment</button>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-gray-500">No bookings found</p>
    <?php endif; ?>
</div>

<!-- Table md+ -->
<div class="hidden md:block">
    <table class="w-full mt-4 text-sm bg-white rounded shadow overflow-hidden">
        <thead class="bg-gray-200">
            <tr>
                <th class="px-3 py-2">User</th>
                <th class="px-3 py-2">Car</th>
                <th class="px-3 py-2">Trip Details</th>
                <th class="px-3 py-2">Pickup Date</th>
                <th class="px-3 py-2">Dropoff Date</th>
                <th class="px-3 py-2">Price</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2">Date Booked</th>
                <th class="px-3 py-2">ID</th>
                <th class="px-3 py-2">ePayment</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($rows): ?>
                <?php foreach ($rows as $r):
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
                    $idPath = $r['ID_PATH'] ? "/uploads/ids/" . $r['ID_PATH'] : '';
                    $receiptPath = $r['receipt_path'] ? "/uploads/receipts/" . $r['receipt_path'] : '';


                ?>
                    <tr class="border-b">
                        <td class="px-3 py-2"><?= htmlspecialchars($r['FIRST_NAME'] . ' ' . $r['LAST_NAME']) ?></td>
                        <td class="px-3 py-2"><?= htmlspecialchars($r['car']) ?></td>
                        <td class="px-3 py-2"><?= htmlspecialchars($r['TRIP_DETAILS']) ?></td>
                        <td class="px-3 py-2"><?= $r['pickup_date'] ?></td>
                        <td class="px-3 py-2"><?= $r['dropoff_date'] ?></td>
                        <td class="px-3 py-2">₱<?= number_format($r['amount'], 2) ?></td>
                        <td class="px-3 py-2 font-semibold <?= $statusColor ?>"><?= $r['status'] ?></td>
                        <td class="px-3 py-2"><?= $r['created_at'] ?></td>
                        <td class="px-3 py-2">
                            <?php if ($idPath): ?>
                                <button class="res-view-id-btn px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600" data-id="<?= htmlspecialchars($idPath) ?>">View</button>
                            <?php endif; ?>
                        </td>
                        <td class="px-3 py-2">
                            <?php if ($receiptPath): ?>
                                <button class="res-view-payment-btn px-2 py-1 bg-green-500 text-white rounded hover:bg-green-600" data-id="<?= htmlspecialchars($receiptPath) ?>">View</button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center py-4 text-gray-500">No bookings found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="flex justify-center mt-4 space-x-2">
    <?php if ($page > 1): ?>
        <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Previous</a>
    <?php endif; ?>

    <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="?page=<?= $p ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 rounded <?= $p == $page ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>"><?= $p ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Next</a>
    <?php endif; ?>
</div>

<!-- ID Modal -->
<div id="res_idModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-4 max-w-md w-full relative">
        <button id="res_closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 font-bold">&times;</button>
        <img id="res_modalImg" src="" alt="ID Image" class="w-full rounded">
    </div>
</div>

<!-- Payment Modal -->
<div id="res_paymentModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-4 max-w-md w-full relative">
        <button id="res_closePaymentModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 font-bold">&times;</button>
        <img id="res_paymentImg" src="" alt="Receipt" class="w-full rounded">
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // ID modal
        const idModal = document.getElementById('res_idModal');
        const idImg = document.getElementById('res_modalImg');
        const closeId = document.getElementById('res_closeModal');

        document.querySelectorAll('.res-view-id-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                idImg.src = btn.dataset.id;
                idModal.classList.remove('hidden');
                idModal.classList.add('flex', 'items-center', 'justify-center');
            });
        });

        closeId.addEventListener('click', () => {
            idModal.classList.add('hidden');
            idModal.classList.remove('flex', 'items-center', 'justify-center');
            idImg.src = '';
        });

        idModal.addEventListener('click', e => {
            if (e.target === idModal) {
                idModal.classList.add('hidden');
                idModal.classList.remove('flex', 'items-center', 'justify-center');
                idImg.src = '';
            }
        });

        // Payment modal
        const paymentModal = document.getElementById('res_paymentModal');
        const paymentImg = document.getElementById('res_paymentImg');
        const closePayment = document.getElementById('res_closePaymentModal');

        document.querySelectorAll('.res-view-payment-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                paymentImg.src = btn.dataset.id;
                paymentModal.classList.remove('hidden');
                paymentModal.classList.add('flex', 'items-center', 'justify-center');
            });
        });

        closePayment.addEventListener('click', () => {
            paymentModal.classList.add('hidden');
            paymentModal.classList.remove('flex', 'items-center', 'justify-center');
            paymentImg.src = '';
        });

        paymentModal.addEventListener('click', e => {
            if (e.target === paymentModal) {
                paymentModal.classList.add('hidden');
                paymentModal.classList.remove('flex', 'items-center', 'justify-center');
                paymentImg.src = '';
            }
        });
    });
</script>