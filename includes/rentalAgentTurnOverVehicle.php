<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['car_id'])) {
    $carId = intval($_POST['car_id']);
    if ($carId > 0) {
        try {
            $stmt = $conn->prepare("UPDATE CAR_DETAILS SET STATUS = 'AVAILABLE' WHERE CAR_ID = ?");
            $stmt->execute([$carId]);
            $message = "Vehicle status updated to AVAILABLE.";
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

//  Fetch only cars currently under maintenance
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


<!--  SweetAlert2 CDN -->
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
                        <!--  Form Button -->
                        <form method="POST">
                            <input type="hidden" name="car_id" value="<?= $v['CAR_ID'] ?>">
                            <button type="submit"
                                class="w-full bg-green-500 hover:bg-green-600 text-white px-3 py-2 rounded text-sm">
                                Done Maintenance
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
            // Reload page after success to update list
            <?php if ($msgType === "success"): ?>
                location.href = window.location.href;
            <?php endif; ?>
        });
    </script>
<?php endif; ?>