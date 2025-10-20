<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['car_id'], $_POST['action'])) {
    $carId = intval($_POST['car_id']);
    $action = $_POST['action'];

    if ($carId > 0) {
        try {
            $newStatus = ($action === 'salvage') ? 'SALVAGE' : 'AVAILABLE';

            $stmt = $conn->prepare("UPDATE CAR_DETAILS SET STATUS = ? WHERE CAR_ID = ?");
            $stmt->execute([$newStatus, $carId]);

            $message = "Vehicle status updated to {$newStatus}.";
            $msgType = "success";
        } catch (Exception $e) {
            $message = "Error: " . $e->getMessage();
            $msgType = "error";
        }
    } else {
        $message = "Invalid Car ID.";
        $msgType = "error";
    }
}

// Fetch only cars currently under maintenance
try {
    $sql = "
        SELECT 
            CAR_ID,
            CAR_NAME,
            COLOR,
            STATUS,
            THUMBNAIL_PATH
        FROM CAR_DETAILS
        WHERE STATUS = 'MAINTENANCE'
        ORDER BY CAR_NAME ASC
    ";
    $stmt = $conn->query($sql);
    $vehicles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $vehicles = [];
}
?>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<h2 class="text-2xl font-bold mb-2">Turnover Vehicle</h2>
<p class="text-gray-600 mb-4">Track vehicles under maintenance.</p>

<div id="maintenanceList" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php if (count($vehicles) === 0): ?>
        <p class="text-gray-500 col-span-full">No vehicles under maintenance.</p>
    <?php else: ?>
        <?php foreach ($vehicles as $v): ?>
            <div class="bg-white rounded-lg shadow-md overflow-hidden flex flex-col">

                <!-- Car Image -->
                <?php if (!empty($v['THUMBNAIL_PATH'])): ?>
                    <img src="<?= htmlspecialchars($v['THUMBNAIL_PATH']) ?>"
                        alt="Car Image"
                        class="w-full p-5 object-cover">
                <?php else: ?>
                    <div class="w-full h-40 flex items-center justify-center bg-gray-200 text-gray-500">
                        No Image
                    </div>
                <?php endif; ?>

                <!-- Vehicle Info -->
                <div class="p-4 flex flex-col flex-1">
                    <p class="mb-1"><span class="font-semibold">Vehicle:</span>
                        <?= htmlspecialchars($v['CAR_NAME']) ?> (<?= htmlspecialchars($v['COLOR']) ?>)
                    </p>
                    <p class="mb-2"><span class="font-semibold">Status:</span> <?= htmlspecialchars($v['STATUS']) ?></p>

                    <div class="mt-auto">
                        <form method="POST" class="space-y-2">
                            <input type="hidden" name="car_id" value="<?= $v['CAR_ID'] ?>">

                            <!-- Done Maintenance -->
                            <button type="submit" name="action" value="available"
                                class="w-full bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm">
                                Done Maintenance
                            </button>

                            <!-- Mark as Salvage -->
                            <button type="submit" name="action" value="salvage"
                                class="w-full bg-red-500 hover:bg-red-600 text-white px-3 py-2 rounded text-sm">
                                Mark as Salvage
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php if (!empty($message)): ?>
    <script>
        Swal.fire({
            icon: '<?= $msgType ?>',
            title: '<?= $msgType === "success" ? "Success" : "Error" ?>',
            text: '<?= $message ?>',
            confirmButtonColor: '#16a34a'
        }).then(() => {
            <?php if ($msgType === "success"): ?>
                location.href = window.location.href;
            <?php endif; ?>
        });
    </script>
<?php endif; ?>