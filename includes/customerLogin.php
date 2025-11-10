<?php
session_start();
include "../../config/db.php";

// Notification messages
$login_input_message = [
    "email" => "",
    "password" => ""
];

$sanitized_email = $sanitized_password = "";
$login_message = "";

// Run only if form submitted
if (isset($_POST['customerLogin'])) {

    // 1. Trim inputs
    $customer_email = trim($_POST['email'] ?? '');
    $customer_password = trim($_POST['password'] ?? '');

    // 2. Sanitize inputs
    $sanitized_email = htmlspecialchars($customer_email, ENT_QUOTES, 'UTF-8');
    $sanitized_password = htmlspecialchars($customer_password, ENT_QUOTES, 'UTF-8');

    // 3. Validation
    if (empty($sanitized_email)) {
        $login_input_message['email'] = "<p class='text-red-500 text-sm'>Email is required.</p>";
    } elseif (!filter_var($sanitized_email, FILTER_VALIDATE_EMAIL)) {
        $login_input_message['email'] = "<p class='text-red-500 text-sm'>Please enter a valid email format.</p>";
    }

    if (empty($sanitized_password)) {
        $login_input_message['password'] = "<p class='text-red-500 text-sm'>Password is required.</p>";
    }

    // 4. Check DB if no errors
    $hasErrors = !empty($login_input_message['email']) || !empty($login_input_message['password']);

    // 5. cheking if the credential is valid then store the user first name in session 
    if (!$hasErrors) {
        try {
            $select_statement_existing_customer = "
    SELECT ua.user_id, ua.password, ua.status, ud.role, ud.first_name 
    FROM user_account ua
    JOIN user_details ud ON ua.user_id = ud.user_id
    WHERE ud.email = :email
;";
            $stmt = $conn->prepare($select_statement_existing_customer);
            $stmt->execute([':email' => $sanitized_email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($sanitized_password, $user['password'])) {
                if ($user['status'] !== 'ACTIVE') {
                    // Account is inactive
                    $login_message = "
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Account Inactive',
                text: 'Your account is not active. Please contact support.'
            });
        </script>";
                } elseif ($user['role'] === 'customer') {
                    // Store first name in session
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['customer_name'] = $user['first_name'];

                    // SweetAlert + redirect
                    $login_message = "
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Welcome back, {$user['first_name']}!',
                showConfirmButton: false,
                timer: 1500
            }).then(() => {
                window.location.href = '../customerModule/customerDashboard.php';
            });
        </script>";
                } else {
                    // Other roles or invalid role
                    $login_message = "
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid credentials',
                text: 'Email or password is incorrect.'
            });
        </script>";
                }
            } else {
                $login_message = "
    <script>
        Swal.fire({
            icon: 'error',
            title: 'Invalid credentials',
            text: 'Email or password is incorrect.'
        });
    </script>";
            }
        } catch (PDOException $e) {
            $login_message = "<p class='text-red-500 text-sm'>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }
}
?>

<!-- HTML Form -->
<form method="POST" class="max-w-md w-full mx-auto bg-white/80 backdrop-blur-md p-8 rounded-xl shadow-lg border border-[#df927f]">
    <h2 class="text-2xl font-bold text-[#df927f] mb-6 text-center">Log-in</h2>

    <!-- Email -->
    <div class="mb-5">
        <label for="email" class="block mb-2 text-sm font-medium text-[#df927f]">Email address</label>
        <input
            value="<?php echo htmlspecialchars($sanitized_email); ?>"
            type="email" id="email" name="email"
            placeholder="your@email.com"
            class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5" />
        <?php echo $login_input_message['email']; ?>
    </div>

    <!-- Password -->
    <div class="mb-5">
        <label for="password" class="block mb-2 text-sm font-medium text-[#df927f]">Password</label>
        <input
            type="password" id="password" name="password"
            class="shadow-xs bg-gray-50 border border-[#df927f] text-gray-900 text-sm rounded-lg focus:ring-[#df927f] focus:border-[#df927f] block w-full p-2.5" />
        <?php echo $login_input_message['password']; ?>
        <label class="flex items-center gap-2 mt-2 text-sm text-gray-600">
            <input type="checkbox" onclick="document.getElementById('password').type = this.checked ? 'text' : 'password'">
            Show Password
        </label>

    </div>

    <!-- Submit -->
    <button type="submit" name="customerLogin" class="w-full text-white bg-[#df927f] hover:bg-[#c97c65] focus:ring-4 focus:outline-none focus:ring-[#df927f]/50 font-medium rounded-lg text-sm px-5 py-2.5 text-center transition">
        Log-in
    </button>

    <!-- Register link -->
    <p class="mt-6 text-sm text-center text-[#df927f]">
        Don’t have an account?
        <a href="registerPage.php" class="text-[#df927f] hover:underline font-medium">Register</a>
    </p>
</form>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php echo $login_message; ?>