<?php
include "../../config/db.php";

$booking_input_message = [
    "car_id" => "",
    "pickup_date" => "",
    "dropoff_date" => "",
    "trip_details" => "",
];

$sanitized_car_id =
    $sanitized_pickup_date =
    $sanitized_dropoff_date =
    $sanitized_trip_details =
    $summary_preview =
    $booking_message = "";

// Convert text date into MySQL format
function normalizeDate($dateInput)
{
    $formats = ['Y-m-d', 'm/d/Y', 'd-m-Y', 'd/m/Y'];
    foreach ($formats as $format) {
        $d = DateTime::createFromFormat($format, $dateInput);
        if ($d && $d->format($format) === $dateInput) {
            return $d->format('Y-m-d');
        }
    }
    return null;
}

// STEP 1: User clicked "Book Now"
if (isset($_POST['customer_booking']) && !isset($_POST['confirm_booking'])) {
    $car_id       = trim($_POST['car_id'] ?? '');
    $pickup_date  = trim($_POST['pickup_date'] ?? '');
    $dropoff_date = trim($_POST['dropoff_date'] ?? '');
    $trip_details = trim($_POST['trip_details'] ?? '');

    $sanitized_car_id       = filter_var($car_id, FILTER_SANITIZE_NUMBER_INT);
    $sanitized_pickup_date  = normalizeDate($pickup_date);
    $sanitized_dropoff_date = normalizeDate($dropoff_date);
    $sanitized_trip_details = htmlspecialchars($trip_details, ENT_QUOTES, 'UTF-8');

    // Validation
    if (empty($sanitized_trip_details)) {
        $booking_input_message['trip_details'] = "<p class='text-red-500 text-sm'>Please fill this field.</p>";
    }
    if (empty($sanitized_car_id)) {
        $booking_input_message['car_id'] = "<p class='text-red-500 text-sm'>Please select a car.</p>";
    }
    if (empty($sanitized_pickup_date)) {
        $booking_input_message['pickup_date'] = "<p class='text-red-500 text-sm'>Pickup date is required.</p>";
    }
    if (empty($sanitized_dropoff_date)) {
        $booking_input_message['dropoff_date'] = "<p class='text-red-500 text-sm'>Drop-off date is required.</p>";
    } elseif (!empty($sanitized_pickup_date) && strtotime($sanitized_dropoff_date) < strtotime($sanitized_pickup_date)) {
        $booking_input_message['dropoff_date'] = "<p class='text-red-500 text-sm'>Drop-off date cannot be earlier than pickup date.</p>";
    }

    // ✅ Add this block here
    if (!empty($sanitized_pickup_date)) {
        $today = strtotime(date('Y-m-d'));
        $pickup = strtotime($sanitized_pickup_date);
        $max_allowed = strtotime('+7 days', $today);

        if ($pickup < $today) {
            $booking_input_message['pickup_date'] = "<p class='text-red-500 text-sm'>Pickup date cannot be in the past.</p>";
        } elseif ($pickup > $max_allowed) {
            $booking_input_message['pickup_date'] = "<p class='text-red-500 text-sm'>Pickup date can only be up to 7 days from today.</p>";
        }
    }

    if (empty($sanitized_dropoff_date)) {
        $booking_input_message['dropoff_date'] = "<p class='text-red-500 text-sm'>Drop-off date is required.</p>";
    } elseif (!empty($sanitized_pickup_date) && strtotime($sanitized_dropoff_date) < strtotime($sanitized_pickup_date)) {
        $booking_input_message['dropoff_date'] = "<p class='text-red-500 text-sm'>Drop-off date cannot be earlier than pickup date.</p>";
    }

    $hasErrorsBooking = array_filter($booking_input_message);

    if (!$hasErrorsBooking) {
        try {
            $pickup  = new DateTime($sanitized_pickup_date);
            $dropoff = new DateTime($sanitized_dropoff_date);
            $interval = $pickup->diff($dropoff);

            $total_days = $interval->days ?: 1;

            // Fetch car details
            $stmt = $conn->prepare("SELECT CAR_NAME, COLOR, CAPACITY, PRICE, THUMBNAIL_PATH 
                                    FROM CAR_DETAILS WHERE CAR_ID = :car_id LIMIT 1");
            $stmt->execute([':car_id' => $sanitized_car_id]);
            $carRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$carRow) throw new Exception("Car not found.");

            $car_price  = (float)$carRow['PRICE'];
            $total_cost = $total_days * $car_price;

            // STEP 2: Show confirmation modal
            $summary_preview = "
<script>
Swal.fire({
  title: 'Confirm Your Reservation',
  html: `
    <div style='text-align:left; font-family:Arial; line-height:1.5;'>
      <div style='margin-bottom:15px;'>
        <img src=\"{$carRow['THUMBNAIL_PATH']}\" style='max-width:250px; height:auto; border-radius:8px;'>
        <h3 style='margin:10px 0 5px; font-size:16px;'>Car Model : {$carRow['CAR_NAME']}</h3>
        <p style='margin:0; font-size:14px; color:#444;'>
         • Color: {$carRow['COLOR']} <br> • Capacity: {$carRow['CAPACITY']} Passengers
        </p>
      </div>
      <p><b>Pick-up:</b> {$sanitized_pickup_date}</p>
      <p><b>Drop-off:</b> {$sanitized_dropoff_date}</p>
      <table style='width:100%; border-collapse:collapse; font-size:14px;'>
        <tr><td>Cost of vehicle</td><td style='text-align:right;'>₱ " . number_format($car_price, 2) . "</td></tr>
        <tr><td>Number of days</td><td style='text-align:right;'>{$total_days}</td></tr>
        <br>
        <tr style='font-weight:bold; border-top:1px solid #ccc;'>
          <td>Total Cost</td><td style='text-align:right;'>₱ " . number_format($total_cost, 2) . "</td></tr>
      </table>
    </div>
  `,
  width: 600,
  showCancelButton: true,
  confirmButtonText: 'Confirm Booking',
  cancelButtonText: 'Cancel'
}).then((result) => {
  if (result.isConfirmed) {
    // Submit the form again with hidden confirm_booking
    let form = document.createElement('form');
    form.method = 'POST';
    form.action = '';
    form.innerHTML = `
      <input type='hidden' name='confirm_booking' value='1'>
      <input type='hidden' name='car_id' value='{$sanitized_car_id}'>
      <input type='hidden' name='pickup_date' value='{$sanitized_pickup_date}'>
      <input type='hidden' name='dropoff_date' value='{$sanitized_dropoff_date}'>
      <input type='hidden' name='trip_details' value='{$sanitized_trip_details}'>
    `;
    document.body.appendChild(form);
    form.submit();
  }
});
</script>";
        } catch (Exception $e) {
            $booking_message = "<script>Swal.fire({icon:'error',title:'System error',text:'" . addslashes($e->getMessage()) . "'});</script>";
        }
    } else {
        $booking_message = "<script>Swal.fire({icon:'error',title:'Forgotten input fields',text:'Please correct the highlighted fields.'});</script>";
    }
}

// STEP 3: User confirmed → commit transaction
if (isset($_POST['confirm_booking'])) {
    try {
        $conn->beginTransaction();

        $car_id       = $_POST['car_id'];
        $pickup_date  = $_POST['pickup_date'];
        $dropoff_date = $_POST['dropoff_date'];
        $trip_details = $_POST['trip_details'];

        // Recalculate cost for safety
        $stmt = $conn->prepare("SELECT PRICE FROM CAR_DETAILS WHERE CAR_ID = :car_id LIMIT 1");
        $stmt->execute([':car_id' => $car_id]);
        $carRow = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$carRow) throw new Exception("Car not found.");

        $pickup  = new DateTime($pickup_date);
        $dropoff = new DateTime($dropoff_date);
        $interval = $pickup->diff($dropoff);
        $total_days = $interval->days ?: 1;
        $total_cost = $total_days * (float)$carRow['PRICE'];

        // Insert booking
        $stmt = $conn->prepare("INSERT INTO CUSTOMER_BOOKING_DETAILS 
            (USER_ID, CAR_ID, PICKUP_DATE, DROP_OFF_DATE, TRIP_DETAILS, STATUS, TOTAL_COST)
            VALUES (:user_id, :car_id, :pickup_date, :dropoff_date, :trip_details, 'PENDING', :total_cost)");
        $stmt->execute([
            ':user_id'      => $_SESSION['user_id'],
            ':car_id'       => $car_id,
            ':pickup_date'  => $pickup_date,
            ':dropoff_date' => $dropoff_date,
            ':trip_details' => $trip_details,
            ':total_cost'   => $total_cost
        ]);

        // Update car status
        $stmt = $conn->prepare("UPDATE CAR_DETAILS SET STATUS = 'RESERVED' WHERE CAR_ID = :car_id");
        $stmt->execute([':car_id' => $car_id]);

        $conn->commit();

        $booking_message = "<script>
Swal.fire({
    icon: 'success',
    title: 'Booking successful!',
    text: 'Go to payment module to secure your slot within the day.'
}).then(() => {
    // Redirect to current page once
    window.location.href = window.location.pathname;
});
</script>";
    } catch (Exception $e) {
        $conn->rollBack();
        $booking_message = "<script>Swal.fire({icon:'error',title:'Transaction failed',text:'" . addslashes($e->getMessage()) . "'});</script>";
    }
}
?>



<form method="POST" class="w-fit mx-auto bg-white p-8 rounded-xl shadow space-y-6">

    <h2 class="text-xl font-bold text-gray-800 mb-4">Book a Vehicle</h2>

    <!-- Trip Details -->
    <div>
        <label class="block mb-2 text-sm font-medium text-gray-700">Trip Details</label>
        <input
            value="<?php echo $sanitized_trip_details; ?>"
            type="text"
            name="trip_details"
            class="w-full p-2.5 border rounded">
        <?php
        echo $booking_input_message['trip_details'];
        ?>
    </div>

    <!-- Date Range Picker -->
    <div id="date-range-picker" date-rangepicker class="flex items-center">
        <!-- Pickup -->
        <input
            id="pickup_date"
            name="pickup_date"
            autocomplete="off"
            pattern="[0-9/]*"
            type="text"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dateInput"
            placeholder="Select pickup date">
        <span class="mx-4 text-black">to</span>
        <!-- Dropoff -->
        <input
            id="dropoff_date"
            name="dropoff_date"
            autocomplete="off"
            pattern="[0-9/]*"
            type="text"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dateInput"
            placeholder="Select drop-off date">


    </div>

    <p>
        <?= $booking_input_message['pickup_date'] ?? '' ?>
    </p>
    <p>
        <?= $booking_input_message['dropoff_date'] ?? '' ?>
    </p>

    <!-- Car Selection -->

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
                    <input type="radio" name="car_id" value="<?php echo $car['CAR_ID']; ?>">

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
    <p>
        <?php
        echo $booking_input_message['car_id'];

        ?>
    </p>
    <!-- Terms & Conditions -->
    <div class="bg-white rounded-xl  p-6">

        <div class="flex items-start space-x-3">
            <button id="checkbox2" type="button" class="mt-1 w-5 h-5 border-2 border-gray-300 rounded flex items-center justify-center hover:border-blue-400 transition-colors">
                <svg id="checkmark2" class="w-3 h-3 text-white hidden" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
            </button>
            <div>
                <p class="text-gray-700">
                    I agree to the
                    Terms & Conditions
                    of this rental agreement.
                </p>
                <p class="text-xs text-gray-500 mt-1">Click to read the full terms and conditions</p>
            </div>
        </div>
    </div>





    <!-- Modal Overlay -->
    <div id="modalOverlay" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden">
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h2 class="text-xl font-semibold text-gray-800">Terms & Conditions</h2>
                <button id="closeModal" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-6 overflow-y-auto max-h-[60vh]">
                <div class="space-y-4 text-sm text-gray-600">
                    <div class="p-4 bg-blue-50 rounded-lg border-l-4 border-blue-400">
                        <h4 class="font-semibold text-gray-800 mb-2">1. Package Exclusion </h4>
                        <p>The rental package does not include fuel, toll fees, or additional services during your booking period.</p>
                    </div>
                    <div class="p-4 bg-green-50 rounded-lg border-l-4 border-green-400">
                        <h4 class="font-semibold text-gray-800 mb-2">2. Additional Hours</h4>
                        <p>Any extra hours beyond the agreed rental period will incur additional fees according to our standard rate card.</p>
                    </div>
                    <div class="p-4 bg-purple-50 rounded-lg border-l-4 border-purple-400">
                        <h4 class="font-semibold text-gray-800 mb-2">3. Payment Methods</h4>
                        <p>We accept both cash payments and GCash digital transactions for your convenience and security.</p>
                    </div>
                    <div class="p-4 bg-yellow-50 rounded-lg border-l-4 border-yellow-400">
                        <h4 class="font-semibold text-gray-800 mb-2">4. No-Show Policy</h4>
                        <p>Failure to show up within 3 hours of the scheduled pickup time will result in automatic cancellation of your reservation.</p>
                    </div>
                    <div class="p-4 bg-red-50 rounded-lg border-l-4 border-red-400">
                        <h4 class="font-semibold text-gray-800 mb-2">5. Cleaning Fees</h4>
                        <p>A cleaning fee of PHP 500 will be charged if any mess or damage is caused by the client during the rental period.</p>
                    </div>
                    <div class="p-4 bg-orange-50 rounded-lg border-l-4 border-orange-400">
                        <h4 class="font-semibold text-gray-800 mb-2">6. Damage Liability</h4>
                        <p>The renter is fully responsible for any damages to the vehicle that occur during the rental period.</p>
                    </div>
                    <div class="p-4 bg-teal-50 rounded-lg border-l-4 border-teal-400">
                        <h4 class="font-semibold text-gray-800 mb-2">7. Identification Requirement</h4>
                        <p>A valid government-issued identification document must be provided before vehicle pickup.</p>
                    </div>
                </div>
            </div>
            <div class="p-6 border-t border-gray-200 flex justify-end space-x-3">
                <button id="declineModal" type="button" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                    Decline
                </button>
                <button id="acceptModal" type="button" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Accept Terms
                </button>
            </div>
        </div>
    </div>
    <button
        type="submit"
        id="customer_booking"
        name="customer_booking"
        class="w-full bg-orange-400 hover:bg-orange-500 text-white p-3 rounded-lg font-bold cursor-not-allowed opacity-50">
        Book Now
    </button>
</form>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php echo $summary_preview; ?>
<?php echo $booking_message; ?>