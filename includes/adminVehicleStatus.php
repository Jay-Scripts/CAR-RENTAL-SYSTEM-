    <h2 class="text-2xl font-bold mb-4">Vehicle Status</h2>

    <div class="flex flex-wrap justify-center items-stretch gap-6">
        <?php
        $stmt = $conn->prepare("SELECT * FROM CAR_DETAILS");
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