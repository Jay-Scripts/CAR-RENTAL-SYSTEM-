<h2 class="text-2xl font-bold mb-4">Vehicle Status</h2>

<!-- Add Car Button -->
<div class="mb-4">
    <button
        onclick="openAddCarModal()"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
        + Add Car
    </button>
</div>

<!-- Car List -->
<div class="flex flex-wrap justify-center items-stretch gap-6" id="carList">
    <?php
    $stmt = $conn->prepare("SELECT * FROM CAR_DETAILS ORDER BY CREATED_AT DESC");
    $stmt->execute();
    $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($cars as $car):
        $statusClass = match ($car['STATUS']) {
            'AVAILABLE' => 'text-green-600 bg-green-100',
            'RESERVED' => 'text-yellow-600 bg-yellow-100',
            'RENTED' => 'text-blue-600 bg-blue-100',
            'MAINTENANCE' => 'text-red-600 bg-red-100',
            'SALVAGE' => 'text-gray-600 bg-gray-100',
            default => 'text-gray-600 bg-gray-100'
        };
    ?>
        <div class="car-content border-l-4 rounded-lg shadow p-4 w-72 bg-white">
            <img src="<?= $car['THUMBNAIL_PATH'] ?>" class="w-full h-40 object-contain mb-3 rounded">
            <div class="font-bold text-center mb-1"><?= $car['CAR_NAME'] ?></div>
            <div class="text-center text-gray-600 mb-2">
                • <?= $car['CAPACITY'] ?> Passenger • <?= $car['COLOR'] ?>
            </div>
            <div class="text-center text-gray-500 mb-2">
                <span class="font-medium">Plate #: </span><?= $car['PLATE_NUM'] ?>
            </div>
            <div class="text-center text-orange-500 font-semibold mb-2">
                PHP <?= number_format($car['PRICE'], 2) ?>
            </div>
            <div class="text-center">
                <span class="px-3 py-1 rounded-full text-sm font-medium <?= $statusClass ?>">
                    <?= $car['STATUS'] ?>
                </span>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add Car Modal -->
<div id="addCarModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg p-6 w-96 relative">
        <h3 class="text-xl font-bold mb-4">Add New Car</h3>
        <form id="addCarForm" enctype="multipart/form-data">
            <input type="text" name="car_name" placeholder="Car Name" required class="w-full mb-2 border p-2 rounded">
            <input type="text" name="color" placeholder="Color" required class="w-full mb-2 border p-2 rounded">
            <input type="number" name="capacity" placeholder="Capacity" required class="w-full mb-2 border p-2 rounded">
            <input type="number" step="0.01" name="price" placeholder="Price" required class="w-full mb-2 border p-2 rounded">
            <input type="text" name="plate_num" placeholder="Plate Number" required class="w-full mb-2 border p-2 rounded">
            <input type="file" name="thumbnail" accept="image/*" required class="w-full mb-4">
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeAddCarModal()" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">Cancel</button>
                <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Add Car</button>
            </div>
        </form>


    </div>
</div>

<script>
    function openAddCarModal() {
        document.getElementById('addCarModal').classList.remove('hidden');
    }

    function closeAddCarModal() {
        document.getElementById('addCarModal').classList.add('hidden');
    }

    // AJAX form submission with SweetAlert
    document.getElementById('addCarForm').addEventListener('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        fetch('../../includes/addCar.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Car Added!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
                    });
                }
            })
            .catch(err => console.error(err));
    });
</script>