<?php
include "../../config/db.php";

// setting notification variables to inform the customers
$customer_input_message = [
    "email" => "",
    "password" => "",
    "repeat_password" => "",
    "gender" => "",
    "first_name" => "",
    "last_name" => "",
    "phone" => "",
    "address" => "",
];
// setting the var value as null as default to avoid error when asign as values in input fields
$customer_register_message =
    $sanitized_email =
    $sanitized_password =
    $sanitized_repeat_pass =
    $sanitized_gender =
    $sanitized_first_name =
    $sanitized_last_name =
    $sanitized_phone =
    $sanitized_address = "";


// if the btn triggered this code runs
if (isset($_POST['customerRegister'])) {

    // 1. Trim
    $email        = trim($_POST['email'] ?? '');
    $password     = trim($_POST['password'] ?? '');
    $repeat_pass  = trim($_POST['repeat_password'] ?? '');
    $gender       = trim($_POST['gender'] ?? '');
    $first_name   = trim($_POST['first_name'] ?? '');
    $last_name    = trim($_POST['last_name'] ?? '');
    $phone        = trim($_POST['phone'] ?? '');
    $address      = trim($_POST['address'] ?? '');

    // 2. Sanitize
    $sanitized_email       = filter_var($email, FILTER_SANITIZE_EMAIL);
    $sanitized_password    = htmlspecialchars($password, ENT_QUOTES, 'UTF-8');
    $sanitized_repeat_pass = htmlspecialchars($repeat_pass, ENT_QUOTES, 'UTF-8');
    $sanitized_gender      = htmlspecialchars($gender, ENT_QUOTES, 'UTF-8');
    $sanitized_first_name  = htmlspecialchars($first_name, ENT_QUOTES, 'UTF-8');
    $sanitized_last_name   = htmlspecialchars($last_name, ENT_QUOTES, 'UTF-8');
    $sanitized_phone       = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
    $sanitized_address     = htmlspecialchars($address, ENT_QUOTES, 'UTF-8');

    // 3. Validation
    // Email
    if (empty($sanitized_email)) {
        $customer_input_message['email'] = "<p class='text-red-500 text-sm'>Email is required.</p>";
    } elseif (!filter_var($sanitized_email, FILTER_VALIDATE_EMAIL)) {
        $customer_input_message['email'] = "<p class='text-red-500 text-sm'>Please enter a valid email format.</p>";
    } else {
        $select_statement_existing_email = "SELECT COUNT(*) 
                                            FROM customer_details   
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
    } else {
        $select_statement_existing_phone = "SELECT COUNT(*) 
                                            FROM customer_details   
                                            WHERE phone = :phone;";

        $stmt = $conn->prepare($select_statement_existing_phone);
        $stmt->execute([':phone' => $sanitized_phone]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $customer_input_message['phone'] = "<p class='text-red-500 text-sm'>Phone number already registered try a new one.</p>";
        }
    }

    // Address
    if (empty($sanitized_address)) {
        $customer_input_message['address'] = "<p class='text-red-500 text-sm'>Address is required.</p>";
    }


    // 4 checking for errors in inputs

    $hasErrors = false;
    foreach ($customer_input_message as $msg) {
        if (!empty($msg)) {
            $hasErrors = true;
            $customer_register_message = "
            <script>
                Swal.fire({
                icon: 'error',
                title: 'Error in input fields!',
                text: 'Please follow the correct format!',
                });
            </script>";
        }
    }

    // 5 insertion of data to database
    if (!$hasErrors) {
        try {
            $conn->beginTransaction();

            // first data insert customer_details table
            $insertStatementCustomerDetails = "INSERT INTO customer_details(first_name, last_name, gender, email, phone, address)
                                               VALUES(:first_name, :last_name, :gender, :email, :phone, :address);";
            $stmt = $conn->prepare($insertStatementCustomerDetails);
            $stmt->execute(
                [
                    ':first_name' => $sanitized_first_name,
                    ':last_name' => $sanitized_last_name,
                    ':gender' => $sanitized_gender,
                    ':email' => $sanitized_email,
                    ':phone' => $sanitized_phone,
                    ':address' => $sanitized_address
                ]
            );
            $customer_ID = $conn->lastInsertId(); // getting the last customer ID added in the customer_detail table (PK) to link the customer_account table

            // second data insert customer_accounts table, if first transaction is successful 

            $hashed_password = password_hash($sanitized_password, PASSWORD_ARGON2ID); // hashing password before insertion
            $insertStatementCustomerAccount = "INSERT INTO customer_account(customer_id, password)
                                               VALUE(:customer_id, :password)";
            $stmt = $conn->prepare($insertStatementCustomerAccount);
            $stmt->execute(
                [
                    ':customer_id' => $customer_ID,
                    ':password' => $hashed_password
                ]
            );


            // if there no error the rest of the transaction is saved
            $conn->commit();
            $customer_register_message = "
               <script>
                    Swal.fire({
                    icon: 'success',
                    title: 'Your account is registered!',
                    showConfirmButton: false,
                    timer: 1500
                    });
               </script>";
        } catch (PDOException $e) {
            // if there an error the rest of the transaction is reverted into initial value like nothing happen
            $conn->rollBack();
            echo "Database error: " . $e->getMessage();
        }
    }
}
?>


<form method="POST"
    class="max-w-md w-full mx-auto bg-white/80 backdrop-blur-md p-8 rounded-xl shadow-lg border border-[#df927f] animate-popup">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php
echo $customer_register_message;
?>