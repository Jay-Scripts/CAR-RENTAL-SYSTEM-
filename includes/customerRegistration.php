<?php
include "../../config/db.php";

$customer_input_message = [
    "email" => "",
    "password" => "",
    "repeat_password" => "",
    "gender" => "",
    "first_name" => "",
    "last_name" => "",
    "phone" => "",
    "address" => "",
    "birthdate" => "",
    "id_file" => ""
];

$customer_register_message =
    $sanitized_email =
    $sanitized_password =
    $sanitized_repeat_pass =
    $sanitized_gender =
    $sanitized_first_name =
    $sanitized_last_name =
    $sanitized_phone =
    $sanitized_address =
    $sanitized_birthdate =
    $idPath = "";

if (isset($_POST['customerRegister'])) {
    // 1. Trim inputs
    $email        = trim($_POST['email'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $repeat_pass  = trim($_POST['repeat_password'] ?? '');
    $gender       = trim($_POST['gender'] ?? '');
    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $address      = trim($_POST['address'] ?? '');
    $birthdate    = trim($_POST['birthdate'] ?? '');

    // 2. Sanitize
    $sanitized_email       = filter_var($email, FILTER_SANITIZE_EMAIL);
    $sanitized_password    = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
    $sanitized_repeat_pass = htmlspecialchars($repeat_pass, ENT_QUOTES, 'UTF-8');
    $sanitized_gender      = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');
    $sanitized_first_name  = htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8');
    $sanitized_last_name   = htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8');
    $sanitized_phone       = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
    $sanitized_address     = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');
    $sanitized_birthdate   = htmlspecialchars($birthdate, ENT_QUOTES, 'UTF-8');

    // 3. Validation
    // Data Privacy checkbox validation
    if (!isset($_POST['privacy_check'])) {
        $customer_input_message['privacy_check'] = "<p class='text-red-500 text-sm'>You must agree to the Data Privacy Policy to register.</p>";
    }

    // Email
    if (empty($sanitized_email)) {
        $customer_input_message['email'] = "<p class='text-red-500 text-sm'>Email is required.</p>";
    } elseif (!filter_var($sanitized_email, FILTER_VALIDATE_EMAIL)) {
        $customer_input_message['email'] = "<p class='text-red-500 text-sm'>Please enter a valid email format.</p>";
    } else {
        $select_statement_existing_email = "SELECT COUNT(*) 
                                            FROM user_details   
                                            WHERE email = :email;";

        $stmt = $conn->prepare($select_statement_existing_email);
        $stmt->execute([':email' => $sanitized_email]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $customer_input_message['email'] = "<p class='text-red-500 text-sm'>Email already registered try a new one.</p>";
        }
    }

    // Password
    if (empty($sanitized_password)) {
        $customer_input_message['password'] = "<p class='text-red-500 text-sm'>Password is required.</p>";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $sanitized_password)) {
        $customer_input_message['password'] = "<p class='text-red-500 text-sm'>Password must be at least 8 characters, with upper, lower, number, and special char.</p>";
    }


    // Repeat password
    if (empty($sanitized_repeat_pass)) {
        $customer_input_message['repeat_password'] = "<p class='text-red-500 text-sm'>Password is required.</p>";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/", $sanitized_repeat_pass)) {
        $customer_input_message['repeat_password'] = "<p class='text-red-500 text-sm'>Password must be at least 8 characters, with upper, lower, number, and special char.</p>";
    } elseif ($sanitized_password  !== $sanitized_repeat_pass) {
        $customer_input_message['repeat_password'] = "<p class='text-red-500 text-sm'>Passwords do not match.</p>";
    }

    // Gender
    $allowed_genders = ["male", "female"];
    if (!in_array($sanitized_gender, $allowed_genders)) {
        $customer_input_message['gender'] = "<p class='text-red-500 text-sm'>Invalid gender value.</p>";
    }


    // First name
    if (empty($sanitized_first_name)) {
        $customer_input_message['first_name'] = "<p class='text-red-500 text-sm'>First name is required.</p>";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $sanitized_first_name)) {
        $customer_input_message['first_name'] = "<p class='text-red-500 text-sm'>First name must only contains letter.</p>";
    }

    // Last name
    if (empty($sanitized_last_name)) {
        $customer_input_message['last_name'] = "<p class='text-red-500 text-sm'>Last name is required.</p>";
    } elseif (!preg_match("/^[a-zA-Z]+$/", $sanitized_last_name)) {
        $customer_input_message['last_name'] = "<p class='text-red-500 text-sm'>Last name must only contains letter.</p>";
    }

    // Phone (basic check, adjust regex to your rules)
    if (empty($sanitized_phone)) {
        $customer_input_message['phone'] = "<p class='text-red-500 text-sm'>Phone number is required.</p>";
    } elseif (!preg_match("/^\d{4}-\d{3}-\d{4}$/", $sanitized_phone)) {
        $customer_input_message['phone'] = "<p class='text-red-500 text-sm'>Enter a valid phone number (11 digits).</p>";
    }
    // Birthdate validation (21+)
    if (empty($sanitized_birthdate)) {
        $customer_input_message['birthdate'] = "<p class='text-red-500 text-sm'>Birthdate is required.</p>";
    } elseif (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $sanitized_birthdate)) {
        $customer_input_message['birthdate'] = "<p class='text-red-500 text-sm'>Invalid birthdate format..</p>";
    } else {
        $birthDateTime = new DateTime($sanitized_birthdate);
        $today = new DateTime();
        $age = $today->diff($birthDateTime)->y;
        if ($age < 21) {
            $customer_input_message['birthdate'] =
                "<p class='text-red-500 text-sm'>You must be at least 21 years old to register.</p>";
        }
    }

    // ID upload validation
    if (isset($_FILES['id_file']) && $_FILES['id_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES['id_file']['tmp_name'];
        $fileName = basename($_FILES['id_file']['name']);
        $fileExt  = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        $allowedExt = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExt, $allowedExt)) {
            $customer_input_message['id_file'] =  "<p class='text-red-500 text-sm'>Invalid file type. Only JPG, JPEG, PNG, GIF allowed.</p>";
        } else {
            $newFileName = "id_" . time() . "_" . $fileName;
            $uploadDir = $_SERVER['DOCUMENT_ROOT'] . "/uploads/ids/";
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $uploadPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmp, $uploadPath)) {
                $idPath = "/uploads/ids/" . $newFileName;
            } else {
                $customer_input_message['id_file'] = "<p class='text-red-500 text-sm'>Failed to upload ID. Check folder permissions.</p>";
            }
        }
    } else {
        $customer_input_message['id_file'] =
            "<p class='text-red-500 text-sm'>ID upload is required.</p>";
    }

    // Check for errors
    $errors = [];
    foreach ($customer_input_message as $msg) {
        if (!empty($msg)) {
            $errors[] = $msg;
        }
    }

    // If errors, show SweetAlert
    if (!empty($errors)) {
        $errorText = implode("<br>", $errors);
        $customer_register_message = "<script>
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                html: '$errorText',
            });
        </script>";
    } else {
        // Insert into DB
        try {
            $conn->beginTransaction();

            $stmt = $conn->prepare("INSERT INTO user_details
                (first_name, last_name, gender, email, phone, address, birthdate, id_path)
                VALUES (:first_name, :last_name, :gender, :email, :phone, :address, :birthdate, :id_path)");
            $stmt->execute([
                ':first_name' => $sanitized_first_name,
                ':last_name'  => $sanitized_last_name,
                ':gender'     => $sanitized_gender,
                ':email'      => $sanitized_email,
                ':phone'      => $sanitized_phone,
                ':address'    => $sanitized_address,
                ':birthdate'  => $sanitized_birthdate,
                ':id_path'    => $idPath
            ]);

            $customer_ID = $conn->lastInsertId();
            $hashed_password = password_hash($sanitized_password, PASSWORD_ARGON2ID);

            $stmt = $conn->prepare("INSERT INTO user_account(user_id, password) VALUES (:customer_id, :password)");
            $stmt->execute([
                ':customer_id' => $customer_ID,
                ':password'    => $hashed_password
            ]);

            $conn->commit();

            $customer_register_message = "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Your account is registered!',
                    showConfirmButton: false,
                    timer: 2000
                });
                setTimeout(() => { window.location.href = '../unRegistedUserModule/loginPage.php'; }, 2000);
            </script>";
        } catch (PDOException $e) {
            $conn->rollBack();
            $errorMsg = htmlspecialchars($e->getMessage());
            $customer_register_message = "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Database Error!',
                    html: '$errorMsg'
                });
            </script>";
        }
    }
}
?>




<form method="POST" enctype="multipart/form-data"
    class="max-w-md w-fit h-fit mx-auto bg-white/80 backdrop-blur-md p-8 rounded-xl shadow-lg border border-[#df927f] animate-popup">
    <h2 class="text-2xl font-bold text-[#df927f] mb-6 text-center">
        Create Your Account
    </h2>

    <!-- Email -->
    <div class="mb-5">
        <label
            for="email"
            class="block mb-2 text-sm font-medium text-[#df927f]">
            Email address
        </label>
        <input

            value="<?php echo htmlspecialchars($sanitized_email) ?>"
            type="email"
            id="email"
            name="email"
            placeholder="your@email.com"
            class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5 dark:bg-gray-700 dark:border-[#df927f] dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#df927f] dark:focus:border-[#df927f] dark:shadow-xs-light" />
        <?php
        echo $customer_input_message['email'];
        ?>
    </div>

    <!-- Password -->
    <div class="mb-5">
        <label
            for="password"
            class="block mb-2 text-sm font-medium text-[#df927f]">
            Password
        </label>
        <input
            type="password"
            id="password"
            name="password"
            class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5 dark:bg-gray-700 dark:border-[#df927f] dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#df927f] dark:focus:border-[#df927f] dark:shadow-xs-light" />
        <?php
        echo $customer_input_message['password'];
        ?>
        <label class="flex items-center gap-2 mt-2 text-sm text-gray-600">
            <input type="checkbox" onclick="document.getElementById('password').type = this.checked ? 'text' : 'password'">
            Show Password
        </label>

    </div>

    <!-- Confirm Password -->
    <div class="mb-5">
        <label
            for="repeat_password"
            class="block mb-2 text-sm font-medium text-[#df927f]">
            Confirm Password
        </label>
        <input
            type="password"
            id="repeat_password"
            name="repeat_password"
            class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5 dark:bg-gray-700 dark:border-[#df927f] dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#df927f] dark:focus:border-[#df927f] dark:shadow-xs-light" />
        <?php
        echo $customer_input_message['repeat_password'];
        ?>
        <label class="flex items-center gap-2 mt-2 text-sm text-gray-600">
            <input type="checkbox" onclick="document.getElementById('repeat_password').type = this.checked ? 'text' : 'password'">
            Show Password
        </label>

    </div>
    <!-- Gender -->
    <div class="mb-5">
        <label
            for="gender"
            class="block mb-2 text-sm font-medium text-[#df927f]">
            Gender
        </label>

        <select
            name="gender"
            class="text-center shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5 dark:bg-gray-700 dark:border-[#df927f] dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#df927f] dark:focus:border-[#df927f] dark:shadow-xs-light">
            <option value="" disabled hidden <?php if (!$sanitized_gender) echo "selected"; ?>>-- Gender --</option>
            <option value="male" <?php if ($sanitized_gender === "male") echo "selected"; ?>>Male</option>
            <option value="female" <?php if ($sanitized_gender === "female") echo "selected"; ?>>Female</option>
        </select>
        <?php
        echo $customer_input_message['gender'];
        ?>
    </div>

    <!-- First + Last Name -->
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="mb-5">
            <label
                for="first_name"
                class="block mb-2 text-sm font-medium text-[#df927f]">
                First Name
            </label>
            <input
                value="<?php echo htmlspecialchars($sanitized_first_name) ?>"
                type="text"
                id="first_name"
                name="first_name"
                class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5 dark:bg-gray-700 dark:border-[#df927f] dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#df927f] dark:focus:border-[#df927f] dark:shadow-xs-light" />
            <?php
            echo $customer_input_message['first_name'];
            ?>
        </div>
        <div class="mb-5">
            <label
                for="last_name"
                class="block mb-2 text-sm font-medium text-[#df927f]">
                Last Name
            </label>
            <input
                value="<?php echo htmlspecialchars($sanitized_last_name) ?>"
                type="text"
                id="last_name"
                name="last_name"
                class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5 dark:bg-gray-700 dark:border-[#df927f] dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#df927f] dark:focus:border-[#df927f] dark:shadow-xs-light" />
            <?php
            echo $customer_input_message['last_name'];
            ?>
        </div>
    </div>

    <!-- Phone + Company -->
    <div class="grid md:grid-cols-2 md:gap-6">
        <div class="mb-5">
            <label
                for="phone"
                class="block mb-2 text-sm font-medium text-[#df927f]">
                Phone (0123-456-7890)
            </label>
            <input
                value="<?php echo htmlspecialchars($sanitized_phone) ?>"
                type="tel"
                id="phone"
                name="phone"
                pattern="[0-9]{4}-[0-9]{3}-[0-9]{4}"
                class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5 dark:bg-gray-700 dark:border-[#df927f] dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#df927f] dark:focus:border-[#df927f] dark:shadow-xs-light" />
            <?php
            echo $customer_input_message['phone'];
            ?>
        </div>
        <div class="mb-5">
            <label
                for="address"
                class="block mb-2 text-sm font-medium text-[#df927f]">
                Address
            </label>
            <input
                value="<?php echo htmlspecialchars($sanitized_address) ?>"
                type="text"
                id="address"
                name="address"
                class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5 dark:bg-gray-700 dark:border-[#df927f] dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#df927f] dark:focus:border-[#df927f] dark:shadow-xs-light" />
            <?php
            echo $customer_input_message['address'];
            ?>
        </div>
    </div>
    <!-- Birthdate -->
    <div class="mb-5">
        <label for="birthdate" class="block mb-2 text-sm font-medium text-[#df927f]">
            Birthdate
        </label>
        <input
            value="<?php echo htmlspecialchars($sanitized_birthdate ?? '') ?>"
            type="date"
            id="birthdate"
            name="birthdate"
            class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5 dark:bg-gray-700 dark:border-[#df927f] dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#df927f] dark:focus:border-[#df927f] " />
        <?php echo $customer_input_message['birthdate'] ?? ''; ?>
    </div>

    <!-- Upload ID -->
    <div class="mb-5">
        <label for="id_file" class="block mb-2 text-sm font-medium text-[#df927f]">
            Upload Valid ID
        </label>
        <input
            type="file"
            id="id_file"
            name="id_file"
            accept=".jpg,.jpeg,.png,.gif"
            class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5 dark:bg-gray-700 dark:border-[#df927f] dark:placeholder-gray-400 dark:text-white dark:focus:ring-[#df927f] dark:focus:border-[#df927f] " />
        <?php echo $customer_input_message['id_file'] ?? ''; ?>
    </div>

    <div class="mb-5">
        <button type="button" id="openPrivacyModal" class="w-full text-white bg-[#df927f] hover:bg-[#c97c65] font-medium rounded-lg text-sm px-5 py-2.5">
            View & Agree to Data Privacy Policy
        </button>
        <?php
        if (!empty($customer_input_message['privacy_check'])) {
            echo $customer_input_message['privacy_check'];
        }
        ?>
    </div>


    <!-- Submit Button -->
    <button
        type="submit"
        name="customerRegister"
        class="w-full text-white bg-[#df927f] hover:bg-[#c97c65] focus:ring-4 focus:outline-none focus:ring-[#df927f]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition">
        Register
    </button>

    <!-- Already have an account -->
    <p class="mt-6 text-sm text-center text-[#df927f]">
        Already have an account?
        <a
            href="loginPage.php"
            class="text-[#df927f] hover:underline font-medium">Log-in</a>
    </p>
</form>

<!-- Data Privacy Modal -->
<div id="privacyModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full p-6 relative">
        <h2 class="text-xl font-bold text-[#df927f] mb-4">Data Privacy Statement</h2>
        <div class="max-h-64 overflow-y-auto text-gray-700 text-sm mb-4 space-y-3">
            <p>At Car Rental, we value your privacy and are committed to protecting your personal information in accordance with the Data Privacy Act of 2012 (Republic Act No. 10173).</p>

            <p>By creating an account or logging in, you consent to the collection, use, and processing of your personal data for legitimate business purposes related to our car rental services.</p>

            <p><strong>Information We Collect:</strong></p>
            <ul class="list-disc list-inside ml-4">
                <li>Full Name</li>
                <li>Email Address</li>
                <li>Contact Number</li>
                <li>Valid ID and Driver’s License (for verification purposes)</li>
                <li>Payment and Transaction Details</li>
            </ul>

            <p><strong>Purpose of Data Collection:</strong></p>
            <ul class="list-disc list-inside ml-4">
                <li>To verify your identity and process your car rental bookings.</li>
                <li>To communicate updates regarding your reservations, payments, and rental status.</li>
                <li>To maintain accurate transaction and booking records.</li>
                <li>To improve our services and ensure system security.</li>
            </ul>

            <p><strong>Data Protection and Security:</strong> All personal data provided is securely stored and accessed only by authorized personnel. Technical and organizational measures are implemented to prevent unauthorized access, disclosure, alteration, or misuse of your information.</p>

            <p><strong>Data Sharing and Retention:</strong> We do not share your information with third parties without your consent, unless required by law. Your data will be retained only as long as necessary for the stated purposes or as required by regulations.</p>

            <p><strong>Your Rights:</strong></p>
            <ul class="list-disc list-inside ml-4">
                <li>Access and correct your personal data.</li>
                <li>Withdraw consent for data processing.</li>
                <li>Request deletion of your account and associated information.</li>
            </ul>

            <p>If you have any questions or concerns regarding your data privacy, you may contact us at <a href="mailto:carrentalsystem2025@gmail.com" class="underline text-[#df927f]">carrentalsystem2025@gmail.com</a>.</p>

            <p>By proceeding with registration or login, you confirm that you have read, understood, and agreed to this Data Privacy Statement.</p>
        </div>

        <label class="flex items-center gap-2 text-sm mb-4">
            <input type="checkbox" id="privacyCheckbox" class="mt-1">
            I have read and agree to the Data Privacy Policy
        </label>

        <div class="flex justify-end gap-2">
            <button id="privacyCancel" class="px-4 py-2 rounded-lg border border-gray-300 hover:bg-gray-100">Cancel</button>
            <button id="privacyAgree" class="px-4 py-2 rounded-lg bg-[#df927f] text-white hover:bg-[#c97c65]">Agree</button>
        </div>
    </div>
</div>


<!-- SweetAlert2 CSS (optional for styling) -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<p><?php
    if (!empty($customer_register_message)) {
        echo $customer_register_message;
    }

    ?></p>

<script>
    // Elements
    const modal = document.getElementById('privacyModal');
    const openBtn = document.getElementById('openPrivacyModal');
    const cancelBtn = document.getElementById('privacyCancel');
    const agreeBtn = document.getElementById('privacyAgree');
    const checkbox = document.getElementById('privacyCheckbox');

    // Open modal
    openBtn.addEventListener('click', () => {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    });

    // Close modal
    cancelBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    });

    // Agree & check the checkbox
    agreeBtn.addEventListener('click', () => {
        if (checkbox.checked) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');

            // Add a hidden input to the form so PHP can validate it
            if (!document.getElementById('privacyHidden')) {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'privacy_check';
                hiddenInput.id = 'privacyHidden';
                hiddenInput.value = '1';
                document.querySelector('form').appendChild(hiddenInput);
            }

            // Optional: update button text
            openBtn.textContent = "Agreed";
            openBtn.disabled = true;
            openBtn.classList.add('bg-green-500', 'cursor-not-allowed');
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Oops!',
                text: 'Please check the box to agree before continuing.'
            });
        }
    });
</script>