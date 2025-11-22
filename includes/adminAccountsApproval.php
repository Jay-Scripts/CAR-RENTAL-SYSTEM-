<?php
include "../../config/db.php";

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Count total inactive accounts
$totalQuery = "SELECT COUNT(*) FROM USER_ACCOUNT ua
               JOIN USER_DETAILS u ON ua.USER_ID = u.USER_ID
               WHERE ua.STATUS = 'INACTIVE'";
$totalStmt = $conn->prepare($totalQuery);
$totalStmt->execute();
$totalRows = $totalStmt->fetchColumn();
$totalPages = ceil($totalRows / $limit);

// Fetch inactive accounts
$query = "SELECT ua.ACCOUNT_ID, ua.STATUS, ua.CREATED_AT as account_created,
                 u.USER_ID, u.FIRST_NAME, u.LAST_NAME, u.EMAIL, u.PHONE, u.ADDRESS, u.GENDER, u.BIRTHDATE, u.ID_PATH
          FROM USER_ACCOUNT ua
          JOIN USER_DETAILS u ON ua.USER_ID = u.USER_ID
          WHERE ua.STATUS = 'INACTIVE'
          ORDER BY ua.CREATED_AT DESC
          LIMIT :limit OFFSET :offset";

$stmt = $conn->prepare($query);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$accounts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<section id="accountsApproval" class="animate-fadeSlide bg-gray-500/20 rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-2">Accounts Approval</h2>

    <!-- Mobile Cards -->
    <div class="space-y-4 md:hidden">
        <?php if ($accounts): ?>
            <?php foreach ($accounts as $a): ?>
                <div class="bg-white p-4 rounded-lg shadow">
                    <p class="font-semibold"><?= htmlspecialchars($a['FIRST_NAME'] . ' ' . $a['LAST_NAME']) ?></p>
                    <p>Email: <?= htmlspecialchars($a['EMAIL']) ?></p>
                    <p>Phone: <?= htmlspecialchars($a['PHONE']) ?></p>
                    <p>Gender: <?= htmlspecialchars($a['GENDER']) ?></p>
                    <p>Birthdate: <?= date('M d, Y', strtotime($a['BIRTHDATE'])) ?></p>
                    <p>Address: <?= htmlspecialchars($a['ADDRESS']) ?></p>
                    <?php if ($a['ID_PATH']): ?>
                        <button
                            class="view-id-btn px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                            data-id="<?= htmlspecialchars($a['ID_PATH']) ?>">
                            View
                        </button>
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
                <?php if ($accounts): ?>
                    <?php foreach ($accounts as $a): ?>
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
                                    <button
                                        class="view-id-btn px-2 py-1 bg-blue-500 text-white rounded hover:bg-blue-600"
                                        data-id="<?= htmlspecialchars($a['ID_PATH']) ?>">
                                        View
                                    </button>
                                <?php endif; ?>


                            </td>
                            <td class="px-3 py-2 text-red-600 font-semibold"><?= $a['STATUS'] ?></td>
                            <td class="px-3 py-2">
                                <button
                                    class="toggle-status-btn px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600"
                                    data-account="<?= $a['ACCOUNT_ID'] ?>"
                                    data-action="ACTIVE">
                                    Set Active
                                </button>
                                <button
                                    class="toggle-status-btn px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600 ml-2"
                                    data-account="<?= $a['ACCOUNT_ID'] ?>"
                                    data-action="DECLINED">
                                    Decline
                                </button>
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
        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Previous</a>
        <?php endif; ?>

        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a href="?page=<?= $p ?>" class="px-3 py-1 rounded <?= $p == $page ? 'bg-blue-500 text-white' : 'bg-gray-200 hover:bg-gray-300' ?>"><?= $p ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300">Next</a>
        <?php endif; ?>
    </div>
</section>

<!-- ID View Modal -->
<div id="idModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl p-4 max-w-md w-full relative">
        <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-800 font-bold">&times;</button>
        <img id="modalImg" src="" alt="ID Image" class="w-full rounded">
    </div>
</div>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('idModal');
        const modalImg = document.getElementById('modalImg');
        const closeModal = document.getElementById('closeModal');

        document.querySelectorAll('.view-id-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const imgPath = btn.dataset.id;
                if (!imgPath) return;

                modalImg.src = imgPath;

                // show modal
                modal.classList.remove('hidden');
                modal.classList.add('flex', 'items-center', 'justify-center');
            });
        });

        closeModal.addEventListener('click', () => {
            modal.classList.add('hidden'); // hide modal
            modal.classList.remove('flex', 'items-center', 'justify-center');
            modalImg.src = '';
        });

        // Close modal on click outside the image
        modal.addEventListener('click', e => {
            if (e.target === modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'items-center', 'justify-center');
                modalImg.src = '';
            }
        });
    });

    document.querySelectorAll('.toggle-status-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const accountId = this.dataset.account;
            const action = this.dataset.action;
            const actionText = action === 'ACTIVE' ? 'activate' : 'decline';

            Swal.fire({
                title: `Are you sure?`,
                text: `You are about to ${actionText} this account.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: action === 'ACTIVE' ? '#16a34a' : '#dc2626',
                cancelButtonColor: '#6b7280',
                confirmButtonText: `Yes, ${actionText}`
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send POST request via fetch
                    fetch('../../includes/adminAccountsApprovalActivateAccount.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `account_id=${accountId}&status_action=${action}`
                        })
                        .then(res => res.text())
                        .then(data => {
                            if (data.trim() === "success") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Updated!',
                                    text: `Account has been ${actionText}.`,
                                    timer: 1500,
                                    showConfirmButton: false
                                }).then(() => location.reload());
                            } else {
                                Swal.fire('Error', data, 'error');
                            }
                        })
                        .catch(err => {
                            Swal.fire('Error', 'Something went wrong', 'error');
                        });

                }
            });
        });
    });
</script>

<script>

</script>