<style>
    .car-option {
        transition: all 0.3s ease;
    }

    .car-option input[type="radio"] {
        display: none;
    }

    .car-option input[type="radio"]:checked+.car-content {
        background-color: gray;
        color: white;
        transform: scale(1.02);
    }

    .car-option input[type="radio"]:checked+.car-content .text-orange-500 {
        color: #fbbf24 !important;
    }
</style><!-- Updated HTML structure -->
<div>
    <label class="block mb-2 text-sm font-medium text-gray-700">Select Your Car</label>
    <div class="flex flex-wrap justify-center items-center gap-4">
        <?php
        $stmt = $conn->prepare("SELECT * FROM CAR_DETAILS WHERE STATUS = 'AVAILABLE'");
        $stmt->execute();
        $cars = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($cars as $car):
        ?>
            <label class="car-option cursor-pointer">
                <input type="radio" name="carId" value="<?= $car['CAR_ID'] ?>" required>
                <div class="car-content border rounded p-4 w-100 hover:shadow-md">
                    <img src="<?= $car['THUMBNAIL_PATH'] ?>" class="w-full h-40 object-contain mb-2">
                    <div class="text-center font-bold"><?= $car['CAR_NAME'] ?></div>
                    <div class="text-center">• <?= $car['CAPACITY'] ?> Passenger • <?= $car['COLOR'] ?></div>
                    <div class="text-center text-orange-500 font-semibold">PHP <?= number_format($car['PRICE'], 2) ?></div>
                </div>
            </label>
        <?php endforeach; ?>
    </div>
</div>