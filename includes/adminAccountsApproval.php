<?php
include "../../config/db.php";

// Get search term
$apprAC_search = trim($_GET['search'] ?? '');

// Pagination
$apprAC_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$apprAC_limit = 10;
$apprAC_offset = ($apprAC_page - 1) * $apprAC_limit;

// Count total inactive accounts with search
$apprAC_totalQuery = "SELECT COUNT(*) FROM USER_ACCOUNT ua
                      JOIN USER_DETAILS u ON ua.USER_ID = u.USER_ID
                      WHERE ua.STATUS = 'INACTIVE'
                      AND (u.FIRST_NAME LIKE :search OR u.LAST_NAME LIKE :search OR u.EMAIL LIKE :search OR u.PHONE LIKE :search)";
$apprAC_totalStmt = $conn->prepare($apprAC_totalQuery);
$apprAC_totalStmt->execute([':search' => "%$apprAC_search%"]);
$apprAC_totalRows = $apprAC_totalStmt->fetchColumn();
$apprAC_totalPages = ceil($apprAC_totalRows / $apprAC_limit);

// Fetch inactive accounts with search
$apprAC_query = "SELECT ua.ACCOUNT_ID, ua.STATUS, ua.CREATED_AT as account_created,
                        u.USER_ID, u.FIRST_NAME, u.LAST_NAME, u.EMAIL, u.PHONE, u.ADDRESS, u.GENDER, u.BIRTHDATE, u.ID_PATH
                 FROM USER_ACCOUNT ua
                 JOIN USER_DETAILS u ON ua.USER_ID = u.USER_ID
                 WHERE ua.STATUS = 'INACTIVE'
                 AND (u.FIRST_NAME LIKE :search OR u.LAST_NAME LIKE :search OR u.EMAIL LIKE :search OR u.PHONE LIKE :search)
                 ORDER BY ua.CREATED_AT DESC
                 LIMIT :limit OFFSET :offset";

$apprAC_stmt = $conn->prepare($apprAC_query);
$apprAC_stmt->bindValue(':search', "%$apprAC_search%", PDO::PARAM_STR);
$apprAC_stmt->bindValue(':limit', $apprAC_limit, PDO::PARAM_INT);
$apprAC_stmt->bindValue(':offset', $apprAC_offset, PDO::PARAM_INT);
$apprAC_stmt->execute();
$apprAC_accounts = $apprAC_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="accountsApproval" class="animate-fadeSlide bg-gray-500/20 rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-2">Accounts Approval</h2>

    <!-- Search Bar -->
    <form method="get" class="mb-4">
        <input type="text" name="search" placeholder="Search by name, email, or phone" value="<?= htmlspecialchars($apprAC_search) ?>"
            class="w-full md:w-1/2 px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring focus:border-blue-300">
        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 mt-2 md:mt-0">Search</button>
    </form>

    <!-- Mobile Cards -->
    <div class="space-y-4 md:hidden">
        <?php if ($apprAC_accounts): ?>
            <?php foreach ($apprAC_accounts as $a): ?>
                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="font-semibold"><?= htmlspecialchars($a['FIRST_NAME'] . ' ' . $a['LAST_NAME']) ?></p>
                    <p>Email: <?= htmlspecialchars($a['EMAIL']) ?></p>
                    <p>Phone: <?= htmlspecialchars($a['PHONE']) ?></p>
                    <p>Gender: <?= htmlspecialchars($a['GENDER']) ?></p>
                    <p>Birthdate: <?= date('M d, Y', strtotime($a['BIRTHDATE'])) ?></p>
                    <p>Address: <?= htmlspecialchars($a['ADDRESS']) ?></p>
                    <?php if ($a['ID_PATH']): ?>
                        <button class="view-id-btn px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                            data-id="<?= htmlspecialchars($a['ID_PATH']) ?>">View</button>
                    <?php endif; ?>
                    <p>Status: <span class="text-red-600 font-semibold"><?= $a['STATUS'] ?></span></p>
                    <p>Account Created: <?= date('M d, Y', strtotime($a['account_created'])) ?></p>
                    <div class="mt-2">
                        <form method="post" action="toggleAccountStatus.php">
                            <input type="hidden" name="account_id" value="<?= $a['ACCOUNT_ID'] ?>">
                            <button type="submit" name="status_action" value="ACTIVE" class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">Set Active</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-gray-500">No inactive accounts found</p>
        <?php endif; ?>
    </div>

    <!-- Table for md+ screens -->
    <div class="hidden md:block">
        <table class="w-full mt-4 text-sm bg-white rounded shadow overflow-hidden">
            <thead class="bg-gray-200">
                <tr>
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">User</th>
                    <th class="px-3 py-2">Email</th>
                    <th class="px-3 py-2">Phone</th>
                    <th class="px-3 py-2">Gender</th>
                    <th class="px-3 py-2">Birthdate</th>
                    <th class="px-3 py-2">Address</th>
                    <th class="px-3 py-2">ID</th>
                    <th class="px-3 py-2">Status</th>
                    <th class="px-3 py-2">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($apprAC_accounts): ?>
                    <?php foreach ($apprAC_accounts as $a): ?>
                        <tr class="border-b">
                            <td class="px-3 py-2"><?= $a['ACCOUNT_ID'] ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($a['FIRST_NAME'] . ' ' . $a['LAST_NAME']) ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($a['EMAIL']) ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($a['PHONE']) ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($a['GENDER']) ?></td>
                            <td class="px-3 py-2"><?= date('M d, Y', strtotime($a['BIRTHDATE'])) ?></td>
                            <td class="px-3 py-2"><?= htmlspecialchars($a['ADDRESS']) ?></td>
                            <td class="px-3 py-2">
                                <?php if ($a['ID_PATH']): ?>
                                    <button class="view-id-btn px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                                        data-id="<?= htmlspecialchars($a['ID_PATH']) ?>">View</button>
                                <?php endif; ?>
                            </td>
                            <td class="px-3 py-2 text-red-600 font-semibold"><?= $a['STATUS'] ?></td>
                            <td class="px-3 py-2">
                                <button class="toggle-status-btn px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600"
                                    data-account="<?= $a['ACCOUNT_ID'] ?>" data-action="ACTIVE">Set Active</button>
                                <button class="toggle-status-btn px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 ml-2"
                                    data-account="<?= $a['ACCOUNT_ID'] ?>" data-action="DECLINED">Decline</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center py-4 text-gray-500">No inactive accounts found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-4 space-x-2">
        <?php if ($apprAC_page > 1): ?>
            <a href="?page=<?= $apprAC_page - 1 ?>&search=<?= urlencode($apprAC_search) ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Previous</a>
        <?php endif; ?>
        <?php for ($p = 1; $p <= $apprAC_totalPages; $p++): ?>
            <a href="?page=<?= $p ?>&search=<?= urlencode($apprAC_search) ?>" class="px-3 py-1 rounded <?= $p == $apprAC_page ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>"><?= $p ?></a>
        <?php endfor; ?>
        <?php if ($apprAC_page < $apprAC_totalPages): ?>
            <a href="?page=<?= $apprAC_page + 1 ?>&search=<?= urlencode($apprAC_search) ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Next</a>
        <?php endif; ?>
    </div>
</section>